<?php

namespace Jexactyl\Http\Controllers\Admin\Jexactyl;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Extensions\Spatie\Fractalistic\Fractal;
use Jexactyl\Services\Helpers\SoftwareVersionService;
use Jexactyl\Repositories\Wings\DaemonServerRepository;
use Jexactyl\Traits\Controllers\PlainJavascriptInjection;
use Jexactyl\Contracts\Repository\NodeRepositoryInterface;
use Jexactyl\Contracts\Repository\ServerRepositoryInterface;

class IndexController extends Controller
{
    use PlainJavascriptInjection;

    public function __construct(
        private Fractal $fractal,
        private DaemonServerRepository $repository,
        private SoftwareVersionService $versionService,
        private NodeRepositoryInterface $nodeRepository,
        private ServerRepositoryInterface $serverRepository,
    ) {
    }

    public function index(): View
    {
        $nodes = $this->nodeRepository->all();
        $servers = $this->serverRepository->all();
        $allocations = DB::table('allocations')->count();
        $suspended = DB::table('servers')->where('status', 'suspended')->count();

        $memoryUsed = 0;
        $memoryTotal = 0;
        $diskUsed = 0;
        $diskTotal = 0;

        foreach ($nodes as $node) {
            $stats = $this->nodeRepository->getUsageStatsRaw($node);

            $memoryUsed += $stats['memory']['value'];
            $memoryTotal += $stats['memory']['max'];
            $diskUsed += $stats['disk']['value'];
            $diskTotal += $stats['disk']['max'];
        }

        $this->injectJavascript([
            'servers' => $servers,
            'diskUsed' => $diskUsed,
            'diskTotal' => $diskTotal,
            'suspended' => $suspended,
            'memoryUsed' => $memoryUsed,
            'memoryTotal' => $memoryTotal,
        ]);

        return view('admin.jexactyl.index', [
            'version' => $this->versionService,
            'servers' => $servers,
            'allocations' => $allocations,
            'used' => [
                'memory' => $memoryUsed,
                'disk' => $memoryTotal,
            ],
            'available' => [
                'memory' => $memoryTotal,
                'disk' => $diskTotal,
            ],
        ]);
    }

    /**
     * Handle settings update.
     *
     * @throws \Jexactyl\Exceptions\Model\DataValidationException
     * @throws \Jexactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function update(BaseSettingsFormRequest $request): RedirectResponse
    {
        foreach ($request->normalize() as $key => $value) {
            $this->settings->set('jexactyl::' . $key, $value);
        }

        $this->alert->success('Jexactyl Settings have been updated.')->flash();

        return redirect()->route('admin.settings');
    }
}
