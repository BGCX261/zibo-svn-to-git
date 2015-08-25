<?php

namespace joppa\form\backend;

use joppa\model\Site;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\orm\ModelManager;

/**
 * Form to select the current site
 */
class SiteSelectForm extends Form {

    /**
     * Name of the form
     * @var string
     */
	const NAME = 'formSiteSelect';

	/**
	 * Name of the site field
	 * @var string
	 */
	const FIELD_SITE = 'site';

	/**
	 * Array with all the sites as used by this form, This array has the site's id as key
	 * and the site's name as value.
	 * @var array
	 */
	private $siteList;

	/**
     * Construct this form
     * @param string $action url where the form will point to
     * @param joppa\model\Site $site optional site to preselect
     * @return null
	 */
	public function __construct($action, Site $site = null) {
		parent::__construct($action, self::NAME);

        $idSite = 0;
		if ($site != null) {
			$idSite = $site->id;
		}

		$siteModel = ModelManager::getInstance()->getModel('Site');
		$this->siteList = $siteModel->getSiteList();

		$fieldFactory = FieldFactory::getInstance();

		$field = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_SITE, $idSite);
        $field->setOptions($this->siteList);
        $field->addEmpty();
        $field->setAttribute('onchange', 'this.form.submit()');

		$this->addField($field);
	}

	/**
	 * Get the site which is currently selected in this form
	 * @return joppa\model\Site if nothing is selected, null will be returned
	 */
    public function getSite() {
        $id = $this->getValue(self::FIELD_SITE);
        if (empty($id)) {
        	return null;
        }

        $siteModel = ModelManager::getInstance()->getModel('Site');
        return $siteModel->getSite($id);
    }

    /**
     * Get the list with sites as used by this form
     * @return array array with
     */
    public function getSiteList() {
        return $this->siteList;
    }

}