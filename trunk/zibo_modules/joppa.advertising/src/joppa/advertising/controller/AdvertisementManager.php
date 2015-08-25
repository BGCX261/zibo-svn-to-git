<?php

namespace joppa\advertising\controller;

use joppa\advertising\model\AdvertisementModel;
use joppa\advertising\table\decorator\AdvertisementDecorator;
use joppa\advertising\view\AdvertisementManagerFormView;

use zibo\library\filesystem\File;
use zibo\library\html\form\Form;
use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;
use zibo\library\orm\model\meta\ModelMeta;

use zibo\orm\manager\controller\ScaffoldManager;
use zibo\orm\scaffold\form\ScaffoldForm;

/**
 * Controller of the advertisement manager
 */
class AdvertisementManager extends ScaffoldManager {

	/**
	 * Path to the icon of this manager
	 * @var string
	 */
    const ICON = 'web/images/joppa/widget/advertisement.png';

    /**
     * Translation key of this manager's name
     * @var string
     */
    const TRANSLATION_MANAGER = 'joppa.advertising.label.advertisements';

    /**
     * Translation key for the add advertisement button
     * @var string
     */
    const TRANSLATION_ADD = 'joppa.advertising.title.advertisement.add';

    /**
     * Translation key for the add advertisement button
     * @var string
     */
    const TRANSLATION_EDIT = 'joppa.advertising.title.advertisement.edit';

    /**
     * Translation key of the overview button
     * @var string
     */
    const TRANSLATION_OVERVIEW = 'joppa.advertising.title.overview';

    /**
     * Constructs a new advertisement manager
     * @return null
     */
    public function __construct() {
        parent::__construct('Advertisement', self::TRANSLATION_MANAGER);

        $this->translationAdd = self::TRANSLATION_ADD;
        $this->translationTitle = self::TRANSLATION_OVERVIEW;
    }

    /**
     * Gets the icon of this manager
     * @return string
     */
    public function getIcon() {
        return self::ICON;
    }

    /**
     * Gets the menu actions of this manager
     * @return array
     */
    public function getActions() {
        $translator = $this->getTranslator();

        $actions = array(
            self::ACTION_ADD => $translator->translate(self::TRANSLATION_ADD),
            '' => $translator->translate(self::TRANSLATION_OVERVIEW),
        );

        return $actions;
    }

    /**
     * Gets a data table for the model
     * @param string $formAction URL where the table form will point to
     * @return zibo\library\html\table\ExtendedTable
     */
    protected function getTable($formAction) {
        $decorator = new AdvertisementDecorator($this->request->getBasePath() . '/' . self::ACTION_EDIT . '/');
        $decorator = new ZebraDecorator($decorator);

        $table = parent::getTable($formAction);
        $table->addDecorator($decorator);

        return $table;
    }

    /**
     * Gets the form for the data of the model
     * @param mixed $data Data object to preset the form
     * @return zibo\library\html\form\Form
     */
    protected function getForm($data = null) {
        $form = new ScaffoldForm($this->request->getBasePath() . '/' . self::ACTION_SAVE, $this->model, $data, array('clicks'), true);

        $imageField = $form->getField('image');
        $imageField->setUploadPath(new File(AdvertisementModel::PATH_IMAGE));

        return $form;
    }

    /**
     * Creates the actual form view
     * @param zibo\library\orm\model\meta\ModelMeta $meta
     * @param zibo\library\html\form\Form $form
     * @param string $title
     * @param mixed $data
     * @param string $localizeAction
     * @return zibo\core\View
     */
    protected function constructFormView(ModelMeta $meta, Form $form, $title, $data = null, $localizeAction = null) {
        return new AdvertisementManagerFormView($form, $title, $data);
    }

}