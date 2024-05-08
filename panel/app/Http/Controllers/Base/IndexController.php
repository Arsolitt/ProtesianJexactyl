<?php

namespace Jexactyl\Http\Controllers\Base;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\View;
use Jexactyl\Contracts\Repository\ServerRepositoryInterface;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Models\ReferralCode;

class IndexController extends Controller
{
    /**
     * IndexController constructor.
     */
    public function __construct(
        protected ServerRepositoryInterface $repository,
        protected ViewFactory $view
    ) {
    }

    /**
     * Returns listing of user's servers.
     */
    public function index(Request $request): Response|View
    {
        $code = $request->query('ref');
        $response = new Response();
        $response->header('Location', '/');
        if ($code ?? ReferralCode::where('code', '=', $code)->exists()) {
            return $response->withCookie(Cookie::make('referral_code', $code, 60 * 24 * 365, '/', null, false, false, true, 'Lax'));
        }
        return $this->view->make('templates/base.core');
    }

    public function welcome(Request $request): Response|View
    {
        $code = $request->query('ref');
        $operationTime = Carbon::now()->diffInDays(Carbon::create(2023, 7, 25));
        $response = new Response();
        $response->header('Location', '/');
        if ($code ?? ReferralCode::where('code', '=', $code)->exists()) {
            return $response->withCookie(Cookie::make('referral_code', $code, 60 * 24 * 365, '/', null, false, false, true, 'Lax'));
        }
        return $this->view->make('templates/base.welcome')->with(
            'operationTime', $operationTime
        );
    }

    public function tos(Request $request): Response|View
    {
        return $this->view->make('components.information.tos');
    }

    public function privacy(Request $request): Response|View
    {
        return $this->view->make('components.information.privacy');
    }
        public function contacts(Request $request): Response|View
    {
        return $this->view->make('components.information.contacts');
    }

}
