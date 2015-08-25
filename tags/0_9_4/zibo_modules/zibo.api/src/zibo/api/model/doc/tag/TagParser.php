<?php

namespace zibo\api\model\doc\tag;

use zibo\api\model\doc\Doc;

/**
 * Parser for the API doc tags
 */
class TagParser {

    /**
     * Start character of a doc tag
     * @var string
     */
    const DELIMITER_TAG_START = '@';

    /**
     * Stop character of a doc tag
     * @var string
     */
    const DELIMITER_TAG_STOP = ' ';

    /**
     * Registered tags
     * @var array
     */
    private $tags = array();

    /**
     * Construct a new parser for doc tags
     * @return null
     */
    public function __construct() {
        $this->registerTag(new AbstractTag());
        $this->registerTag(new AccessTag());
        $this->registerTag(new AuthorTag());
        $this->registerTag(new CopyrightTag());
        $this->registerTag(new DeprecatedTag());
        $this->registerTag(new ExampleTag());
        $this->registerTag(new ExceptionTag());
        $this->registerTag(new GlobalTag());
        $this->registerTag(new IgnoreTag());
        $this->registerTag(new InternalTag());
        $this->registerTag(new LinkTag());
        $this->registerTag(new NameTag());
        $this->registerTag(new PackageTag());
        $this->registerTag(new ParamTag());
        $this->registerTag(new ReturnTag());
        $this->registerTag(new SeeTag());
        $this->registerTag(new SinceTag());
        $this->registerTag(new StaticTag());
        $this->registerTag(new StaticVarTag());
        $this->registerTag(new SubPackageTag());
        $this->registerTag(new ThrowsTag());
        $this->registerTag(new TodoTag());
        $this->registerTag(new VarTag());
        $this->registerTag(new VersionTag());
    }

    /**
     * Parse the tags from the lines array into the Doc data container
     * @param zibo\api\model\doc\Doc $doc Doc data container
     * @param array $lines doc comment lines
     * @return null
     */
    public function parse(Doc $doc, array $lines) {
        $tag = false;
        $tagLines = array();

        foreach ($lines as $line) {
            $lineTag = $this->getTag($line);
            if (!$lineTag) {
                $tagLines[] = $line;
                continue;
            }

            if ($tag) {
                $this->tags[$tag]->parse($doc, $tagLines);
            }

            $tagDoc = self::DELIMITER_TAG_START . $lineTag . self::DELIMITER_TAG_STOP;

            $tag = $lineTag;
            $tagLines = array(substr($line, strlen($tagDoc)));
        }

        if ($tag) {
            $this->tags[$tag]->parse($doc, $tagLines);
        }
    }

    /**
     * Get the tag of a string
     * @param string $string
     * @return boolean|Tag the tag if the string starts with a registered tag, false otherwise
     */
    public function getTag($string) {
        if (!$string || $string[0] != self::DELIMITER_TAG_START) {
            return false;
        }

        $positionStop = strpos($string, self::DELIMITER_TAG_STOP);
        if ($positionStop === false) {
            $tag = substr($string, 1);
        } elseif ($positionStop > 3) {
            $tag = substr($string, 1, $positionStop - strlen(self::DELIMITER_TAG_START));
        } else {
            return false;
        }

        if (!$tag) {
            return false;
        }

        if (!array_key_exists($tag, $this->tags)) {
            return false;
        }

        return $tag;
    }

    /**
     * Registers a tag to this parser
     * @param Tag $tag
     * @return null
     */
    public function registerTag(Tag $tag) {
        $this->tags[$tag->getName()] = $tag;
    }

    /**
     * Unregisters a tag from this parser if it's set
     * @param string $tagName
     * @return null
     */
    public function unregisterTag($tagName) {
        if (isset($this->tags[$tagName])) {
            unset($this->tags[$tagName]);
        }
    }

}