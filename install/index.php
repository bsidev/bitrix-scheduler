<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class bsi_scheduler extends CModule
{
    public const MODULE_ID = 'bsi.scheduler';

    public $MODULE_ID = 'bsi.scheduler';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $PARTNER_NAME = 'Sergey Balasov';
    public $PARTNER_URI = 'https://bsidev.ru';
    public $MODULE_DESCRIPTION;
    public $errors = false;

    public function __construct()
    {
        $arModuleVersion = [];

        Loc::loadMessages(__FILE__);

        include __DIR__ . '/version.php';
        if (is_array($arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'] ?? null;
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'] ?? null;
        }

        $this->MODULE_NAME = Loc::getMessage('BSI_SCHEDULER_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BSI_SCHEDULER_MODULE_DESCRIPTION');
    }

    public function doInstall(): void
    {
        global $APPLICATION;

        $this->installFiles();
        $this->installDb();

        Loader::includeModule($this->MODULE_ID);

        $APPLICATION->includeAdminFile(
            Loc::getMessage('BSI_SCHEDULER_INSTALL_TITLE'),
            __DIR__ . '/step1.php'
        );
    }

    public function doUninstall(): void
    {
        global $APPLICATION, $step;

        $step = (int) $step;
        if ($step < 2) {
            $GLOBALS['errors'] = [];
            $APPLICATION->includeAdminFile(
                Loc::getMessage('BSI_SCHEDULER_UNINSTALL_TITLE'),
                __DIR__ . '/unstep1.php'
            );
        } elseif ($step === 2) {
            $this->uninstallDb([
                'savedata' => $_REQUEST['savedata'],
            ]);
            $this->uninstallFiles();

            $GLOBALS['errors'] = [];
            $APPLICATION->includeAdminFile(
                Loc::getMessage('BSI_SCHEDULER_UNINSTALL_TITLE'),
                __DIR__ . '/unstep2.php'
            );
        }
    }

    public function installDb(): bool
    {
        ModuleManager::registerModule($this->MODULE_ID);

        return true;
    }

    public function uninstallDb($params = []): bool
    {
        if (!$params['savedata']) {
            Option::delete($this->MODULE_ID);
        }

        ModuleManager::unRegisterModule($this->MODULE_ID);

        return true;
    }

    public function installFiles(): bool
    {
        return true;
    }

    public function uninstallFiles(): bool
    {
        return true;
    }
}
