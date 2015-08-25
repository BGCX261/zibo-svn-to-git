<?php

namespace joppa\advertising\controller;

use joppa\advertising\form\AdvertisementWidgetPropertiesForm;
use joppa\advertising\model\AdvertisementBlockModel;
use joppa\advertising\model\AdvertisementModel;
use joppa\advertising\view\AdvertisementWidgetView;
use joppa\advertising\view\AdvertisementWidgetPropertiesView;

use zibo\library\validation\exception\ValidationException;
use zibo\library\widget\controller\AbstractWidget;

use zibo\orm\controller\ScaffoldManager;

/**
 * Widget to display random advertisements
 */
class AdvertisementWidget extends AbstractWidget {

	/**
	 * Path to the icon of this widget
	 * @var string
	 */
    const ICON = 'web/images/joppa/widget/advertisement.png';

    /**
     * Action to register a click
     * @var string
     */
    const ACTION_CLICK = 'click';

    /**
     * Setting key for the block id
     * @var string
     */
    const PROPERTY_BLOCK = 'block';

    /**
     * Setting key for the width of the advertisement
     * @var string
     */
    const PROPERTY_WIDTH = 'width';

    /**
     * Setting key for the height of the advertisement
     * @var string
     */
    const PROPERTY_HEIGHT = 'height';

    /**
     * Translation key for the widget name
     * @var string
     */
    const TRANSLATION_NAME = 'joppa.advertising.widget.name';

    /**
     * Translation key for the no block set label
     * @var string
     */
    const TRANSLATION_BLOCK_NOT_SET = 'joppa.advertising.label.block.not.set';

    /**
     * Translation key for the block label
     * @var string
     */
    const TRANSLATION_BLOCK = 'joppa.advertising.label.block';

    /**
     * Translation key for the automatic label
     * @var string
     */
    const TRANSLATION_AUTOMATIC = 'joppa.advertising.label.automatic';

    /**
     * Translation key for the width label
     * @var string
     */
    const TRANSLATION_HEIGHT = 'joppa.advertising.label.height';

    /**
     * Translation key for the width label
     * @var string
     */
    const TRANSLATION_WIDTH = 'joppa.advertising.label.width';

    /**
     * Hook with the ORM module to load the defined models
     * @var array
     */
    public $useModels = array(AdvertisementModel::NAME, AdvertisementBlockModel::NAME);

    /**
     * Constructs a new advertisement widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Shows a running advertisement, if available, for the set advertisement block
     * @return null
     */
    public function indexAction() {
        $block = $this->getAdvertisementBlock();
        if (!$block->id) {
            return;
        }

        $advertisement = $this->models[AdvertisementModel::NAME]->getRunningAdvertisement($block->id);
        if (!$advertisement) {
            return;
        }

        $advertisement->url = $this->request->getBasePath() . '/' . self::ACTION_CLICK . '/' . $advertisement->id;
        $width = $this->getWidth();
        $height = $this->getHeight();

        $view = new AdvertisementWidgetView($advertisement, $width, $height);
        $this->response->setView($view);
    }

    /**
     * Registers the click on an advertisement and redirects to the url of it.
     * @return null
     */
    public function clickAction($id) {
        $url = $this->models[AdvertisementModel::NAME]->click($id);
        if (!$url) {
            $url = $this->getReferer();
        }

        $this->response->setRedirect($url);
    }

    /**
     * Gets a preview of the set properties
     * @return string
     */
    public function getPropertiesPreview() {
    	$translator = $this->getTranslator();

    	$block = $this->getAdvertisementBlock();

    	if (!$block->id) {
    		return $translator->translate(self::TRANSLATION_BLOCK_NOT_SET);
    	}

    	$preview = $translator->translate(self::TRANSLATION_BLOCK) . ': ' . $block->name . '<br />';

    	$width = $this->getWidth();
    	$height = $this->getHeight();

    	$preview .= $translator->translate(self::TRANSLATION_WIDTH) . ': ';
    	if ($width) {
    		$preview .= $width;
    	} else {
    		$preview .= $translator->translate(self::TRANSLATION_AUTOMATIC);
    	}
    	$preview .= '<br />' . $translator->translate(self::TRANSLATION_HEIGHT) . ': ';
    	if ($height) {
    		$preview .= $height;
    	} else {
    		$preview .= $translator->translate(self::TRANSLATION_AUTOMATIC);
    	}

    	return $preview;
    }

    /**
     * Performs the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $block = $this->getAdvertisementBlock();
        $width = $this->getWidth();
        $height = $this->getHeight();

        $form = new AdvertisementWidgetPropertiesForm($this->request->getBasePath(), $block, $width, $height);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
                $form->validate();

                $block = $form->getAdvertisementBlock();
                $width = $form->getWidth();
                $height = $form->getHeight();

                $this->models[AdvertisementBlockModel::NAME]->save($block);

                $this->properties->setWidgetProperty(self::PROPERTY_BLOCK, $block->id);
                $this->properties->setWidgetProperty(self::PROPERTY_HEIGHT, $height);
                $this->properties->setWidgetProperty(self::PROPERTY_WIDTH, $width);

                $this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $e) {
                $form->setValidationException($e);
            }
        }

        $view = new AdvertisementWidgetPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

    /**
     * Gets the advertisement block of this widget
     * @return string
     */
    private function getAdvertisementBlock() {
    	$block = null;

        $blockId = $this->properties->getWidgetProperty(self::PROPERTY_BLOCK);
        if ($blockId) {
        	$block = $this->models[AdvertisementBlockModel::NAME]->findById($blockId, 0);
        }

        if (!$block) {
            $block = $this->models[AdvertisementBlockModel::NAME]->createData();
        }

        return $block;
    }

    /**
     * Gets the width setting of this widget
     * @return string
     */
    private function getWidth() {
        return $this->properties->getWidgetProperty(self::PROPERTY_WIDTH);
    }

    /**
     * Gets the height setting of this widget
     * @return string
     */
    private function getHeight() {
        return $this->properties->getWidgetProperty(self::PROPERTY_HEIGHT);
    }

}