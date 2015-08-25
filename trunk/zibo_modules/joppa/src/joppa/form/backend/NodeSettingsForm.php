<?php

namespace joppa\form\backend;

use joppa\model\NodeSettings;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\ValidationError;
use zibo\library\validation\exception\ValidationException;

use zibo\ZiboException;

/**
 * Form edit the a NodeSettings object through an ini editor
 */
class NodeSettingsForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
	const NAME = 'formNodeSettings';

	/**
	 * Name of the id field
	 * @var string
	 */
	const FIELD_ID = 'id';

	/**
	 * Name of the settings field
	 * @var string
	 */
	const FIELD_SETTINGS = 'settings';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * The node settings which this form represents
	 * @var joppa\model\NodeSettings
	 */
	private $nodeSettings;

	/**
     * Construct this form
     * @param string $action url where this form will point to
     * @param joppa\model\NodeSettings the node settings which this form represents
	 */
	public function __construct($action, NodeSettings $nodeSettings) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$this->nodeSettings = $nodeSettings;

		$factory = FieldFactory::getInstance();

		$this->addField($factory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_ID));
		$this->addField($factory->createField(FieldFactory::TYPE_TEXT, self::FIELD_SETTINGS));

        $this->setValue(self::FIELD_ID, $this->nodeSettings->getNode()->id);
        $this->setValue(self::FIELD_SETTINGS, $this->nodeSettings->getIniString());
	}

	/**
	 * Get the node settings from this form
	 * @param boolean $updateValues set to true to get the submitted node settings, false to get the initial node settings
	 * @return joppa\model\NodeSettings
	 * @throws zibo\ZiboException when the form is not submitted and $updateValues is set to true
	 * @throws zibo\library\validation\exception\ValidationException when the submitted settings are not valid and $updateValues is set to true
	 */
	public function getNodeSettings($updateValues = true) {
		if (!$updateValues) {
    		return $this->nodeSettings;
		}

		if (!$this->isSubmitted()) {
            throw new ZiboException('Form not submitted');
		}

        $settings = @parse_ini_string($this->getValue(self::FIELD_SETTINGS));

        if ($settings === false) {
            $error = error_get_last();
            $error = new ValidationError('error', '%error%', array('error' => $error['message']));

            $exception = new ValidationException();
            $exception->addErrors(self::FIELD_SETTINGS, array($error));

            throw $exception;
        }

        $nodeSettings = new NodeSettings($this->nodeSettings->getNode(), $this->nodeSettings->getInheritedNodeSettings());

        // set the values from the form
        $inheritPrefixLength = strlen(NodeSettings::INHERIT_PREFIX);
        foreach ($settings as $key => $value) {
        	$inherit = false;

        	if (strlen($key) > $inheritPrefixLength && strncmp($key, NodeSettings::INHERIT_PREFIX, $inheritPrefixLength) == 0) {
                $key = substr($key, $inheritPrefixLength);
                $inherit = true;
        	}

            $nodeSettings->set($key, $value, $inherit);
        }

        return $nodeSettings;
	}

}