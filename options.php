<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

global $APPLICATION;
global $USER;

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/options.php');
Loc::loadMessages(__FILE__);

$module_id = 'bsi.scheduler';
Loader::includeModule($module_id);

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$request = Application::getInstance()->getContext()->getRequest();

$tabs = [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('MAIN_TAB_SET'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'),
        'ICON' => '',
    ],
];
$tabControl = new CAdminTabControl('tabControl', $tabs);
$actionUrl = $APPLICATION->GetCurPage()
    . '?' . http_build_query([
        'mid' => $module_id,
        'lang' => LANGUAGE_ID,
        'back_url_settings' => $request['back_url_settings'] ?? null,
    ])
    . '&' . $tabControl->ActiveTabParam();

if ($request->isPost() && $request['Update'] !== '' && check_bitrix_sessid()) {
    if (isset($request['data']) && is_array($request['data'])) {
        foreach ($request['data'] as $name => $value) {
            Option::set($module_id, $name, $value);
        }
    }

    if ($request['back_url_settings'] !== '') {
        LocalRedirect($request['back_url_settings']);
    } else {
        LocalRedirect($actionUrl);
    }
}
?>
<form method="post" action="<?= $actionUrl ?>">
    <?= bitrix_sessid_post() ?>
    <?php
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td style="width: 40%; vertical-align: top;"><?= Loc::getMessage('BSI_SCHEDULER_OPTION_RESOURCE_PATH') ?>:</td>
        <td style="width: 60%;">
            <input type="text" name="data[resource_path]" value="<?= Option::get($module_id, 'resource_path') ?>" size="50">
            <?php
            echo BeginNote();
            echo Loc::getMessage('BSI_SCHEDULER_OPTION_RESOURCE_PATH_NOTE');
            echo EndNote();
            ?>
        </td>
    </tr>
    <?php $tabControl->Buttons() ?>
    <input type="submit" name="Update" value="<?= Loc::getMessage('MAIN_SAVE') ?>" class="adm-btn-save">
    <input type="reset" name="reset" value="<?= Loc::getMessage('MAIN_RESET') ?>">
    <?php $tabControl->End() ?>
</form>
