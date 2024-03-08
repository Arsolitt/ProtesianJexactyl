<?php

namespace Jexactyl\Http\Controllers\Base;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Models\ReferralCode;

class OldReferralController extends Controller
{
    public function redirect(Request $request)
    {
        $code = $request->query('ref');
        $response = new Response();
        $response->header('Location', '/');
        if ($code ?? ReferralCode::where('code', '=', $code)->exists()) {
            $response->withCookie(Cookie::make('referral_code', $code, 60 * 24 * 365, '/', null, false, false, true, 'Lax'));
        }
        return $response;
    }
}
