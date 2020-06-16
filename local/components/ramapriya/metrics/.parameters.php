<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'PARAMETERS' => [
        'USER_ID' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('PARAM_USER_ID_NAME'),
            'TYPE' => 'STRING'
        ],
        'TEMPLATE' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('PARAM_TEMPLATE_NAME'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'crm' => Loc::getMessage('PARAM_TEMPLATE_LIST_CRM'),
                'telephony' => Loc::getMessage('PARAM_TEMPLATE_LIST_TELEPHONY')
            ]
        ],
        'PAGE_TITLE' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('PARAM_PAGE_NAME'),
            'TYPE' => 'STRING'
        ]
    ]
];