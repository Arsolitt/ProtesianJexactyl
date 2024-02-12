<?php

namespace Jexactyl\Transformers\Api\Client;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Extensions\Spatie\Fractalistic\Fractal;
use Jexactyl\Models\User;
use Jexactyl\Models\Server;
use Webmozart\Assert\Assert;
use Jexactyl\Transformers\Api\Application\BaseTransformer as BaseApplicationTransformer;

abstract class BaseClientTransformer extends BaseApplicationTransformer
{

    protected SettingsRepositoryInterface $settings;
    public function __construct()
    {
        parent::__construct();
        Container::getInstance()->call([$this, 'loadDependencies']);
    }

    public function loadDependencies(SettingsRepositoryInterface $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Return the user model of the user requesting this transformation.
     */
    public function getUser(): User
    {
        return $this->request->user();
    }

    /**
     * Determine if the API key loaded onto the transformer has permission
     * to access a different resource. This is used when including other
     * models on a transformation request.
     *
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    protected function authorize(string $ability, Server $server = null): bool
    {
        Assert::isInstanceOf($server, Server::class);

        return $this->request->user()->can($ability, [$server]);
    }

    /**
     * {@inheritDoc}
     */
    protected function makeTransformer(string $abstract)
    {
        Assert::subclassOf($abstract, self::class);

        return parent::makeTransformer($abstract);
    }
}
