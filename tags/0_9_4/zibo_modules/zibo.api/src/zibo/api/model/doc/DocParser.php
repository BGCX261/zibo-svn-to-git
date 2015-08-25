<?php

namespace zibo\api\model\doc;

use zibo\api\model\doc\tag\TagParser;

/**
 * Parser to parse doc comment strings into Doc data containers
 */
class DocParser {

    /**
     * Start characters of a doc comment
     * @var string
     */
    const DELIMITER_COMMENT_START = '/**';

    /**
     * Line start character of a doc comment
     * @var string
     */
    const DELIMITER_COMMENT_LINE = '*';

    /**
     * Stop characters of a doc comment
     * @var string
     */
    const DELIMITER_COMMENT_STOP = '*/';

    /**
     * Tag parser
     * @var zibo\api\model\doc\tag\TagParser
     */
    private $tagParser;

    /**
     * Construct a new doc parser
     * @return null
     */
    public function __construct() {
        $this->tagParser = new TagParser();
    }

    /**
     * Parse a doc comment string into a Doc data container
     * @param string $string doc comment string
     * @return zibo\api\model\doc\Doc the provided doc comment string parsed into a Doc data container
     */
    public function parse($string) {
        $lines = $this->getLines($string);

        $doc = new Doc();
        if (!$lines) {
            return $doc;
        }

        $lines = $this->setDescription($doc, $lines);
        $lines = $this->setLongDescription($doc, $lines);

        $this->tagParser->parse($doc, $lines);

        return $doc;
    }

    /**
     * Sets the short description to the Doc object and removes the lines containing it
     * @param zibo\api\model\doc\Doc $doc Doc data container
     * @param array $lines Array with the lines of the doc comment
     * @return array The remaining lines
     */
    private function setDescription(Doc $doc, array $lines) {
        $description = '';

        foreach ($lines as $lineNumber => $line) {
            if (empty($line)) {
                unset($this->lines[$lineNumber]);
                break;
            }

            if ($this->tagParser->getTag($line)) {
                break;
            }

            unset($lines[$lineNumber]);
            $description .= $line . ' ';
        }

        if ($description) {
            $doc->setDescription($description);
        }

        return $lines;
    }

    /**
     * Sets the long description to the Doc object and removes the lines containing it
     * @param zibo\api\model\doc\Doc $doc Doc data container
     * @param array $lines Array with the lines of the doc comment
     * @return array The remaining lines
     */
    private function setLongDescription(Doc $doc, array $lines) {
        $description = '';

        foreach ($lines as $lineNumber => $line) {
            if ($this->tagParser->getTag($line)) {
                break;
            }

            unset($lines[$lineNumber]);
            $description .= $line . ' ';
        }

        if ($description) {
            $doc->setLongDescription($description);
        }

        return $lines;
    }

    /**
     * Get the lines from a doc comment string
     * @param string $string doc comment string
     * @return array Array with the line number as key and the line without the doc comment characters as value
     */
    private function getLines($string) {
        $lines = explode("\n", $string);
        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            $lines[$lineNumber] = $this->removeCommentFromLine($line);
        }

        array_shift($lines);
        array_pop($lines);

        return $lines;
    }

    /**
     * Remove the doc comment characters from a line
     * @param string $line
     * @return string the line withouth the doc comment characters
     */
    private function removeCommentFromLine($line) {
        if ($line && $line[0] == self::DELIMITER_COMMENT_LINE) {
            $line = substr($line, 1);
            return trim($line);
        }

        if ($line == self::DELIMITER_COMMENT_START || $line == self::DELIMITER_COMMENT_STOP) {
            return '';
        }

        return trim($line);
    }

}