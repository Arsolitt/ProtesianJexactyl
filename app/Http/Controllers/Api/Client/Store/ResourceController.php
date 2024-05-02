<?php

namespace Jexactyl\Http\Controllers\Api\Client\Store;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jexactyl\Events\User\LastActivity;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Controllers\Api\Client\ClientApiController;
use Jexactyl\Http\Requests\Api\Client\Store\PurchaseResourceRequest;
use Jexactyl\Services\Store\LimitsService;
use Jexactyl\Services\Store\ResourcePurchaseService;
use Jexactyl\Transformers\Api\Client\Store\CostTransformer;
use Jexactyl\Transformers\Api\Client\Store\LimitTransformer;
use Jexactyl\Transformers\Api\Client\Store\UserTransformer;

class ResourceController extends ClientApiController
{
    /**
     * ResourceController constructor.
     */
    public function __construct(
        private ResourcePurchaseService $purchaseService,
        private LimitsService           $limitsService
    )
    {
        parent::__construct();
    }

    /**
     * Get the resources for the authenticated user.
     *
     * @throws DisplayException
     */
    public function user(Request $request)
    {
        $user = $request->user();
        LastActivity::dispatch($user, $request->header('CF-Connecting-IP') ?? $request->ip());
        return $this->fractal->item($user)
            ->transformWith($this->getTransformer(UserTransformer::class))
            ->toArray();
    }

    /**
     * Get the cost of resources.
     *
     * @throws DisplayException
     */
    public function costs(Request $request)
    {
        $data = [];
        $prefix = 'store:cost:';
        $types = ['memory', 'disk', 'allocation', 'backup', 'database', 'slot'];

        foreach ($types as $type) {
            $data[] = $this->settings->get($prefix . $type, 0);
        }

        return $this->fractal->item($data)
            ->transformWith($this->getTransformer(CostTransformer::class))
            ->toArray();
    }

    public function limits(Request $request)
    {
        $data = $this->limitsService->getLimits();
        return $this->fractal->item($data)
            ->transformWith($this->getTransformer(LimitTransformer::class))
            ->toArray();
    }

    /**
     * Allows users to purchase resources via the store.
     *
     * @throws DisplayException
     */
    public function purchase(PurchaseResourceRequest $request): JsonResponse
    {
        $this->purchaseService->handle($request);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
