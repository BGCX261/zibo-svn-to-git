<?php

namespace joppa\model;

use joppa\Module;

use zibo\admin\controller\LocalizeController;

use zibo\core\Zibo;

use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\manipulation\statement\UpdateStatement;

use zibo\library\i18n\translation\Translator;
use zibo\library\orm\model\ExtendedModel;
use zibo\library\orm\query\ModelQuery;

use zibo\ZiboException;

use \Exception;

/**
 * Model of a site
 */
class SiteModel extends ExtendedModel implements NodeType {

	/**
	 * Name of the model
	 * @var string
	 */
	const NAME = 'Site';

    /**
     * The node type for the data objects of this model
     * @var string
     */
    const NODE_TYPE = 'site';

    /**
     * Class name of the backend controller
     * @var string
     */
    const CONTROLLER_BACKEND = 'joppa\\controller\\backend\\SiteController';

    /**
     * Localization method to keep a translated copy (1 tree)
     * @var string
     */
    const LOCALIZATION_METHOD_COPY = 'copy';

    /**
     * Localization method to keep a unique tree per locale
     * @var string
     */
    const LOCALIZATION_METHOD_UNIQUE = 'unique';

    /**
     * Create a site data object
     * @return joppa\model\Site
     */
    public function createSite() {
        $site = $this->createData();

        $site->localizationMethod = self::LOCALIZATION_METHOD_COPY;

        $site->node = $this->getModel(NodeModel::NAME)->createNode(self::NODE_TYPE);
        $site->node->settings->set(NodeSettingModel::SETTING_PUBLISH, NodeSettingModel::DEFAULT_PUBLISH, true);

        return $site;
    }

    /**
     * Get a site
     * @param int $id id of the site
     * @param integer $recursiveDepth
     * @param string $locale
     * @return Site
     * @throws zibo\ZiboException when the site could not be found
     */
    public function getSite($id, $recursiveDepth = 1, $locale = null, $includeUnlocalized = null) {
    	if ($locale === null) {
    		$locale = LocalizeController::getLocale();
    	}
    	if ($includeUnlocalized === null) {
    		$includeUnlocalized = ModelQuery::INCLUDE_UNLOCALIZED_FETCH;
    	}

        $site = $this->findById($id, $recursiveDepth, $locale, $includeUnlocalized);
        if (!$site) {
            throw new ZiboException('Could not find site with id ' . $id);
        }

        if ($recursiveDepth === 0) {
            return $site;
        }

        $nodeSettingModel = $this->getModel(NodeSettingModel::NAME);

        $site->node->settings = $nodeSettingModel->getNodeSettings($site->node->id);

        return $site;
    }

    /**
     * Get a list of the sites
     * @param integer $recursiveDepth
     * @param stirng $locale
     * @return array Array with Site objects
     */
    public function getSites($recursiveDepth = 1, $locale = null) {
        $query = $this->createQuery($recursiveDepth, $locale);
        $query->addOrderBy('{node.name} ASC');
        return $query->query();
    }

    /**
     * Get a list of the sites
     * @param string $locale
     * @return array Array with site id as key and the site name as value
     */
    public function getSiteList($locale = null) {
        if ($locale === null) {
            $locale = LocalizeController::getLocale();
        }

    	$query = $this->createQuery(1, $locale, ModelQuery::INCLUDE_UNLOCALIZED_FETCH);
        $query->setFields('{id}, {node}, {isDefault}');
        $query->addOrderBy('{node.name} ASC');

        $sites = $query->query();

        $list = array();
        foreach ($sites as $site) {
            $list[$site->id] = $site->node->name . ($site->isDefault ? ' «' : '');
        }

        return $list;
    }

    /**
     * Gets the a list of the sites with a base URL
     * @return array Array with the base URL of the site as key and the id of the site as value. The default site will be indexed with key 0 instead of the url
     */
    public function getSiteUrls() {
    	$cache = Module::getCache();

    	$urls = $cache->get(Module::CACHE_TYPE_SITE_URLS, 0);
    	if ($urls) {
    		return $urls;
    	}

        $query = $this->createQuery(0);
        $query->addCondition('({defaultNode} IS NOT NULL AND {defaultNode} <> %1%)', '');
        $query->addCondition('({baseUrl} IS NOT NULL AND {baseUrl} <> %1%) OR {isDefault} = %2%', '', true);
        $sites = $query->query();

        $urls = array();
        foreach ($sites as $site) {
        	if ($site->isDefault) {
                $urls[0] = $site;
        	} elseif ($site->baseUrl) {
        		$urls[$site->baseUrl] = $site;
        	}
        }

        $cache->set(Module::CACHE_TYPE_SITE_URLS, 0, $urls);

        return $urls;
    }

    /**
     * Gets the site of a node
     * @param integer|Node $node Node to get the site from
     * @param integer $recursiveDepth
     * @param string $locale
     * @param boolean|string $includeUnlocalized
     * @return Site
     */
    public function getSiteForNode($node, $recursiveDepth = 1, $locale = null, $includeUnlocalized = false) {
    	$nodeModel = $this->getModel(NodeModel::NAME);
    	$node = $nodeModel->getRootNode($node, 0, $locale);

    	$query = $this->createQuery($recursiveDepth, $locale, $includeUnlocalized);
    	$query->addCondition('{node} = %1%', $node->id);
    	return $query->queryFirst();
    }

    /**
     * Get an array with the nodes of the site of the provided node
     * @param int|Node $node Node to get the nodes of
     * @param string|array $excludes id's of nodes which are not to be included in the result
     * @param int $maxDepth Maximum number of nested node levels to be fetched
     * @param string $locale Locale code
     * @param boolean $loadSettings set to true to load the NodeSettings object of the node
     * @param boolean $isFrontend set to true to load only the nodes which are available in the frontend
     * @return array Array with the node id as key and the node as value
     */
    public function getNodeTreeForNode($node, $excludes = null, $maxDepth = null, $locale = null, $loadSettings = false, $isFrontend = false) {
        $site = $this->getSiteForNode($node, 0);

        return $this->getNodeTreeForSite($site, $excludes, $maxDepth, $locale, $loadSettings, $isFrontend);
    }

    /**
     * Get an array with the nodes and specify the number of levels for fetching the children of the nodes.
     * @param int|Site $site The site to fetch the nodes of
     * @param string|array $excludes id's of nodes which are not to be included in the result
     * @param int $maxDepth Maximum number of nested node levels to be fetched
     * @param string $locale Locale code
     * @param boolean $loadSettings set to true to load the NodeSettings object of the node
     * @param boolean $isFrontend set to true to load only the nodes which are available in the frontend
     * @return array Array with the node id as key and the node as value
     */
    public function getNodeTreeForSite($site, $excludes = null, $maxDepth = null, $locale = null, $loadSettings = false, $isFrontend = false) {
    	if (is_numeric($site)) {
    		$site = $this->getSite($site);
    	}

    	if ($site->localizationMethod == self::LOCALIZATION_METHOD_UNIQUE) {
    		$includeUnlocalized = false;
    	} else {
	    	$includeUnlocalized = ModelQuery::INCLUDE_UNLOCALIZED_FETCH;
    	}

    	$nodeModel = $this->getModel(NodeModel::NAME);

    	return $nodeModel->getNodeTree($site->node, $excludes, $maxDepth, $locale, $includeUnlocalized, $loadSettings, $isFrontend);
    }

    /**
     * Get the default site
     * @param integer $recursiveDepth
     * @param boolean $locale
     * @return Site
     */
    public function getDefaultSite($recursiveDepth = 1, $locale = null) {
        $site = $this->findFirstBy('isDefault', '1', $recursiveDepth, $locale, true);
        if (!$site) {
            throw new ZiboException('Could not find the default site');
        }

        if ($recursiveDepth === 0) {
            return $site;
        }

        $nodeSettingModel = $this->getModel(NodeSettingModel::NAME);

        $site->node->settings = $nodeSettingModel->getNodeSettings($site->node->id);

        return $site;
    }

    /**
     * Set the default site
     * @param int|Site $site
     * @return null
     * @throws Exception when an error occured
     */
    public function setDefaultSite($site) {
        $id = $this->getPrimaryKey($site);

        if ($site === $id) {
	        $site = $this->findById($id, 0);
	        if (!$site) {
	            throw new ZiboException('Could not find site with id ' . $id);
	        }
        }

        $transactionStarted = $this->startTransaction();
        try {
            $statement = new UpdateStatement();
            $statement->addTable(new TableExpression($this->meta->getName()));
            $statement->addValue(new FieldExpression('isDefault'), 0);

            $this->executeStatement($statement);

            $site->isDefault = true;
            $this->save($site, 'isDefault');

            $this->commitTransaction($transactionStarted);
        } catch (Exception $exception) {
            $this->rollbackTransaction($transactionStarted);
            throw $exception;
        }
    }

    /**
     * Copies a site
     * @param Site $site Site to copy
     * @return Site The copy of the site
     */
    public function copy(Site $site) {
    	$nodeModel = $this->getModel(NodeModel::NAME);
    	$nodeSettingModel = $this->getModel(NodeSettingModel::NAME);

    	$isTransactionStarted = $this->startTransaction();
    	try {
	    	$nodeCopy = $nodeModel->copy($site->node, true, true, false, true);

	    	$siteCopy = $this->createData();
	    	$siteCopy->node = $nodeCopy->id;
	    	$siteCopy->localizationMethod = $site->localizationMethod;
	    	$siteCopy->isDefault = false;

	    	$this->save($siteCopy);

	    	$nodeCopyTable = $nodeModel->getCopyTable();

	    	$settings = $nodeSettingModel->getAllNodeSettingsForNode($nodeCopy, '{key} LIKE %1%', null, array('1' => NodeSettingModel::SETTING_WIDGET . '.%.node%'));
	    	foreach ($settings as $setting) {
	    		if (is_numeric($setting->value) && array_key_exists($setting->value, $nodeCopyTable)) {
	    			$setting->value = $nodeCopyTable[$setting->value];
	    			$nodeSettingModel->save($setting, 'value');
	    		}
	    	}

	    	$siteCopy->node = $nodeCopy;

    		$this->commitTransaction($isTransactionStarted);
    	} catch (Exception $exception) {
    		$this->rollbackTransaction($isTransactionStarted);
    		throw $exception;
    	}

    	return $siteCopy;
    }

    /**
     * Saves the site, makes sure a default site is selected
     * @param Site $site The site to save
     * @return null
     */
    protected function saveData($site) {
    	$isNew = $site->id ? false : true;

    	parent::saveData($site);

    	if (!$isNew) {
    		return;
    	}

    	$query = $this->createQuery(0);
    	$numSites = $query->count();

    	if ($numSites == 1) {
    		$this->setDefaultSite($site);
    	}
    }

    /**
     * Deletes a site, makes sute a default site is selected
     * @param Site $site The site to save
     * @return null
     */
    protected function deleteData($site) {
    	$site = parent::deleteData($site);

    	if (!$site->isDefault) {
    		return $site;
    	}

        $query = $this->createQuery(0);
        $newDefaultSite = $query->queryFirst();
    	if ($newDefaultSite) {
    		$this->setDefaultSite($newDefaultSite);
    	}

    	return $site;
    }

    /*
     *
     * NodeType methods
     *
     */

    /**
     * Get the data of a node
     * @param int $id id of the node
     * @param integer $recursiveDepth
     * @param string $locale code of the locale
     * @return mixed data of the node
     */
    public function getNodeData($id, $recursiveDepth = 1, $locale = null) {
        $site = $this->findFirstBy('node', $id, $recursiveDepth, $locale, true);
        if (!$site) {
            throw new ZiboException('Could not find site with node id ' . $id);
        }

        if ($recursiveDepth === 0) {
            return $site;
        }

        $nodeSettingModel = $this->getModel(NodeSettingModel::NAME);

        $site->node->settings = $nodeSettingModel->getNodeSettings($site->node->id);

        return $site;
    }

    /**
     * Get the label of this node type
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getLabel(Translator $translator) {
        return $translator->translate(Module::TRANSLATION_NODE_TYPE_PREFIX . self::NODE_TYPE);
    }

    /**
     * Checks if this node type is available in the frontend
     * @return boolean
     */
    public function isAvailableInFrontend() {
        return false;
    }

    /**
     * Gets the default inherit value for a new node setting
     * @return boolean
     */
    public function getDefaultInherit() {
        return true;
    }

    /**
     * Get the class name of the frontend controller
     * @return string
     */
    public function getFrontendController() {
        return null;
    }

    /**
     * Get the class name of the backend controller
     * @return string
     */
    public function getBackendController() {
        return self::CONTROLLER_BACKEND;
    }

}