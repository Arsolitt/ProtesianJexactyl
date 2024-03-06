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
    public function compose(View $view): void
    {
        foreach (array_keys(config('store.gateways')) as $key) {
            $prefix = 'store:' . $key . ':';
            $enabled = $this->setting($prefix . 'enabled', Composer::TYPE_BOOL);
            if ($enabled) {
                $gateways[] = [
                    'id' => $key,
                    'name' => $this->setting($prefix . 'name', Composer::TYPE_STR),
                    'enabled' => $enabled,
                    'min' => $this->setting($prefix . 'min', Composer::TYPE_INT),
                    'max' => $this->setting($prefix . 'max', Composer::TYPE_INT),
                ];
            }
        }
        $view->with('storeConfiguration', [
            'enabled' => $this->setting('store:enabled', Composer::TYPE_BOOL),
            'currency' => $this->setting('store:currency', Composer::TYPE_STR),

            'gateways' => $gateways ?? [],

            'referrals' => [
                'enabled' => $this->setting('referrals:enabled', Composer::TYPE_BOOL),
                'reward' => $this->setting('referrals:reward', Composer::TYPE_INT),
            ],

            'editing' => [
                'enabled' => $this->setting('server:editing', Composer::TYPE_BOOL),
            ]
        ]);
    }
}
