<?php

namespace zibo\admin\table\decorator\modules;

use zibo\library\html\table\decorator\ActionDecorator;

/**
 * Module decorator to create an uninstall action in the module table
 */
class ReinstallActionDecorator extends ActionDecorator {

    /**
     * Translation key for the label of the action
     * @var string
     */
    const TRANSLATION_LABEL = 'modules.button.reinstall';

    /**
     * Translation key for the confirmation message of this action
     * @var string
     */
    const TRANSLATION_MESSAGE = 'modules.label.reinstall.confirm';

    /**
     * Constructs a new action decorator
     * @param string $href Base URL for the uninstall action
     * @return null
     */
    public function __construct($href) {
        parent::__construct($href, self::TRANSLATION_LABEL, self::TRANSLATION_MESSAGE);
    }

    /**
     * Gets the href attribute for the anchor with the provided cell value
     * @param mixed $value Value of the cell
     * @return string Href attribute for the anchor
     */
    protected function getHrefFromValue($value) {
        if (!is_array($value) && !array_key_exists('path', $value)) {
            return '';
        }

        return $this->href . $value['path'];
    }

    /**
     * Gets the translation of the message
     * @param mixed $value Value of the cell
     * @return string|null The translation of the message to use for the confirmation
     */
    protected function processMessage($value) {
        return $this->translator->translate($this->message, array('module' => $value['name']));
    }

}