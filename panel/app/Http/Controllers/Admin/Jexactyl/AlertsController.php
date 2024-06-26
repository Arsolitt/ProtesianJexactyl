<?php

namespace Jexactyl\Http\Controllers\Admin\Jexactyl;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Exceptions\Model\DataValidationException;
use Jexactyl\Exceptions\Repository\RecordNotFoundException;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Http\Requests\Admin\Jexactyl\AlertFormRequest;
use Prologue\Alerts\AlertsMessageBag;

class AlertsController extends Controller
{
    /**
     * AppearanceController constructor.
     */
    public function __construct(
        private AlertsMessageBag $alert,
        private SettingsRepositoryInterface $settings
    ) {
    }

    /**
     * Render the Jexactyl settings interface.
     */
    public function index(): View
    {
        return view('admin.jexactyl.alerts', [
            'type' => $this->settings->get('alert:type', 'success'),
            'message' => $this->settings->get('alert:message'),
        ]);
    }

    /**
     * Update or create an alert.
     *
     * @throws DataValidationException|RecordNotFoundException
     */
    public function update(AlertFormRequest $request): RedirectResponse
    {
        foreach ($request->normalize() as $key => $value) {
            $this->settings->set($key, $value);
        }

        $this->alert->success('Jexactyl Alert has been updated.')->flash();

        return redirect()->route('admin.jexactyl.alerts');
    }

    /**
     * Delete the current alert.
     */
    public function remove(): RedirectResponse
    {
        $this->settings->forget('alert:type');
        $this->settings->forget('alert:message');

        $this->alert->success('Jexactyl Alert has been removed.')->flash();

        return redirect()->route('admin.jexactyl.alerts');
    }
}
