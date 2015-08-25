<?php

namespace zibo\repository\table\decorator;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;

use zibo\admin\model\module\Module;
use zibo\admin\model\module\Installer;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;

/**
 * Decorator to show the state of a module on the system with the option to install or upgrade
 */
class ModuleInstallUpgradeActionDecorator implements Decorator {

    /**
     * Translation key for the dependency label
     * @var string
     */
    const TRANSLATION_DEPENDENCY_ZIBO = 'repository.label.dependency.zibo';

    /**
     * Translation key for the module installed label
     * @var string
     */
    const TRANSLATION_INSTALLED = 'repository.label.installed';

    /**
     * Translation key for when a module has a newer version installed
     * @var string
     */
    const TRANSLATION_INSTALLED_NEWER = 'repository.label.version.newer';

    /**
     * Translation key for the install button
     * @var string
     */
    const TRANSLATION_INSTALL = 'repository.button.install';

    /**
     * Translation key for the upgrade button
     * @var string
     */
    const TRANSLATION_UPGRADE = 'repository.button.upgrade';

    /**
     * Translation key for the current version label
     * @var string
     */
    const TRANSLATION_VERSION_CURRENT = 'repository.label.version.current';

    /**
     * Instance of the module installer
     * @var zibo\admin\model\Installer
     */
    private $installer;

    /**
     * URL to the install action
     * @var string
     */
    private $installAction;

    /**
     * URL to the upgrade action
     * @var string
     */
    private $upgradeAction;

    /**
     * Instance of the translator
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new install/upgrade decorator
     * @param zibo\admin\model\Installer $installer Module installer
     * @param string $installAction URL to the install action
     * @param string $upgradeAction URL to the upgrade action
     * @return null
     */
    public function __construct(Installer $installer, $installAction, $upgradeAction) {
        $this->installer = $installer;
        $this->installAction = $installAction;
        $this->upgradeAction = $upgradeAction;
        $this->translator = I18n::getInstance()->getTranslator();
    }

    /**
     * Decorates a table cell by setting an anchor to the cell based on the cell's value
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $module = $cell->getValue();
        if (!($module instanceof Module)) {
            $cell->setValue('');
            return;
        }

        $namespace = $module->getNamespace();
        $name = $module->getName();
        $version = $module->getVersion();
        $ziboVersion = $module->getZiboVersion();

        if (version_compare(Zibo::VERSION, $ziboVersion) === -1) {
            $label = $this->translator->translate(self::TRANSLATION_DEPENDENCY_ZIBO, array('version' => $ziboVersion));
            $label = '<span class="newerZiboRequired">' . $label . '</span>';

            $cell->setValue($label);
            return;
        }

        $urlParams = $namespace . '/' . $name . '/' . $version;

        if (!$this->installer->hasModule($namespace, $name)) {
            $label = $this->translator->translate(self::TRANSLATION_INSTALL, array('version' => $version));

            $anchor = new Anchor($label, $this->installAction . $urlParams);
            $anchor = $anchor->getHtml();

            $cell->setValue($anchor);
            return;
        }

        $installedModule = $this->installer->getModule($namespace, $name);
        $installedVersion = $installedModule->getVersion();
        $versionCompare = version_compare($installedVersion, $version);

        if ($versionCompare === 0) {
            $value = '<span class="installed">' . $this->translator->translate(self::TRANSLATION_INSTALLED) . '</span>';
        } elseif ($versionCompare === -1) {
            $label = $this->translator->translate(self::TRANSLATION_UPGRADE, array('version' => $version));
            $anchor = new Anchor($label, $this->upgradeAction . $urlParams);
            $value = $anchor->getHtml();
        } else {
            $value = '<span class="installedNewer">' . $this->translator->translate(self::TRANSLATION_INSTALLED_NEWER) . '</span>';
        }
        $value .= '<div class="info">' . $this->translator->translate(self::TRANSLATION_VERSION_CURRENT, array('version' => $installedVersion)) . '</div>';

        $cell->setValue($value);
    }

}