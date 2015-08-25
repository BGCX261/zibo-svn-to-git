<?php

namespace zibo\library\validation\filter;

/**
 * Trim filter for a multiline string, will trim and remove empty lines
 */
class TrimLinesFilter extends AbstractFilter {

    /**
     * Trims the lines of the value and remove the empty lines
     * @param string $value
     * @return string
     */
    public function filter($value) {
        if (!is_string($value)) {
            return $value;
        }

        $lines = explode("\n", $value);

        $value = '';
        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $value .= ($value ? "\n" : '') . $line;
        }

        return $value;
    }

}