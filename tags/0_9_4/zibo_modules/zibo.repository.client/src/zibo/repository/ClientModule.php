<?php

namespace zibo\repository;

use zibo\admin\model\module\Installer;

use zibo\core\Zibo;

use zibo\repository\model\Client;

/**
 * Repository client module initializer
 */
class ClientModule {

    /**
     * Configuration key for the URL of the XML-RPC server of the repository
     * @var string
     */
    const CONFIG_REPOSITORY = 'repository.client.repository';

    /**
     * Translation key for the title of the repository
     * @var string
     */
    const TRANSLATION_TITLE = 'repository.client.title';

    /**
     * Route of the repository client
     * @var string
     */
    const ROUTE = 'admin/modules/repository';

    /**
     * Instance of the repository client
     * @var zibo\repository\model\Client
     */
    private static $client;

    /**
     * Initializes the repository client module
     * @return null
     */
    function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(Installer::EVENT_PRE_INSTALL_MODULE, array($this, 'preInstallModule'));
    }

    /**
     * Solves the dependencies of the modules which are about to be installed
     * @param zibo\admin\model\Installer $installer Instance of the Zibo module installer
     * @param $modulePath
     * @param array $modules Array with the modules which are to be installed
     * @return null
     */
    public function preInstallModule(Installer $installer, $modulePath, $modules) {
        $client = $this->getClient($installer);
        if ($client) {
            $client->solveDependencies($modules);
        }
    }

    /**
     * Gets the repository client
     * @param zibo\admin\model\Installer $installer Instance of the Zibo module installer
     * @return zibo\repository\model\Client|null Instance of the client if configured properly, null otherwise
     */
    public static function getClient(Installer $installer = null) {
        if (self::$client) {
            return self::$client;
        }

        $url = Zibo::getInstance()->getConfigValue(self::CONFIG_REPOSITORY, 'http://repository.zibo.be/xmlrpc');
        if ($url === null) {
            return null;
        }

        if (!$installer) {
            $installer = new Installer();
        }

        return self::$client = new Client($installer, $url);
    }

}