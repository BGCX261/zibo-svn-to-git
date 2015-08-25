<?php

namespace zibo\core\config\io\ini;

use zibo\core\config\Config;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Reads configuration in ini format from the config directory in the Zibo include path
 * (application, modules/*, system)
 */
class IniParser {

    const PARSE_RESERVED_PREFIX = 'ZZZ';
    const PARSE_RESERVED_SUFFIX = 'ZZZ';

    // reserved keys: null, yes, no, true, false, on, off, none
    // reserved chars: {}|&~![()^"
    private $reservedWords = array('null', 'yes', 'no', 'true', 'false', 'on', 'off', 'none',
                                   'NULL', 'YES', 'NO', 'TRUE', 'FALSE', 'ON', 'OFF', 'NONE');

    private $variables;

    public function setVariables(array $variables = null) {
        $this->variables = $variables;
    }

    public function getValuesFromIni(array $ini, array $values) {
        foreach ($ini as $key => $value) {
            if (!is_array($value)) {
                $values = $this->addKey($values, $key, $value);
                continue;
            }

            foreach ($value as $k => $v) {
                if (!isset($values[$key])) {
                    $values[$key] = array();
                }
                $values[$key] = $this->addKey($values[$key], $k, $v);
            }
        }

        return $values;
    }

    public function getWriteOutput($values, $key = null) {
        $output = '';
        if (is_array($values)) {
            foreach ($values as $k => $v) {
                $newKey = is_null($key) ? $k : $key . Config::TOKEN_SEPARATOR . $k;
                $output .= $this->getWriteOutput($v, $newKey);
            }
        } elseif (is_null($key)) {
            throw new ZiboException('Provided key is null and the values are not an array. Make sure values is an array if you leave your key empty.');
        } else {
            if (is_null($values)) {
                return $output;
            } elseif (is_bool($values)) {
                $values = $values === true ? '1' : '0';
            } elseif (!ctype_alnum($values)) {
                $values = addslashes($values);
                $values = '"' . $values . '"';
            }
            $output .= $key . ' = ' . $values . "\n";
        }

        return $output;
    }

    public function addKey(array $values, $key, $value) {
        $tokens = explode(Config::TOKEN_SEPARATOR, $key);

        if (count($tokens) == 1) {
            $values[$key] = $this->parseVariables($value);
            return $values;
        }

        $valueKey = $tokens[0];
        if (!isset($values[$valueKey])) {
            $values[$valueKey] = array();
        }

        unset($tokens[0]);
        $key = implode(Config::TOKEN_SEPARATOR, $tokens);

        $values[$valueKey] = $this->addKey($values[$valueKey], $key, $value);

        return $values;
    }

    private function parseVariables($string) {
        if (!String::isString($string, String::NOT_EMPTY) || !isset($this->variables)) {
            return $string;
        }

        foreach ($this->variables as $variable => $value) {
            $string = str_replace('%' . $variable . '%', $value, $string);
        }

        return $string;
    }

    public function parseReservedWords($string) {
        foreach ($this->reservedWords as $reservedWord) {
            $string = str_replace($reservedWord, self::PARSE_RESERVED_PREFIX . $reservedWord . self::PARSE_RESERVED_SUFFIX, $string);
        }
        return $string;
    }

    public function unparseReservedWords($string) {
        foreach ($this->reservedWords as $reservedWord) {
            $string = str_replace(self::PARSE_RESERVED_PREFIX . $reservedWord . self::PARSE_RESERVED_SUFFIX, $reservedWord, $string);
        }
        return $string;
    }

    public function unparseIniWithReservedWords(array $ini) {
        $newIni = array();
        foreach ($ini as $key => $value) {
            $newKey = $this->unparseReservedWords($key);
            if (is_array($value)) {
                $newValue = $this->unparseIniWithReservedWords($value);
            } else {
                $newValue = $this->unparseReservedWords($value);
            }
            $newIni[$newKey] = $newValue;
        }
        return $newIni;
    }

}