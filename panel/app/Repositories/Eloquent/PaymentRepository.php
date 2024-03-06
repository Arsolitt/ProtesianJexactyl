<?php

namespace Jexactyl\Repositories\Eloquent;

use Jexactyl\Contracts\Repository\PaymentRepositoryInterface;
use Jexactyl\Models\Payment;

class PaymentRepository extends EloquentRepository implements PaymentRepositoryInterface
{
    /**
     * Return the model backing this repository.
     */
    public function model(): string
    {
        return Payment::class;
    }
}
