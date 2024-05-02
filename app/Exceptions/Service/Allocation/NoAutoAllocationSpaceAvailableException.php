<?php

namespace Jexactyl\Exceptions\Service\Allocation;

use Jexactyl\Exceptions\DisplayException;

class NoAutoAllocationSpaceAvailableException extends DisplayException
{
    /**
     * NoAutoAllocationSpaceAvailableException constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'На узле недостаточно портов! Попробуй позже или напиши в поддержку.'
        );
    }
}
