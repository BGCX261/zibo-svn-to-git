<?php

namespace joppa\content\form;

use joppa\content\model\ContentProperties;
use joppa\content\model\ContentViewFactory;

use joppa\model\Node;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\translation\Translator;
use zibo\library\i18n\I18n;
use zibo\library\orm\definition\ModelTable;

/**
 * Form to edit the properties of a content detail widget
 */
class ContentDetailPropertiesForm extends AbstractContentPropertiesForm {

    /**
     * Name of the id field
     * @var string
     */
    const FIELD_PARAMETER_ID = 'parameterId';

    /**
     * Translation key for the primary key option
     * @var string
     */
    const TRANSLATION_PRIMARY_KEY = 'joppa.content.label.primary.key';

    /**
     * Translation key for the slug option
     * @var string
     */
    const TRANSLATION_SLUG = 'joppa.content.label.slug';

	/**
	 * Constructs a new properties form
	 * @param string $action URL where this form will point to
	 * @param joppa\model\Node $node
	 * @param joppa\content\model\ContentProperties $properties
	 * @return null
	 */
	public function __construct($action, Node $node, ContentProperties $properties) {
		parent::__construct($action, $node, $properties);

        $translator = I18n::getInstance()->getTranslator();
		$fieldFactory = FieldFactory::getInstance();

		$parameterId = $properties->getParameterId();

        $parameterIdField = $fieldFactory->createField(FieldFactory::TYPE_OPTION, self::FIELD_PARAMETER_ID, $parameterId);
        $parameterIdField->setOptions($this->getParameterIdOptions($translator));

        $this->addField($parameterIdField);
	}

    /**
     * Gets a content properties object for the submitted form
     * @return joppa\content\model\ContentProperties
     */
    public function getContentProperties() {
        $properties = parent::getContentProperties();
        $properties->setParameterId($this->getValue(self::FIELD_PARAMETER_ID));

        return $properties;
    }

    /**
     * Gets the options for the view type
     * @param zibo\library\i18n\translation\Translator $translator
     * @return array
     */
    protected function getViewOptions(Translator $translator) {
        $views = ContentViewFactory::getInstance()->getDetailViews();

        foreach ($views as $name => $class) {
            $views[$name] = $translator->translate(self::TRANSLATION_VIEW . $name);
        }

        return $views;
    }

    /**
     * Gets the options for the parameters type
     * @param zibo\library\i18n\translation\Translator $translator
     * @return array
     */
    private function getParameterIdOptions($translator) {
        return array(
            ModelTable::PRIMARY_KEY => $translator->translate(self::TRANSLATION_PRIMARY_KEY),
            'slug' => $translator->translate(self::TRANSLATION_SLUG),
        );
    }

}