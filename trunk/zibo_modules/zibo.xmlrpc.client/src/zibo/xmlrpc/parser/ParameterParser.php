<?php

namespace zibo\xmlrpc\parser;

use zibo\library\xmlrpc\Value;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Parser for parameter strings into PHP values and visa versa.
 *
 * <p>A parameter string is a string representation of different values of different types.
 * All XML-RPC types can be represented with it. Each parameter is separated by a ','.</p>
 * <ul>
 * <li>A <strong>nil</strong> value is entered as 'null'.</li>
 * <li>A <strong>boolean</strong> is entered as 'true' or 'false'.</li>
 * <li>A <strong>numeric</strong> value is entered plain.</li>
 * <li>A <strong>string</strong> is entered plain and surrounded by a '"'.</li>
 * <li>A <strong>datetime</strong> is entered as a string in the ISO 8601 format</li>
 * <li>A <strong>base64</strong> is entered as a string</li>
 * <li>A <strong>array</strong> is started with '[' and stopped with ']'. Each value is separated by a ','.</li>
 * <li>A <strong>struct</strong> is started with '{' and stopped with '}'. Each value is again separated by a ','. The key from a struct is separated with a ':' from the value.</li>
 * </ul>
 * <p>Arrays and structs can be nested.</p>
 * <p>Some examples:</p>
 * <ul>
 * <li>15, true, "A test string"<br />
 * <br />
 * is the parameter string of<br/>
 * <br />
 * array(15, true, "A test string")<br />
 * <br />
 * </li><li>
 * 5, [15, "test", 47]<br />
 * <br />
 * is the parameter string of<br />
 * <br />
 * array(5, array(15, "test", 47))<br />
 * <br />
 * </li>
 * <li>
 * {url: "http://localhost", values: [10, 15]}<br />
 * <br />
 * is the parameter string of<br />
 * <br />
 * array(array("url" => "http://localhost", "values" => array(10, 15)))
 * </li>
 * </ul>
 */
class ParameterParser {

    /**
     * Token for an array to open
     * @var string
     */
    const ARRAY_OPEN = '[';

    /**
     * Token for an array to close
     * @var string
     */
    const ARRAY_CLOSE = ']';

    /**
     * Boolean false value
     * @var string
     */
    const BOOLEAN_FALSE = 'false';

    /**
     * Boolean true value
     * @var string
     */
    const BOOLEAN_TRUE = 'true';

    /**
     * Indent string
     * @var string
     */
    const INDENT_STRING = '    ';

    /**
     * Nil value
     * @var string
     */
    const NIL = 'null';

    /**
     * Token to open or close a string
     * @var string
     */
    const STRING_DELIMITER = '"';

    /**
     * Token to open a struct
     * @var string
     */
    const STRUCT_OPEN = '{';

    /**
     * Token to close a string
     * @var string
     */
    const STRUCT_CLOSE = '}';

    /**
     * Token to separate a struct key from it value
     * @var string
     */
    const STRUCT_KEY_VALUE_SEPARATOR = ':';

    /**
     * Token to separate different tracks
     * @var string
     */
    const TOKEN_SEPARATOR = ',';

    /**
     * Parses a parameter string into PHP values
     * @param string $value A parameter string
     * @return array Array with the values from the provided parameter string
     */
    public function parse($value) {
        if (String::isEmpty($value)) {
            throw new ZiboException('Provided value is empty');
        }

        $tokens = $this->parseTokens($value);

        $parsedValues = array();
        foreach ($tokens as $token) {
            $parsedValues[] = $this->parseToken($token);
        }

        return $parsedValues;
    }

    /**
     * Parses a parameter string into value tokens
     * @param string $value A parameter string
     * @param boolean $parseOnlySeparator
     * @return array Array with each value from the parameter string as a value in the array
     */
    private function parseTokens($value, $parseOnlySeparator = false) {
//        echo "\nparseTokens('" . $value . "', " . ($parseOnlySeparator ? 1 : 0) . ");";
//        echo "\n             012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789";
//        echo "\n                       1         2         3         4         5         6         7         8\n";
        $startPosition = 0;
        $tokens = array();

        do {
            $separatorPosition = strpos($value, self::TOKEN_SEPARATOR, $startPosition);

//            echo "\nstartPosition: " . $startPosition;
//            echo "\nseparatorPosition: " . $separatorPosition;

            if ($separatorPosition === false) {
                $tokens[] = $value;
                $value = false;
                break;
            }

            if ($separatorPosition === $startPosition) {
                if ($startPosition !== 0) {
                    $tokens[] = trim(substr($value, 0, $separatorPosition));
                }
                $value = trim(substr($value, $startPosition + 1));
                $startPosition = 0;
                continue;
            }

            $stringPosition = strpos($value, self::STRING_DELIMITER);
            $arrayPosition = strpos($value, self::ARRAY_OPEN);
            $structPosition = strpos($value, self::STRUCT_OPEN);

//            echo "\nstringPosition: " . $stringPosition;
//            echo "\narrayPosition: " . $arrayPosition;
//            echo "\nstructPosition: " . $structPosition . "\n";

            $positions = array();
            if ($structPosition !== false && $structPosition < $separatorPosition) {
                $positions[$structPosition] = Value::TYPE_STRUCT;
            }
            if ($stringPosition !== false && $stringPosition < $separatorPosition) {
                $positions[$stringPosition] = Value::TYPE_STRING;
            }
            if ($arrayPosition !== false && $arrayPosition < $separatorPosition) {
                $positions[$arrayPosition] = Value::TYPE_ARRAY;
            }

            if ($positions) {
                ksort($positions);
                list($position, $type) = each($positions);

                if ($type == Value::TYPE_STRUCT) {
//                        echo "\n" . 'handle struct ' . $position . ' - ' . $value;
                    $this->processToken($tokens, $value, $startPosition, $parseOnlySeparator, self::STRUCT_OPEN, self::STRUCT_CLOSE, $position);
                } elseif ($type == Value::TYPE_ARRAY) {
//                        echo "\n" . 'handle array ' . $position . ' - ' . $value;
                    $this->processToken($tokens, $value, $startPosition, $parseOnlySeparator, self::ARRAY_OPEN, self::ARRAY_CLOSE, $position);
                } elseif ($type == Value::TYPE_STRING) {
//                        echo "\n" . 'handle string ' . $position . ' - ' . $value;
                    $this->processToken($tokens, $value, $startPosition, $parseOnlySeparator, self::STRING_DELIMITER, self::STRING_DELIMITER, $position);
                }

                continue;
            }

            $tokens[] = trim(substr($value, 0, $separatorPosition));
            $value = trim(substr($value, $separatorPosition + 1));
        } while ($value != '');

        return $tokens;
    }

    /**
     * Processes a token
     * @param array $tokens Handled tokens, array to add the value of the current token to when $parseOnlySeparator is false
     * @param string $value The string we are parsing
     * @param integer $startPosition The current start position
     * @param boolean $parseOnlySeparator True to update the start position with the close position of the current value, false to add the current value to $tokens and trim the current value from the value string
     * @param string $open Open symbol
     * @param string $close Close symbol
     * @param integer $currentPosition The current parsing position in the value
     * @return null
     */
    private function processToken(&$tokens, &$value, &$startPosition, $parseOnlySeparator, $open, $close, $currentPosition) {
        $closePosition = $this->getClosePosition($value, $open, $close, $currentPosition);
        $closePosition++;

        if ($parseOnlySeparator) {
            $startPosition = $closePosition;
        } else {
            $tokens[] = trim(substr($value, 0, $closePosition));
            $value = trim(substr($value, $closePosition));
        }
    }

    /**
     * Parses a parameter string of a value into a PHP value
     * @param string $token The parameter string of one value
     * @return mixed The PHP value of the provided parameter string
     * @throws zibo\ZiboException when the parameter string is not valid
     */
    private function parseToken($token) {
        $firstChar = $token[0];
        $lastChar = $token[strlen($token) - 1];

        if ($firstChar == self::STRING_DELIMITER) {
            if ($lastChar !== self::STRING_DELIMITER) {
                throw new ZiboException('String ' . $token . ' is not closed');
            }
            $string = substr($token, 1, -1);
            return str_replace('\\' . self::STRING_DELIMITER, self::STRING_DELIMITER, $string);
        }

        if ($firstChar == self::ARRAY_OPEN) {
            if ($lastChar !== self::ARRAY_CLOSE) {
                throw new ZiboException('Array ' . $token . ' is not closed');
            }
            $array = substr($token, 1, -1);
            return $this->parse($array);
        }

        if ($firstChar == self::STRUCT_OPEN) {
            if ($lastChar !== self::STRUCT_CLOSE) {
                throw new ZiboException('Array ' . $token . ' is not closed');
            }
            $struct = substr($token, 1, -1);
            return $this->parseStruct($struct);
        }

        if ($token == self::BOOLEAN_FALSE) {
            return false;
        }
        if ($token == self::BOOLEAN_TRUE) {
            return true;
        }

        if ($token == self::NIL) {
            return null;
        }

        if (!is_numeric($token)) {
            throw new ZiboException('Invalid value detected: ' . $token);
        }

        if ($token % 1 === 0) {
            return intval($token);
        } else {
            return floatval($token);
        }
    }

    /**
     * Parses a parameter string of a struct into a PHP array
     * @param string $token Parameter string containing a struct value
     * @return array The struct as a PHP array
     * @throws zibo\ZiboException when there is no key value separator
     * @throws zibo\ZiboException when a key contains invalid characters
     */
    private function parseStruct($token) {
        $struct = array();

        $tokens = $this->parseTokens($token, true);

        foreach ($tokens as $token) {
            $position = strpos($token, self::STRUCT_KEY_VALUE_SEPARATOR);
            if ($position === false) {
                throw new ZiboException('Struct value ' . $token . ' is invalid: no key value separator found.');
            }

            $key = trim(substr($token, 0, $position));
            if (!preg_match('/^([a-zA-Z0-9_]*)$/', $key)) {
                throw new ZiboException('Struct value ' . $token . ' is invalid: key has invalid characters. Only alphanumeric characters and a underscore are allowed.');
            }

            $value = $this->parse(trim(substr($token, $position + 1)));

            $struct[$key] = $value[0];
        }

        return $struct;
    }

    /**
     * Gets the close position of the provided close symbol
     * @param string $token The string to look in
     * @param string $symbolOpen The symbol to open a nested value
     * @param string $symbolClose The symbol for which we are looking the position of, it's also the symbol to close a nested value
     * @param integer $initialOpenPosition The position of the open symbol for which we are looking the close symbol of
     * @return integer
     * @throws zibo\ZiboException when the symbol is not closed
     */
    private function getClosePosition($token, $symbolOpen, $symbolClose, $initialOpenPosition = 0) {
        $initialOpenPosition++;

        $closePosition = strpos($token, $symbolClose, $initialOpenPosition);
        if ($closePosition === false) {
            throw new ZiboException($symbolOpen . ' opened but not closed for ' . $token);
        }

        $openPosition = strpos($token, $symbolOpen, $initialOpenPosition);
        if ($openPosition === false || $openPosition > $closePosition || ($symbolOpen == $symbolClose && $openPosition == $closePosition)) {
            return $closePosition;
        }

        $openClosePosition = $this->getClosePosition($token, $symbolOpen, $symbolClose, $openPosition);

        return $this->getClosePosition($token, $symbolOpen, $symbolClose, $openClosePosition);
    }

    /**
     * Parses a PHP value into a parameter string
     * @param mixed $value The value to parse into a parameter string
     * @param boolean $format Set to true to add output formatting to the parameter string
     * @param integer $indentLevel Level of indentation for recursive calls with output formatting
     * @return string A parameter string
     * @see parse
     */
    public function unparse($value, $format = false, $indentLevel = 0) {
        if (is_array($value)) {
            return $this->unparseArray($value, $format, $indentLevel);
        }

        return $this->unparseScalarValue($value);
    }

    /**
     * Parses an PHP array into a parameter string
     * @param array $array The array to parse
     * @param boolean $format Set to true to add output formatting to the parameter string
     * @param integer $indentLevel Level of indentation for recursive calls with output formatting
     * @return string A parameter string of the provided array
     */
    private function unparseArray(array $array, $format = false, $indentLevel = 0) {
        $isStruct = false;
        $unparsedValue = '';

        foreach ($array as $key => $value) {
            if ($format) {
                $unparsedValue .= "\n" . $this->getIndentString($indentLevel + 1);
            }

            if (!is_numeric($key)) {
                $isStruct = true;
                $unparsedValue .= $key . self::STRUCT_KEY_VALUE_SEPARATOR . ' ';
            }

            $unparsedValue .= $this->unparse($value, $format, $indentLevel + 2);

            $unparsedValue .= self::TOKEN_SEPARATOR . ' ';
        }
        $unparsedValue = substr($unparsedValue, 0, -2);

        if ($isStruct) {
            $open = self::STRUCT_OPEN;
            $close = self::STRUCT_CLOSE;
        } else {
            $open = self::ARRAY_OPEN;
            $close = self::ARRAY_CLOSE;
        }

        if ($format) {
            $unparsedValue = $open . $unparsedValue . "\n" . $this->getIndentString($indentLevel - 1) . $close;
        } else {
            $unparsedValue = $open . $unparsedValue . $close;
        }

        return $unparsedValue;
    }

    /**
     * Parses a scalar PHP value into a parameter string value
     * @param mixed $value The scalar PHP value
     * @return string A parameter string of the provided value
     * @throws zibo\ZiboException when the provided value could not be parsed
     */
    private function unparseScalarValue($value) {
        if (is_null($value)) {
            return self::NIL;
        }

        if (!is_scalar($value)) {
            throw new ZiboException('Could not unparse value: Provided value is not a scalar value');
        }

        if (is_bool($value)) {
            if ($value) {
                return self::BOOLEAN_TRUE;
            } else {
                return self::BOOLEAN_FALSE;
            }
        }

        if (is_numeric($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = str_replace(self::STRING_DELIMITER, '\\' . self::STRING_DELIMITER, $value);
            return self::STRING_DELIMITER . $value . self::STRING_DELIMITER;
        }

        throw new ZiboException('Could not unparse ' . $value);
    }

    /**
     * Gets a indentation string
     * @param integer $level Number of the indentation level
     * @return string
     */
    private function getIndentString($level) {
        if ($level <= 0) {
            return;
        }

        return str_repeat(self::INDENT_STRING, $level);
    }

}