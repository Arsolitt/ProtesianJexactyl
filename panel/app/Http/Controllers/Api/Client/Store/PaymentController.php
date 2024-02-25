<?php

namespace Jexactyl\Http\Controllers\Api\Client\Store;

use Illuminate\Http\Request;
use Jexactyl\Exceptions\Model\DataValidationException;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Http\Requests\Api\Client\Store\PaymentRequest;
use Jexactyl\Models\Payment;
use Jexactyl\Services\Store\PaymentCreationService;

class PaymentController extends Controller
{

    public function __construct(private PaymentCreationService $creationService)
    {

    }

    /**
     * @throws DataValidationException
     */
    public function purchase(PaymentRequest $request)
    {

        $payment = $this->creationService->handle($request);
        return response()->json($payment);

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
