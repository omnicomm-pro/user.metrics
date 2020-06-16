<?php

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;



$arLibs = [
    'buttons' => [
        'js' => '/local/php_interface/buttons/js/buttons.js',
        'lang' => '/local/php_interface/buttons/lang/' . LANGUAGE_ID . '/buttons.php',
        'rel' => ['Ajax', 'SidePanel']
    ]
];

foreach($arLibs as $lib => $params) {
    CJSCore::RegisterExt($lib, $params);
}

$profileTemplates = [
    "profile" => ltrim(Option::get('intranet', 'path_user', '', SITE_ID), '/')
];

if(CComponentEngine::parseComponentPath('/', $profileTemplates, $arVars) == 'profile') {

    $userId = (int)$arVars["USER_ID"];

    $script = "<script>
        BX.ready(function() {
            Buttons.createArea($userId);
        });
    </script>";

    

    $USER = new CUser;
    $currentUser = (int)$USER->GetID();

    CJSCore::Init(['buttons', 'SidePanel']);
    Asset::getInstance()->addString($script);    

}