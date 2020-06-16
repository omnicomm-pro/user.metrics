<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Main\Context;
use Bitrix\Main\Loader;

Loader::includeModule('intranet');

$subordinateEmployees = CIntranetUtils::getSubordinateEmployees($userId, true, 'Y', ['ID']);

$employees = [];
    
while($emp = $subordinateEmployees->Fetch()) {
    $employees[] = (int)$emp['ID'];
}

$USER = new CUser;
$currentUser = $USER->GetID();

if($USER->IsAdmin() || $currentUser == 4288) {
	$template = '';
} else {
	$template = 'access_denied';
}

$request = Context::getCurrent()->getRequest();

if($request['user']) {

	$APPLICATION->RestartBuffer();

	$APPLICATION->SetTitle($request['title']);

	$componentParams = [
		'USER_ID' => $request['user'],
		'TEMPLATE' => $request['template'],
		'PAGE_TITLE' => $request['title']
	];
	
	$APPLICATION->IncludeComponent('ramapriya:metrics', $template, $componentParams);

	die();

}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';