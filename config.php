<?php

use humhub\modules\wallet\Module;

return [
    'id'        => 'wallet',
    'class'     => Module::class,
    'namespace' => 'humhub\modules\wallet',
    'urlManagerRules' => [
        'wallet/wallet/google' => 'wallet/wallet/google',
        'wallet/wallet/apple'  => 'wallet/wallet/apple',
    ],
];
