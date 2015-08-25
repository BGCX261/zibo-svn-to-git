<?php

namespace zibo\library\bbcode;

use zibo\library\bbcode\tag\Tag;
use zibo\library\tokenizer\symbol\SimpleSymbol;
use zibo\library\tokenizer\symbol\NestedSymbol;
use zibo\library\tokenizer\Tokenizer;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Tag parser for the BBCodeParser
 */
class BBCodeTag {

    /**
     * Name of the tag
     * @var string
     */
    private $tag;

    /**
     * Parameters for the tag
     * @var array
     */
    private $parameters;

    /**
     * Flag to see if this is a closing tag
     * @var boolean
     */
    private $isCloseTag;

    /**
     * Constructs a new tag
     * @param string $tagContent The content between []
     * @return null
     * @throws zibo\ZiboException when the provided tag content is empty or invalid
     */
    public function __construct($tagContent) {
        $this->parseTagContent($tagContent);
    }

    /**
     * Gets the name of the tag
     * @return string
     */
    public function getTagName() {
        return $this->tag;
    }

    /**
     * Gets the parameters of the tag
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * Gets whether this tag is a close tag or not
     * @return boolean True if this tag is a closing tag, false if this tag is a opening tag
     */
    public function isCloseTag() {
        return $this->isCloseTag;
    }

    /**
     * Parses the tag content to split the tag from the parameters
     * @param string $tagContent
     * @return null
     * @throws zibo\ZiboException when the provided tag content is empty or invalid
     */
    private function parseTagContent($tagContent) {
        if (String::isEmpty($tagContent)) {
            throw new ZiboException('Provided tag content is empty');
        }

        $tokens = $this->tokenize($tagContent);

        $tag = array_shift($tokens);
        $parameters = array();

        if ($this->parseArgument($tag, $key, $value)) {
            $tag = $key;
            $parameters[] = $value;
        }

        foreach ($tokens as $token) {
            if (!$this->parseArgument($token, $key, $value)) {
                continue;
            }

            $parameters[strtolower($key)] = $value;
        }

        $this->isCloseTag = false;
        if (substr($tag, 0, 1) == Tag::CLOSE_PREFIX) {
            $tag = substr($tag, 1);
            $this->isCloseTag = true;
        }

        $this->tag = strtolower($tag);
        $this->parameters = $parameters;
    }

    /**
     * Gets the key and value from the argument value
     * @param string $argument The argument to parse
     * @param string $key The parsed key
     * @param string $value The parsed value
     * @return boolean True if the argument has been parsed, false if no parameter has been found
     */
    private function parseArgument($argument, &$key, &$value) {
        $positionSeparator = strpos($argument, '=');
        if (!$positionSeparator) {
            return false;
        }

        $key = substr($argument, 0, $positionSeparator);
        $value = substr($argument, $positionSeparator + 1);

        return true;
    }

    /**
     * Tokenizes the tag content in argument tokens
     *
     * eg. img=150x100 alt="Image description" width=150
     * array {
     *     "img=150x100",
     *     "alt=Image Description",
     *     "width=150",
     * }
     *
     * @param string $tagContent
     * @return array
     */
    private function tokenize($tagContent) {
        $positionApo = strpos($tagContent, '"');
        if (!$positionApo) {
            return explode(' ', $tagContent);
        }

        $tokens = array();

        $tokenizer = new Tokenizer();
        $tokenizer->addSymbol(new SimpleSymbol(' ', false));
        $tokenizer->addSymbol(new NestedSymbol('"', '"', null, true));

        $isStringArgument = false;
        $previousToken = null;
        $parsedTokens = $tokenizer->tokenize($tagContent);
        foreach ($parsedTokens as $token) {
            if ($token == '"') {
                $isStringArgument = !$isStringArgument;

                continue;
            }

            if ($isStringArgument) {
                $tokens[count($tokens) - 1] .= $token;
            } else {
                $tokens[] = $token;
            }
        }

        return $tokens;
    }

}