<?php

namespace Jexactyl\Http\Controllers\Auth;

use Illuminate\Support\Facades\Log;
use Jexactyl\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Services\Users\UserCreationService;
use Jexactyl\Exceptions\Model\DataValidationException;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;

class DiscordController extends Controller
{
    private SettingsRepositoryInterface $settings;
    private UserCreationService $creationService;

    public function __construct(
        UserCreationService $creationService,
        SettingsRepositoryInterface $settings,
    ) {
        $this->creationService = $creationService;
        $this->settings = $settings;
    }

    /**
     * Uses the Discord API to return a user objext.
     */
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'https://discord.com/api/oauth2/authorize?'
            . 'client_id=' . $this->settings->get('discord:id')
            . '&redirect_uri=' . route('auth.discord.callback')
            . '&response_type=code&scope=identify%20email%20guilds%20guilds.join&prompt=none',
        ], 200, [], null, false);
    }

    /**
     * Returns data from the Discord API to login.
     *
     * @throws DisplayException
     * @throws DataValidationException
     */
    public function callback(Request $request)
    {
        $code = Http::asForm()->post('https://discord.com/api/oauth2/token', [
            'client_id' => $this->settings->get('discord:id'),
            'client_secret' => $this->settings->get('discord:secret'),
            'grant_type' => 'authorization_code',
            'code' => $request->input('code'),
            'redirect_uri' => route('auth.discord.callback'),
        ]);

        if (!$code->ok()) {
            response()->json(['error' => 'invalid_grant'], 401);
        }

        $req = json_decode($code->body());
        if (preg_match('(email|guilds|identify|guilds.join)', $req->scope) !== 1) {
            response()->json(['error' => 'invalid_scope'], 401);
        }

        $discord = json_decode(Http::withHeaders(['Authorization' => 'Bearer ' . $req->access_token])->asForm()->get('https://discord.com/api/users/@me')->body());

        if (User::where('discord_id', $discord->id)->exists()) {
            $user = User::where('discord_id', $discord->id)->first();
        } elseif(User::where('email', $discord->email)->exists()) {
            $user = User::where('email', $discord->email)->first();
            $user->update(['discord_id' => $discord->id]);
        }
        else {

            if (!$this->settings->get('discord:enabled')) {
                response()->json(['error' => 'invalid_grant'], 401);
            }

            $data = [
                'approved' => !$this->settings->get('approvals:enabled'),
                'email' => $discord->email,
                'username' => $discord->username,
                'discord_id' => $discord->id,
                'name_first' => $discord->username,
                'name_last' => $discord->discriminator,
                'password' => $this->genString(),
                'ip' => $request->getClientIp(),
                'server_slots' => $this->settings->get('registration:slots', 0),
            ];

            try {
                $this->creationService->handle($data);
            } catch (\Exception $e) {
                Log::error($e);
                response()->json(['error' => 'invalid_grant'], 401);
            }
            $user = User::where('email', $discord->email)->first();
        }
        Auth::loginUsingId($user->id, true);
        return redirect('/');
    }

    /**
     * Returns a string used for creating a users
     * username and password on the Panel.
     */
    public function genString(): string
    {
        $chars = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        return substr(str_shuffle($chars), 0, 16);
    }
}
