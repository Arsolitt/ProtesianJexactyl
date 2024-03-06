<?php

namespace Jexactyl\Http\Controllers\Admin\Payments;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\View\Factory as ViewFactory;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Models\Partner;
use Jexactyl\Models\Payment;
use Prologue\Alerts\AlertsMessageBag;

class PaymentController extends Controller
{
    public function __construct(
        protected AlertsMessageBag $alert,
        protected Translator $translator,
        protected ViewFactory $view
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with('user')
            ->select('payments.*', 'users.username')
            ->leftJoin('users', 'users.id', '=', 'payments.user_id')
            ->orderByRaw("FIELD(status, 'paid', 'canceled', 'open')")
            ->paginate(50);

//        dd($payments);


        return $this->view->make('admin.payments.index', ['payments' => $payments]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $partner = Partner::with('user')
            ->select('partners.*', 'users.username', 'users.email')
            ->leftJoin('users', 'users.id', '=', 'partners.user_id')
            ->first();
        return $this->view->make('admin.partners.view', [
            'partner' => $partner,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partner $partner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {

    }
}
