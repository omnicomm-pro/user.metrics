<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<div class="container-fluid m-1">
    <h1 class="text-danger"><?=Loc::getMessage('ACCESS_DENIED')?></h1>
</div>
<style>
    @import url('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
</style>