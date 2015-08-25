<?php

namespace zibo\dashboard\model;

use zibo\library\cache\io\FileCacheIO;
use zibo\library\cache\SimpleCache;
use zibo\library\filesystem\File;

/**
 * The model of the dashboards
 */
class DashboardModel {

    /**
     * Directory for the dashboard cache
     * @var string
     */
    const CACHE_DIRECTORY = 'application/data/';

    /**
     * Type for the dashboard cache
     * @var unknown_type
     */
    const CACHE_TYPE = 'dashboard';

    /**
     * The cahe for the dashboards
     * @var zibo\library\cache\Cache
     */
    private $cache;

    /**
     * Constructs a new dashboard model
     * @return null
     */
    public function __construct() {
        $cacheDirectory = new File(self::CACHE_DIRECTORY);
        $cacheIO = new FileCacheIO($cacheDirectory);

        $this->cache = new SimpleCache($cacheIO);
    }

    /**
     * Gets a dashboard from the model
     * @param string $name Name of the dashboard
     * @return Dashboard
     */
    public function getDashboard($name) {
        return $this->cache->get(self::CACHE_TYPE, $name);
    }

    /**
     * Sets a dashboard to the model
     * @param Dashboard $dashboard
     * @return null
     */
    public function setDashboard(Dashboard $dashboard) {
        $this->cache->set(self::CACHE_TYPE, $dashboard->getName(), $dashboard);
    }

    /**
     * Removes a dashboard from the model
     * @param Dashboard $dashboard
     * @return null
     */
    public function removeDashboard(Dashboard $dashboard) {
        $this->cache->clear(self::CACHE_TYPE, $dashboard->getName());
    }

}