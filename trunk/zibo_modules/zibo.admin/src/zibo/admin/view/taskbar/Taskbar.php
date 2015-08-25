<?php

namespace zibo\admin\view\taskbar;

use zibo\core\View;

use zibo\library\i18n\I18n;
use zibo\library\security\SecurityManager;

/**
 * Taskbar for quick access to the different parts of your application
 */
class Taskbar {

    /**
     * Style class for the applications menu
     * @var string
     */
    const APPLICATIONS_STYLE_CLASS = 'applications';

    /**
     * Style id for the applications menu
     * @var string
     */
    const APPLICATIONS_STYLE_ID = 'taskbarApplications';

    /**
     * Translation key for the applications menu
     * @var string
     */
    const TRANSLATION_APPLICATIONS = 'taskbar.applications';

    /**
     * Translation key for the settings menu
     * @var string
     */
    const TRANSLATION_SETTINGS = 'taskbar.settings';

    /**
     * The applications menu
     * @var Menu
     */
    private $applicationsMenu;

    /**
     * The settings menu
     * @var Menu
     */
    private $settingsMenu;

    /**
     * Array containing the notification panels which are View instances
     * @var array
     */
    private $notificationPanels;

    /**
     * Title of the taskbar
     * @var string
     */
    private $title;

    /**
     * Construct the taskbar
     * @return null
     */
    public function __construct() {
        $translator = I18n::getInstance()->getTranslator();

        $this->applicationsMenu = new Menu($translator->translate(self::TRANSLATION_APPLICATIONS));
        $this->applicationsMenu->setId(self::APPLICATIONS_STYLE_ID);
        $this->applicationsMenu->setClass(self::APPLICATIONS_STYLE_CLASS);

        $this->settingsMenu = new Menu($translator->translate(self::TRANSLATION_SETTINGS));

        $this->notificationPanels = array();
    }

    /**
     * Set the title of the taskbar
     * @param string $title
     * @return null
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Get the title of the taskbar
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Add a notification panel to this taskbar
     * @param zibo\core\View $notificationPanel
     * @return null
     */
    public function addNotificationPanel(View $notificationPanel) {
        $this->notificationPanels[] = $notificationPanel;
    }

    /**
     * Get all the notification panels
     * @return array Array containing View instances
     */
    public function getNotificationPanels() {
        return $this->notificationPanels;
    }

    /**
     * Get the applications menu
     * @return Menu
     */
    public function getApplicationsMenu() {
        return $this->applicationsMenu;
    }

    /**
     * Get the settings menu
     * @return Menu
     */
    public function getSettingsMenu() {
        return $this->settingsMenu;
    }

}