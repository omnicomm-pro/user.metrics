<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\LeadTable;
use Bitrix\Crm\ContactTable;
use Bitrix\Crm\CompanyTable;
use Bitrix\Crm\Category\DealCategory;
use Bitrix\Voximplant\StatisticTable;

Loc::loadMessages(__FILE__);

class UserMetricsComponent extends CBitrixComponent {

    public function includeModules() {

        $modules = ['crm', 'voximplant'];

        foreach($modules as $module) {
            Loader::includeModule($module);
        }

    }

    public function executeComponent() {

        $this->includeModules();

        $userId = $this->arParams['USER_ID'];

        switch($this->arParams['TEMPLATE']) {

            case 'crm':
                $this->arResult['table_columns'] = $this->getDealsColumns();
                $this->arResult['table_rows'] = $this->getUserDeals($userId);
                break;

            case 'telephony':
                $this->arResult['table_columns'] = $this->getCallColumns();
                $this->arResult['table_rows'] = $this->getUserCalls($userId);
                break;
                
        }
        
        $this->includeComponentTemplate();
    }

    public function getDate($dateString = '-30 days') {
        $date = DateTime::createFromTimestamp(time());
        return $date->add($dateString);
    }

    public function getDealsColumns() {

        return [
            'DEAL' => Loc::getMessage('CRM_DEAL_TITLE'),
            'STAGE' => Loc::getMessage('CRM_DEAL_STAGE'),
            'CATEGORY' => Loc::getMessage('CRM_DEAL_CATEGORY'),
            'DATE_CREATE' => Loc::getMessage('CRM_DEAL_DATE_CREATE'),
            'COMPANY' => Loc::getMessage('CRM_DEAL_COMPANY'),
            'CONTACT' => Loc::getMessage('CRM_DEAL_CONTACT')
        ];

    }

    public function getUserDeals($userId) {

        $deals = DealTable::getList([
            'order' => [
                'DATE_CREATE' => 'DESC'
            ],
            'filter' => [
                'ASSIGNED_BY_ID' => $userId,
                'IS_WORK' => true
            ],
            'select' => [
                'ID',
                'TITLE',
                'STAGE' => 'STAGE_ID',
                'CATEGORY' => 'CATEGORY_ID',
                'COMPANY.ID',
                'COMPANY_TITLE' => 'COMPANY.TITLE',
                'CONTACT.ID',
                'CONTACT_NAME' => 'CONTACT.NAME',
                'CONTACT_LAST_NAME' => 'CONTACT.LAST_NAME',
                'DATE_CREATE'
            ]
        ])->fetchAll();

        $result = [];

        foreach($deals as $deal) {

            $dealId = $deal['ID'];
            $dealTitle = $deal['TITLE'];
            $dealUrl = '/crm/deal/details/' . $dealId . '/';
            $stageId = $deal['STAGE'];
            $categoryId = $deal['CATEGORY'];

            $row = [
                'ID' => $dealId,
                'DEAL' => print_url($dealUrl, $dealTitle),
                'STAGE' => $this->getDealStageName($categoryId, $stageId),
                'CATEGORY' => DealCategory::getName($categoryId),
                'DATE_CREATE' => (new DateTime($deal['DATE_CREATE']))->format('d.m.Y')
            ];

            if(!empty($deal['CRM_DEAL_CONTACT_ID'])) {

                $contactId = $deal['CRM_DEAL_CONTACT_ID'];
                $contactUrl = '/crm/contact/details/' . $contactId . '/';
                $contactName = !empty($deal['CONTACT_NAME']) ? $deal['CONTACT_NAME'] : '';
                $contactName .= !empty($deal['CONTACT_LAST_NAME']) ? ' ' . $deal['CONTACT_LAST_NAME'] : '';

                $row['CONTACT'] = print_url($contactUrl, $contactName);

            }

            if(!empty($deal['CRM_DEAL_COMPANY_ID'])) {

                $companyId = $deal['CRM_DEAL_COMPANY_ID'];
                $companyUrl = '/crm/contact/details/' . $companyId . '/';
                $companyTitle = $deal['COMPANY_TITLE'];

                $row['COMPANY'] = print_url($companyUrl, $companyTitle);

            }

            $result[] = $row;

        }

        return $result;

    }

    public function getDealStageName($categoryId, $stageId) {

        $stages = DealCategory::getStageList($categoryId);
        return $stages[$stageId];

    }

    public function getCallColumns() {

        return [
            'PHONE' => Loc::getMessage('CRM_CALL_PHONE'),
            'DATE' => Loc::getMessage('CRM_CALL_DATE'),
            'TYPE' => Loc::getMessage('CRM_CALL_TYPE'),
            'DURATION' => Loc::getMessage('CRM_CALL_DURATION'),
            'CLIENT' => Loc::getMessage('CRM_CALL_CLIENT')
        ];

    }

    public function getCallType($typeId) {

        $callTypes = CVoxImplantHistory::GetCallTypes();
        return $callTypes[$typeId];
    }

    public function getCallClient($entityType, $entityId) {

        $clientUrl = '/crm/' . strtolower($entityType) . '/details/' . $entityId . '/';

        switch($entityType) {

            case 'LEAD':
                $clientName = '[Лид] ' . LeadTable::getById($entityId)->fetch()['TITLE'];
                break;

            case 'CONTACT':
                $client = ContactTable::getById($entityId)->fetch();
                $clientName = '[Контакт] ';
                $clientName .= !empty($client['LAST_NAME']) ? $client['LAST_NAME'] : '';
                $clientName .= !empty($client['NAME']) ? ' ' . $client['NAME'] : '';
                break;

            case 'COMPANY':
                $clientName = '[Компания] ' . CompanyTable::getById($entityId)->fetch()['TITLE'];
                break;
        }

        return print_url($clientUrl, $clientName);
        
    }

    public function getUserCalls($userId) {

        $arCallsParams = [
            'order' => ['CALL_START_DATE' => 'DESC'],
            'filter' => [
                'PORTAL_USER_ID' => $userId,
                '>=CALL_START_DATE' => $this->getDate(),
                '>CALL_DURATION' => 0
            ],
            'select' => [
                'USER' => 'PORTAL_USER_ID',
                'PHONE' => 'PHONE_NUMBER',
                'TYPE' => 'INCOMING',
                'DATE' => 'CALL_START_DATE',
                'DURATION' => 'CALL_DURATION',
                'STATUS' => 'CALL_STATUS',
                'CRM_ENTITY_TYPE',
                'CRM_ENTITY_ID',
                'CRM_ACTIVITY_ID',
                'COMMENT'
            ]
        ];

        $calls = StatisticTable::getList($arCallsParams)->fetchAll();

        $result = [];

        foreach($calls as $call) {
            $row = [
                'PHONE' => $call['PHONE'],
                'TYPE' => $this->getCallType($call['TYPE']),
                'DATE' => (new DateTime($call['DATE']))->__toString(),
                'DURATION' => date('H:i:s', mktime(0,0, $call['DURATION'])),
                'CLIENT' => $this->getCallClient($call['CRM_ENTITY_TYPE'], $call['CRM_ENTITY_ID'])
            ];

            $result[] = $row;
        }

        return $result;

    }

}