<?php

namespace joppa\content\form;

use joppa\content\model\ContentProperties;
use joppa\content\model\ContentViewFactory;

use joppa\model\Node;
use joppa\model\NodeModel;
use joppa\model\SiteModel;

use zibo\library\database\manipulation\expression\OrderExpression;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\i18n\translation\Translator;
use zibo\library\i18n\I18n;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\HasField;
use zibo\library\orm\definition\field\PropertyField;
use zibo\library\orm\query\ModelQuery;
use zibo\library\orm\ModelManager;

/**
 * Form to edit the properties of a content overview widget
 */
abstract class AbstractContentPropertiesForm extends SubmitCancelForm {

   /**
     * Name of the form
     * @var string
     */
    const NAME = 'formContentProperties';

	/**
	 * Name of the model field
	 * @var string
	 */
	const FIELD_MODEL = 'model';

    /**
     * Name of the fields field
     * @var string
     */
    const FIELD_FIELDS = 'fields';

    /**
     * Name of the recursive depth field
     * @var string
     */
    const FIELD_RECURSIVE_DEPTH = 'recursiveDepth';

    /**
     * Name of the include unlocalized field
     * @var string
     */
    const FIELD_INCLUDE_UNLOCALIZED = 'includeUnlocalized';

    /**
     * Name of the view field
     * @var string
     */
    const FIELD_VIEW = 'view';

	/**
	 * Translation key for the yes label
	 * @var string
	 */
	const TRANSLATION_YES = 'label.yes';

	/**
	 * Translation key for the no label
	 * @var string
	 */
	const TRANSLATION_NO = 'label.no';

    /**
     * Translation key for the predefined view type
     * @var string
     */
    const TRANSLATION_VIEW = 'joppa.content.label.view.';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Constructs a new properties form
	 * @param string $action URL where this form will point to
	 * @param joppa\model\Node $node
	 * @param joppa\content\model\ContentProperties $properties
	 * @return null
	 */
	public function __construct($action, Node $node, ContentProperties $properties) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$translator = I18n::getInstance()->getTranslator();
		$modelManager = ModelManager::getInstance();
		$fieldFactory = FieldFactory::getInstance();

		$model = $properties->getModelName();
		$fields = $properties->getModelFields();
		$recursiveDepth = $properties->getRecursiveDepth();
		$includeUnlocalized = $properties->getIncludeUnlocalized();
		$view = $properties->getView();

		$modelField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_MODEL, $model);
		$modelField->setOptions($this->getModelOptions($modelManager));
		$modelField->addEmpty();

		$fieldsField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_FIELDS, $fields);
		$fieldsField->setOptions(self::getModelFieldOptions($modelManager, $model));
		$fieldsField->setIsMultiple(true);

		$recursiveDepthField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_RECURSIVE_DEPTH, $recursiveDepth);
		$recursiveDepthField->setOptions($this->getNumericOptions(0, 5));

		$includeUnlocalizedField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_INCLUDE_UNLOCALIZED, $includeUnlocalized);
		$includeUnlocalizedField->setOptions($this->getIncludeUnlocalizedOptions($translator));

        $viewField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_VIEW, $view);
        $viewField->setOptions($this->getViewOptions($translator));

		$this->addField($modelField);
		$this->addField($fieldsField);
		$this->addField($recursiveDepthField);
		$this->addField($includeUnlocalizedField);
		$this->addField($viewField);
	}

    /**
     * Gets a content properties object for the submitted form
     * @return joppa\content\model\ContentProperties
     */
    public function getContentProperties() {
        $properties = new ContentProperties();
        $properties->setModelName($this->getValue(self::FIELD_MODEL));
        $properties->setRecursiveDepth($this->getValue(self::FIELD_RECURSIVE_DEPTH));
        $properties->setIncludeUnlocalized($this->getValue(self::FIELD_INCLUDE_UNLOCALIZED));
        $properties->setView($this->getValue(self::FIELD_VIEW));

        $fields = $this->getValue(self::FIELD_FIELDS);
        if ($fields) {
            $properties->setModelFields($fields);
        }

        return $properties;
    }

	/**
	 * Gets the options for the model field
	 * @param zibo\library\orm\ModelManager $modelManager Manager of the models
	 * @return array Array with the name of the model as key and as value
	 */
	protected function getModelOptions(ModelManager $modelManager) {
		$models = $modelManager->getModels(true);

		ksort($models);

		$options = array();
		foreach ($models as $modelName => $model) {
			$options[$modelName] = $modelName;
		}

		return $options;
	}

	/**
	 * Gets the fields of a model as options for a form field
	 * @param zibo\library\orm\ModelManager $modelManager Manager of the models
	 * @param string $model Name of the selected model
	 * @param boolean $includeRelationFields
	 * @param boolean $includeHasFields
	 * @return array Array with tne name of the field as key and as value
	 */
	public static function getModelFieldOptions(ModelManager $modelManager, $model, $includeRelationFields = false, $includeHasFields = false, $recursiveDepth = 1) {

		if ($includeRelationFields) {
			$options = array('' => '---');
		} else {
            $options = array();
		}

		if (!$model) {
			return $options;
		}

		$model = $modelManager->getModel($model);
		$meta = $model->getMeta();
		$fields = $meta->getFields();

		foreach ($fields as $fieldName => $field) {
			if (!$includeRelationFields || $field instanceof PropertyField) {
                $options[$fieldName] = $fieldName;
                continue;
			}

			if (!($includeHasFields || $field instanceof BelongsToField)) {
				continue;
			}

			if ($recursiveDepth != '1') {
				$options[$fieldName] = $fieldName;
				continue;
			}

			$relationModel = $meta->getRelationModel($fieldName);
			$relationMeta = $relationModel->getMeta();
			$relationFields = $relationMeta->getFields();

			foreach ($relationFields as $relationFieldName => $relationField) {
				if (!$includeHasFields && $relationField instanceof HasField) {
					continue;
				}

				$name = $fieldName . '.' . $relationFieldName;
				$options[$name] = $name;
			}
		}

		return $options;
	}

	/**
	 * Gets the include unlocalized options
	 * @param zibo\library\i18n\translation\Translator $translator
	 * @return array
	 */
	protected function getIncludeUnlocalizedOptions(Translator $translator) {
		return array(
            '' => $translator->translate(self::TRANSLATION_NO),
            ModelQuery::INCLUDE_UNLOCALIZED_FETCH => $translator->translate(self::TRANSLATION_YES),
		);
	}

   /**
     * Gets the options for the view type
     * @param zibo\library\i18n\translation\Translator $translator
     * @return array
     */
    abstract protected function getViewOptions(Translator $translator);

	/**
	 * Gets numeric options
	 * @param integer $minimum
	 * @param integer $maximum
	 * @return array
	 */
	protected function getNumericOptions($minimum, $maximum) {
		$options = array();
		for ($i = $minimum; $i <= $maximum; $i++) {
			$options[$i] = $i;
		}

		return $options;
	}

}