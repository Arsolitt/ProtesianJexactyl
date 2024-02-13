<?php

namespace Jexactyl\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Cache;
use Znck\Eloquent\Traits\BelongsToThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Jexactyl\Exceptions\Http\Server\ServerStateConflictException;

class Server extends Model
{
    use BelongsToThrough;
    use Notifiable;

    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    public const string RESOURCE_NAME = 'server';
    public const string STATUS_INSTALLING = 'installing';
    public const string STATUS_INSTALL_FAILED = 'install_failed';
    public const string STATUS_REINSTALL_FAILED = 'reinstall_failed';
    public const string STATUS_SUSPENDED = 'suspended';
    public const string STATUS_RESTORING_BACKUP = 'restoring_backup';

    /**
     * The table associated with the model.
     */
    protected $table = 'servers';

    /**
     * Default values when creating the model. We want to switch to disabling OOM killer
     * on server instances unless the user specifies otherwise in the request.
     */
    protected $attributes = [
        'status' => self::STATUS_INSTALLING,
        'oom_disabled' => true,
        'installed_at' => null,
    ];

    /**
     * The default relationships to load for all server models.
     */
    protected $with = ['allocation'];

    /**
     * Fields that are not mass assignable.
     */
    protected $guarded = ['id', self::CREATED_AT, self::UPDATED_AT, 'deleted_at', 'installed_at'];

    public static array $validationRules = [
        'external_id' => 'sometimes|nullable|string|between:1,191|unique:servers',
        'owner_id' => 'required|integer|exists:users,id',
        'name' => 'required|string|min:1|max:191',
        'node_id' => 'required|exists:nodes,id',
        'renewable' => 'sometimes|boolean',
        'renewal' => 'sometimes|integer',
        'bg' => 'nullable|string',
        'description' => 'string',
        'status' => 'nullable|string',
        'memory' => 'required|numeric|min:0',
        'swap' => 'required|numeric|min:-1',
        'io' => 'required|numeric|between:10,1000',
        'cpu' => 'required|numeric|min:0',
        'threads' => 'nullable|regex:/^[0-9-,]+$/',
        'oom_disabled' => 'sometimes|boolean',
        'disk' => 'required|numeric|min:0',
        'allocation_id' => 'required|bail|unique:servers|exists:allocations,id',
        'nest_id' => 'required|exists:nests,id',
        'egg_id' => 'required|exists:eggs,id',
        'startup' => 'required|string',
        'skip_scripts' => 'sometimes|boolean',
        'image' => 'required|string|max:191',
        'database_limit' => 'present|nullable|integer|min:0',
        'allocation_limit' => 'sometimes|nullable|integer|min:0',
        'backup_limit' => 'present|nullable|integer|min:0',
        'monthly_price' => 'required|numeric|min:0',
    ];

    /**
     * Cast values to correct type.
     */
    protected $casts = [
        'node_id' => 'integer',
        'renewable' => 'boolean',
        'renewal' => 'integer',
        'bg' => 'string',
        'skip_scripts' => 'boolean',
        'owner_id' => 'integer',
        'memory' => 'integer',
        'swap' => 'integer',
        'disk' => 'integer',
        'io' => 'integer',
        'cpu' => 'integer',
        'oom_disabled' => 'boolean',
        'allocation_id' => 'integer',
        'nest_id' => 'integer',
        'egg_id' => 'integer',
        'database_limit' => 'integer',
        'allocation_limit' => 'integer',
        'backup_limit' => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        'deleted_at' => 'datetime',
        'installed_at' => 'datetime',
    ];

    /**
     * Returns the format for server allocations when communicating with the Daemon.
     */
    public function getAllocationMappings(): array
    {
        return $this->allocations->where('node_id', $this->node_id)->groupBy('ip')->map(function ($item) {
            return $item->pluck('port');
        })->toArray();
    }

    public function isInstalled(): bool
    {
        return $this->status !== self::STATUS_INSTALLING && $this->status !== self::STATUS_INSTALL_FAILED;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Gets the user who owns the server.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Gets the subusers associated with a server.
     */
    public function subusers(): HasMany
    {
        return $this->hasMany(Subuser::class, 'server_id', 'id');
    }

    /**
     * Gets the default allocation for a server.
     */
    public function allocation(): HasOne
    {
        return $this->hasOne(Allocation::class, 'id', 'allocation_id');
    }

    /**
     * Gets all allocations associated with this server.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class, 'server_id');
    }

    /**
     * Gets information for the nest associated with this server.
     */
    public function nest(): BelongsTo
    {
        return $this->belongsTo(Nest::class);
    }

    /**
     * Gets information for the egg associated with this server.
     */
    public function egg(): HasOne
    {
        return $this->hasOne(Egg::class, 'id', 'egg_id');
    }

    /**
     * Gets information for the service variables associated with this server.
     */
    public function variables(): HasMany
    {
        return $this->hasMany(EggVariable::class, 'egg_id', 'egg_id')
            ->select(['egg_variables.*', 'server_variables.variable_value as server_value'])
            ->leftJoin('server_variables', function (JoinClause $join) {
                // Don't forget to join against the server ID as well since the way we're using this relationship
                // would actually return all the variables and their values for _all_ servers using that egg,
                // rather than only the server for this model.
                //
                // @see https://github.com/pterodactyl/panel/issues/2250
                $join->on('server_variables.variable_id', 'egg_variables.id')
                    ->where('server_variables.server_id', $this->id);
            });
    }

    /**
     * Gets information for the node associated with this server.
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }

    /**
     * Gets information for the tasks associated with this server.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Gets all databases associated with a server.
     */
    public function databases(): HasMany
    {
        return $this->hasMany(Database::class);
    }

    /**
     * Returns the location that a server belongs to.
     *
     * @throws \Exception
     */
    public function location(): \Znck\Eloquent\Relations\BelongsToThrough
    {
        return $this->belongsToThrough(Location::class, Node::class);
    }

    /**
     * Returns the associated server transfer.
     */
    public function transfer(): HasOne
    {
        return $this->hasOne(ServerTransfer::class)->whereNull('successful')->orderByDesc('id');
    }

    public function backups(): HasMany
    {
        return $this->hasMany(Backup::class);
    }

    /**
     * Returns all mounts that have this server has mounted.
     */
    public function mounts(): HasManyThrough
    {
        return $this->hasManyThrough(Mount::class, MountServer::class, 'server_id', 'id', 'id', 'mount_id');
    }

    /**
     * Returns all of the activity log entries where the server is the subject.
     */
    public function activity(): MorphToMany
    {
        return $this->morphToMany(ActivityLog::class, 'subject', 'activity_log_subjects');
    }

    /**
     * Checks if the server is currently in a user-accessible state. If not, an
     * exception is raised. This should be called whenever something needs to make
     * sure the server is not in a weird state that should block user access.
     *
     * @throws \Jexactyl\Exceptions\Http\Server\ServerStateConflictException
     */
    public function validateCurrentState()
    {
        if (
            $this->isSuspended() ||
            $this->node->isUnderMaintenance() ||
            !$this->isInstalled() ||
            $this->status === self::STATUS_RESTORING_BACKUP ||
            !is_null($this->transfer)
        ) {
            throw new ServerStateConflictException($this);
        }
    }

    /**
     * Checks if the server is currently in a transferable state. If not, an
     * exception is raised. This should be called whenever something needs to make
     * sure the server is able to be transferred and is not currently being transferred
     * or installed.
     */
    public function validateTransferState(): void
    {
        if (
            !$this->isInstalled() ||
            $this->status === self::STATUS_RESTORING_BACKUP ||
            !is_null($this->transfer)
        ) {
            throw new ServerStateConflictException($this);
        }
    }

    public function hourlyPrice(): float|int
    {
        return Cache::get('server_hourly_price_' . $this->id, function () {
            Cache::set('server_hourly_price_' . $this->id, $this->monthlyPrice() / 30 / 24);
            return $this->monthlyPrice() / 30 / 24;
        });
    }

    public function monthlyPrice(): float|int
    {
        return Cache::get('server_monthly_price_' . $this->id, function () {
            Cache::set('server_monthly_price_' . $this->id, $this->monthly_price);
            return $this->monthly_price;
        });
    }

    protected function updateMonthlyPrice(){
        Cache::forget('server_monthly_price_' . $this->id);
        Cache::forget('server_hourly_price_' . $this->id);
    }
}
