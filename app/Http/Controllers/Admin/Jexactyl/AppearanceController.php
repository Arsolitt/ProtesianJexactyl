<?php

namespace Jexactyl\Http\Controllers\Admin\Jexactyl;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Exceptions\Model\DataValidationException;
use Jexactyl\Exceptions\Repository\RecordNotFoundException;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Http\Requests\Admin\Jexactyl\AppearanceFormRequest;
use Prologue\Alerts\AlertsMessageBag;

class AppearanceController extends Controller
{
    /**
     * AppearanceController constructor.
     */
    public function __construct(
        private Repository $config,
        private AlertsMessageBag $alert,
        private SettingsRepositoryInterface $settings
    ) {
    }

    /**
     * Render the Jexactyl settings interface.
     */
    public function index(): View
    {
        return view('admin.jexactyl.appearance', [
            'name' => config('app.name'),
            'logo' => $this->settings->get('settings::app:logo', 'https://avatars.githubusercontent.com/u/91636558'),

            'admin' => config('theme.admin'),
            'user' => ['background' => config('theme.user.background')],
        ]);
    }

    /**
     * Handle settings update.
     *
     * @throws DataValidationException|RecordNotFoundException
     */
    public function update(AppearanceFormRequest $request): RedirectResponse
    {
        foreach ($request->normalize() as $key => $value) {
            $this->settings->set('settings::' . $key, $value);
        }

        $this->alert->success('Jexactyl Appearance has been updated.')->flash();

        return redirect()->route('admin.jexactyl.appearance');
    }
}
