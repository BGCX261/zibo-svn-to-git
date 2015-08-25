<?php

namespace zibo\library\orm\model\data\format;

use zibo\library\orm\model\data\format\modifier\DataFormatModifierFacade;
use zibo\library\tokenizer\symbol\NestedSymbol;
use zibo\library\tokenizer\Tokenizer;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Variable of a data format
 */
class DataFormatVariable {

    /**
     * Delimiter for a string value instead of a variable
     * @var string
     */
    const DELIMITER_STRING = '"';

    /**
     * Separator between the fields of a variable
     * @var string
     */
    const SEPARATOR_FIELD = '.';

    /**
     * Separator between the variable and the modifiers
     * @var string
     */
    const SEPARATOR_MODIFIER = '|';

    /**
     * Separator between the modifier and it's arguments
     * @var string
     */
    const SEPARATOR_ARGUMENT = ':';

    /**
     * The format string of the variable (includes the modifiers)
     * @var string
     */
    private $format;

    /**
     * The name of the variable
     * @var string
     */
    private $variable;

    /**
     * Flag to see if the variable is a variable or a string
     * @var boolean
     */
    private $isString;

    /**
     * The modifiers in the format string
     * @var array
     */
    private $modifiers;

    /**
     * Constructs a new data format variable
     * @param string $format Variable format string
     * @return null
     * @throws zibo\ZiboException when the provided format is empty or not a string
     */
    public function __construct($format) {
        $this->setFormat($format);
    }

    /**
     * Gets a variable for the format
     * @param mixed $data Model data object
     * @param string $variableName Name of the variable
     * @return string
     */
    public function getValue($data) {
        if ($this->isString) {
            $value = $this->variable;
        } else {
            $tokens = explode(self::SEPARATOR_FIELD, $this->variable);

            $value = $data;
            foreach ($tokens as $token) {
                if (!is_object($value) || !isset($value->$token)) {
                    return null;
                }

                $value = $value->$token;
            }
        }

        if (!$this->modifiers) {
            return $value;
        }

        $modifierFacade = DataFormatModifierFacade::getInstance();

        foreach ($this->modifiers as $name => $arguments) {
            $value = $modifierFacade->modifyValue($value, $name, $arguments);
        }

        return $value;
    }

    /**
     * Gets the format string of this variable
     * @return string
     */
    public function getFormat() {
        return $this->format;
    }

    /**
     * Sets the format for this data format variable. This will parse the variable for quicker data formatting.
     * @param string $format The variable format string
     * @return null
     * @throws zibo\ZiboException when the provided format is empty or not a string
     */
    private function setFormat($format) {
        if (String::isEmpty($format)) {
            throw new ZiboException('Provided format is empty');
        }

        $tokens = explode(self::SEPARATOR_MODIFIER, $format);

        $this->format = $format;
        $this->variable = array_shift($tokens);
        $this->modifiers = $this->getModifiersFromTokens($tokens);

        $this->isString = false;
        if (substr($this->variable, 0, 1) == self::DELIMITER_STRING && substr($this->variable, -1) == self::DELIMITER_STRING) {
            $this->isString = true;
            $this->variable = substr($this->variable, 1, -1);
        }
    }

    /**
     * Gets the used modifiers from the provided format
     * @param array $tokens Tokens of the modifiers
     * @return array Array with the modifiers
     */
    private function getModifiersFromTokens(array $tokens) {
        if (!$tokens) {
            return array();
        }

        $modifiers = array();
        foreach ($tokens as $token) {
            $arguments = explode(self::SEPARATOR_ARGUMENT, $token);
            $name = array_shift($arguments);
            $modifiers[$name] = $arguments;
        }

        return $modifiers;
    }

}