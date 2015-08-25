<?php

namespace zibo\admin\view\system;

use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\SoftwareDetector;

/**
 * View to show an overview of the system setup
 */
class SystemView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/system/system';

    /**
     * Path to the JS script of this view
     * @var string
     */
    const SCRIPT_SYSTEM = 'web/scripts/admin/system.js';

    /**
     * Path to the CSS of this view
     * @var string
     */
    const STYLE_SYSTEM = 'web/styles/admin/system.css';

    /**
     * Constructs a new system view
     * @param array $ziboConfiguration The complete Zibo configuration
     * @param array $baseUrl The base URL of the installation
     * @param array $routes Array with Route objects
     * @param zibo\library\SoftwareDetector $softwareDetector Instance of a software detector
     * @param integer $numVistors Number of visitors
     * @param integer $numUsers Number of users
     * @param integer $numGuests Number of guests
     * @param array $currentUsers The names of the current users
     * @return null
     */
    public function __construct(array $ziboConfiguration, $baseUrl, array $routes, SoftwareDetector $softwareDetector, $numVisitors, $numUsers, $numGuests, $currentUsers) {
        parent::__construct(self::TEMPLATE);

        $this->set('ziboConfiguration', $ziboConfiguration);
        $this->set('ziboVersion', Zibo::VERSION);
        $this->set('phpVersion', phpversion());
        $this->set('baseUrl', $baseUrl);
        $this->set('routes', $routes);
        $this->set('osName', $softwareDetector->getOperatingSystemName());
        $this->set('osVersion', $softwareDetector->getOperatingSystemVersion());
        $this->set('browserName', $softwareDetector->getBrowserName());
        $this->set('browserVersion', $softwareDetector->getBrowserVersion());
        $this->set('userAgent', $softwareDetector->getUserAgent());
        $this->set('ip', $_SERVER['REMOTE_ADDR']);
        $this->set('numVisitors', $numVisitors);
        $this->set('numUsers', $numUsers);
        $this->set('numGuests', $numGuests);
        $this->set('currentUsers', $currentUsers);

        $this->addStyle(self::STYLE_SYSTEM);
        $this->addJavascript(self::SCRIPT_SYSTEM);
        $this->addInlineJavascript('ziboAdminInitializeSystem();');
    }

}