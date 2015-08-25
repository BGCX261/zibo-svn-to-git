<?php

/**
 * @package zibo-xmlrpc-model
 */
namespace zibo\xmlrpc\model;

use zibo\library\xmlrpc\Server;

/**
 * Manager for a modulair webservice server
 */
class Webservice {

    private $modules;

    /**
     * Construct the manager for the modulair webservice server
     */
    public function __construct() {
        $this->modules = array();
    }

    /**
     * Register the services of the added modules to the xmlrpc server
     * @param Server instance of an xmlrpc server
     */
    public function registerServices(Server $server) {
        foreach ($this->modules as $module) {
            $servicePrefix = $module->getPrefix();

            $services = $module->getServices();
            foreach ($services as $service) {
                $description = null;
                if (isset($service[4])) {
                    $description = $service[4];
                }

                $server->registerService($servicePrefix . $service[0], $service[1], $service[2], $service[3], $description);
            }
        }
    }

    /**
     * Add a module to this server
     * @param WebserviceModule module to add to this server
     */
    protected function addModule(WebserviceModule $module) {
        $this->modules[] = $module;
    }

}