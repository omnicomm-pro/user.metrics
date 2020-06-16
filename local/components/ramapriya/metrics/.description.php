<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('USER_METRICS_COMPONENT_NAME'),
    'DESCRIPTION' => Loc::getMessage('USER_METRICS_COMPONENT_DESCRIPTION'),
    'PATH' => [
        'ID' => 'CRM'
    ]
];