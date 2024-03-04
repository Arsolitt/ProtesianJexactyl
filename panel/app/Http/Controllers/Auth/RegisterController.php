<?php

namespace Jexactyl\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Exceptions\Model\DataValidationException;
use Jexactyl\Http\Requests\Auth\RegisterRequest;
use Jexactyl\Models\ReferralCode;
use Jexactyl\Services\Users\UserCreationService;

class RegisterController extends AbstractLoginController
{
    /**
     * RegisterController constructor.
     */
    public function __construct(private UserCreationService $creationService, private SettingsRepositoryInterface $settings)
    {
        parent::__construct();
    }

    /**
     * Handle a register request to the application.
     *
     * @throws DataValidationException|DisplayException
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $approved = false;
        $verified = false;
        $prefix = 'registration:';

        if ($this->settings->get('registration:enabled') != 'true') {
            throw new DisplayException('Регистрация отключена');
        }

        if (!$this->settings->get('registration:verification')) {
            $verified = true;
        }

        if ($this->settings->get('approvals:enabled') != 'true') {
            $approved = true;
        }

        $referralCode = $request->input('referral_code');

        if (!ReferralCode::where('code', $referralCode)->exists()) {
            $referralCode = '';
        }

        $this->creationService->handle([
            'email' => $request->input('email'),
            'username' => $request->input('user'),
            'name_first' => $request->input('user'),
            'name_last' => $request->input('user'),
            'password' => $request->input('password'),
            'ip' => $request->getClientIp(),
            'server_slots' => $this->settings->get($prefix . 'slots', 0),
            'approved' => $approved,
            'verified' => $verified,
            'referral_code' => $referralCode,
        ]);

        return new JsonResponse([
            'data' => [
                'complete' => true,
                'intended' => $this->redirectPath(),
            ],
        ]);
    }
}
