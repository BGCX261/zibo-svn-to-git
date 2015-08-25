<?php

namespace joppa\podcast\controller;

use joppa\podcast\model\PodcastModel;

use zibo\admin\controller\LocalizeController;

use zibo\library\filesystem\File;
use zibo\library\html\form\Form;

use zibo\orm\manager\controller\ScaffoldManager;
use zibo\orm\scaffold\form\ScaffoldForm;
use zibo\orm\scaffold\table\ScaffoldTable;

/**
 * Manager for the podcasts
 */
class PodcastManager extends ScaffoldManager {

	/**
	 * Translation key for the add button
	 * @var string
	 */
	const TRANSLATION_ADD = 'joppa.podcast.button.add';

	/**
	 * Translation key for the label of the date publication field
	 * @var unknown_type
	 */
	const TRANSLATION_DATE_PUBLICATION = 'joppa.podcast.label.date.publication';

	/**
	 * Translation key for the label of the title field
	 * @var unknown_type
	 */
	const TRANSLATION_TITLE = 'joppa.podcast.label.title';

	/**
	 * Constructs a new podcast manager
	 * @return null
	 */
    public function __construct() {
    	$translator = $this->getTranslator();

    	$isReadOnly = false;

    	$search = array('title', 'teaser', 'text');

    	$order = array(
            $translator->translate(self::TRANSLATION_DATE_PUBLICATION) => array(
                'ASC' => '{datePublication} ASC',
                'DESC' => '{datePublication} DESC',
            ),
            $translator->translate(self::TRANSLATION_TITLE) => array(
                'ASC' => '{title} ASC',
                'DESC' => '{title} DESC',
            ),
    	);

        parent::__construct(PodcastModel::NAME, PodcastWidget::TRANSLATION_NAME, PodcastWidget::ICON, $isReadOnly, $search, $order);

        $this->translationAdd = self::TRANSLATION_ADD;
        $this->translationOverview = PodcastWidget::TRANSLATION_NAME;
        $this->orderDirection = 'DESC';
    }

    /**
     * Gets the form for the data of the model
     * @param mixed $data Data object to preset the form
     * @return zibo\library\html\form\Form
     */
    protected function getForm($data = null) {
        $form = new ScaffoldForm($this->request->getBasePath() . '/' . self::ACTION_SAVE, $this->model, $data, array('locale', 'slug'), true);

        $imageField = $form->getField('image');
        $imageField->setUploadPath(new File(PodcastModel::PATH_IMAGE));

        $audioField = $form->getField('audio');
        $audioField->setUploadPath(new File(PodcastModel::PATH_AUDIO));

        return $form;
    }

    /**
     * Gets the data object from the provided form
     * @param zibo\library\html\form\Form $form
     * @return mixed Data object
     */
    protected function getFormData(Form $form) {
        $data = $form->getData();

        if (!$data->id) {
            $data->locale = LocalizeController::getLocale();
        }

        return $data;
    }

}