<?php

namespace Jexactyl\Http\Controllers\Admin\Jexactyl;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Exceptions\Model\DataValidationException;
use Jexactyl\Exceptions\Repository\RecordNotFoundException;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Http\Requests\Admin\Jexactyl\StoreFormRequest;
use Prologue\Alerts\AlertsMessageBag;

class StoreController extends Controller
{
    /**
     * StoreController constructor.
     */
    public function __construct(
        private AlertsMessageBag            $alert,
        private SettingsRepositoryInterface $settings
    )
    {
    }

    /**
     * Render the Jexactyl store settings interface.
     */
    public function index(): View
    {
        $prefix = 'store:';

        $currencies = [];
        foreach (config('store.settings.currencies') as $key => $value) {
            $currencies[] = ['code' => $key, 'name' => $value];
        }

        return view('admin.jexactyl.store', [
            'enabled' => $this->settings->get($prefix . 'enabled', false),

            'yookassa' => [
                'name' => $this->settings->get($prefix . 'yookassa:name', 'Yookassa'),
                'enabled' => $this->settings->get($prefix . 'yookassa:enabled', false),
                'min' => $this->settings->get($prefix . 'yookassa:min', 10),
                'max' => $this->settings->get($prefix . 'yookassa:max', 9999),
            ],

            'selected_currency' => $this->settings->get($prefix . 'currency', 'USD'),
            'currencies' => $currencies,

            'cost_memory' => $this->settings->get($prefix . 'cost:memory', 75 / 1024) * 1024,
            'cost_disk' => $this->settings->get($prefix . 'cost:disk', 2 / 1024) * 1024,
            'cost_slot' => $this->settings->get($prefix . 'cost:slot', 50),
            'cost_allocation' => $this->settings->get($prefix . 'cost:allocation', 10),
            'cost_backup' => $this->settings->get($prefix . 'cost:backup', 10),
            'cost_database' => $this->settings->get($prefix . 'cost:database', 10),

            'limit_min_memory' => $this->settings->get($prefix . 'limit:min:memory', 512),
            'limit_min_disk' => $this->settings->get($prefix . 'limit:min:disk', 1024),
            'limit_min_allocations' => $this->settings->get($prefix . 'limit:min:allocations', 1),
            'limit_min_backups' => $this->settings->get($prefix . 'limit:min:backups', 0),
            'limit_min_databases' => $this->settings->get($prefix . 'limit:min:databases', 0),

            'limit_max_memory' => $this->settings->get($prefix . 'limit:max:memory', 16384),
            'limit_max_disk' => $this->settings->get($prefix . 'limit:max:disk', 102400),
            'limit_max_allocations' => $this->settings->get($prefix . 'limit:max:allocations', 25),
            'limit_max_backups' => $this->settings->get($prefix . 'limit:max:backups', 25),
            'limit_max_databases' => $this->settings->get($prefix . 'limit:max:databases', 25),
        ]);
    }

    /**
     * Handle settings update.
     *
     * @throws DataValidationException
     * @throws RecordNotFoundException
     */
    public function update(StoreFormRequest $request): RedirectResponse
    {
        foreach ($request->normalize() as $key => $value) {
            switch ($key) {
                case 'store:cost:disk':
                case 'store:cost:memory':
                    $value = $value / 1024;
                    break;
                default:
                    break;
            }
            $this->settings->set($key, $value);
        }

        $this->alert->success('If you have enabled a payment gateway, please remember to configure them. <a href="https://docs.jexactyl.com">Documentation</a>')->flash();

        return redirect()->route('admin.jexactyl.store');
    }
}
