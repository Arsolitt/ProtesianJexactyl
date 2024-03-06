<?php

namespace Jexactyl\Listeners\Payment;

use Illuminate\Contracts\Queue\ShouldQueue;
use Jexactyl\Events\Store\PaymentPaid;
use Jexactyl\Models\Payment;

class PaidListener implements ShouldQueue
{
  public string $queue = 'standard';

  /**
   * Create the event listener.
   */
  public function __construct()
  {
    //
  }

  /**
   * Handle the event.
   */
  public function handle(PaymentPaid $event): void
  {
    $event->payment->update([
      'status' => Payment::STATUS_PAID,
    ]);
  }

}
