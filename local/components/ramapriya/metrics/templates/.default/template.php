<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

switch($arParams['TEMPLATE']) {

    case 'crm':
        $notFoundMessage = Loc::getMessage('TEMPLATE_DEALS_NOT_FOUND');
        break;

    case 'telephony':
        $notFoundMessage = Loc::getMessage('TEMPLATE_CALLS_NOT_FOUND');
        break;

}

?>

<div class="container-fluid m-1">

    <h1><?=$arParams['PAGE_TITLE']?></h1>

<?php if(!empty($arResult['table_rows'])) {?>

    <table class="table table-striped table-responsive-md">
        <thead class="thead-dark">
            <tr>
            <?php foreach($arResult['table_columns'] as $column) {?>
                <th><?=$column?></th>
            <?php }?>
            </tr>
        </thead>
        <tbody>
        <?php foreach($arResult['table_rows'] as $row) {?>
            <tr>
            <?php foreach($arResult['table_columns'] as $key => $value) {?>
                <td><?=$row[$key]?></td>
            <?php }?>
            </tr>
        <?php }?>
        </tbody>
    </table>

<?php } else {?>

    <p><?=$notFoundMessage?></p>

<?php }?>

</div>

<style>

    @import url('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');

</style>