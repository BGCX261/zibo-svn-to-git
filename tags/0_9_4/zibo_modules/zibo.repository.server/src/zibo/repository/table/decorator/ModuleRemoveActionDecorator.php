<?php

namespace zibo\repository\table\decorator;

use zibo\library\html\table\decorator\ConfirmAnchorDecorator;
use zibo\library\i18n\I18n;

/**
 * Table decorator to generate a remove link for a module
 */
class ModuleRemoveActionDecorator extends ConfirmAnchorDecorator {

    /**
     * Translation key for the confirmation message
     * @var unknown_type
     */
    const TRANSLATION_CONFIRM = 'repository.confirm.version.remove';

    /**
     * Translation key for the label of the remove button
     * @var string
     */
    const TRANSLATION_REMOVE = 'button.delete';

    /**
     * Translator for the button and the confirmation message
     * @var string
     */
    private $translator;

    /**
     * Constructs a new module decorator
     * @param string $action URL where the anchor should point
     * @return null
     */
    public function __construct($action) {
        parent::__construct($action, self::TRANSLATION_CONFIRM);

        $this->translator = I18n::getInstance()->getTranslator();
        $this->label = $this->translator->translate(self::TRANSLATION_REMOVE);
    }

    /**
     * Gets the label for the anchor
     * @param mixed $value Value of the cell
     * @return string Label for the anchor
     */
    protected function getLabelFromValue($value) {
        return $this->label;
    }

    /**
     * Gets the href attribute for the anchor
     * @param mixed $value Value of the cell
     * @return string Href attribute for the anchor
     */
    protected function getHrefFromValue($value) {
        $namespace = $value->getNamespace();
        $name = $value->getName();
        $version = $value->getVersion();

        $params = $namespace . '/' . $name . '/' . $version;

        return $this->href . $params;
    }

    /**
     * Hook to process the message with the value of the cell
     * @param mixed $value Value of the cell
     * @return string|null The message to use for the confirmation, null for no confirmation
     */
    protected function processMessage($value) {
        return $this->translator->translate($this->message, array('version' => $value->getVersion()));
    }

}