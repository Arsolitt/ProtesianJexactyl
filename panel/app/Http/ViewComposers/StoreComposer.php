<?php

namespace Jexactyl\Http\ViewComposers;

use Illuminate\View\View;

class StoreComposer extends Composer
{
    /**
     * StoreComposer constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Provide access to the asset service in the views.
     */
    public function compose(View $view)
    {
        $view->with('storeConfiguration', [
            'enabled' => $this->setting('store:enabled', Composer::TYPE_BOOL),
            'currency' => $this->setting('store:currency', Composer::TYPE_STR),

            'gateways' => [
                'paypal' => $this->setting('store:paypal:enabled', Composer::TYPE_BOOL),
                'stripe' => $this->setting('store:stripe:enabled', Composer::TYPE_BOOL),
            ],

            'editing' => [
                'enabled' => $this->setting('renewal:editing', Composer::TYPE_BOOL),
            ],

            'referrals' => [
                'enabled' => $this->setting('referrals:enabled', Composer::TYPE_BOOL),
                'reward' => $this->setting('referrals:reward', Composer::TYPE_INT),
            ],

            'earn' => [
                'enabled' => $this->setting('earn:enabled', Composer::TYPE_BOOL),
                'amount' => $this->setting('earn:amount', Composer::TYPE_INT),
            ],
        ]);
    }
}
