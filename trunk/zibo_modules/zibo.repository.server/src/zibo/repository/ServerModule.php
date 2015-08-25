<?php

namespace zibo\repository;

use zibo\core\Zibo;

use zibo\library\filesystem\File;

use zibo\repository\model\Repository;
use zibo\repository\xmlrpc\Service;

use zibo\xmlrpc\controller\ServerController;

/**
 * Repository server initializer
 */
class ServerModule {

    /**
     * Configuration key for the repository directory
     * @var string
     */
    const CONFIG_DIRECTORY_REPOSITORY = 'repository.server.directory';

    /**
     * Default directory for the repository
     * @var string
     */
    const DIRECTORY_REPOSITORY = 'application/data/repository';

    /**
     * Translation key for the repository title
     * @var string
     */
    const TRANSLATION_TITLE = 'repository.title.server';

    /**
     * Initialize the repository server module
     * @return null
     */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(ServerController::EVENT_PRE_SERVICE, array($this, 'registerXmlrpcServices'));
    }

    /**
     * Registers the webservices on the XML-RPC server
     * @param zibo\library\xmlrpc\Server $server Instance of the XML-RPC server
     * @return null
     */
    public function registerXmlrpcServices($server) {
        $repository = new Repository(new File(self::DIRECTORY_REPOSITORY));

        $service = new Service($repository);
        $service->registerServices($server);
    }

}