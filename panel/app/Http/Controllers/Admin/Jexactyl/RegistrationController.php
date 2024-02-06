<?php

namespace Jexactyl\Http\Controllers\Admin\Jexactyl;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Http\Requests\Admin\Jexactyl\RegistrationFormRequest;

class RegistrationController extends Controller
{
    /**
     * RegistrationController constructor.
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
        return view('admin.jexactyl.registration', [
            'enabled' => $this->settings->get('registration:enabled', false),
            'verification' => $this->settings->get('registration:verification', false),

            'discord_enabled' => $this->settings->get('discord:enabled', false),
            'discord_id' => $this->settings->get('discord:id', 0),
            'discord_secret' => $this->settings->get('discord:oauth_secret', 0),

            'slot' => $this->settings->get('registration:slots', 2),
        ]);
    }

    /**
     * Handle settings update.
     *
     * @throws \Jexactyl\Exceptions\Model\DataValidationException
     * @throws \Jexactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function update(RegistrationFormRequest $request): RedirectResponse
    {
        foreach ($request->normalize() as $key => $value) {
            $this->settings->set($key, $value);
        }

        $this->alert->success('Jexactyl Registration has been updated.')->flash();

        return redirect()->route('admin.jexactyl.registration');
    }
}
