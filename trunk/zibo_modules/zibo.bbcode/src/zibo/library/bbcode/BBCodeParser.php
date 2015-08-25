<?php

namespace zibo\library\bbcode;

use zibo\library\bbcode\tag\AlignTag;
use zibo\library\bbcode\tag\BoldTag;
use zibo\library\bbcode\tag\CenterTag;
use zibo\library\bbcode\tag\CodeTag;
use zibo\library\bbcode\tag\ColorTag;
use zibo\library\bbcode\tag\ImageTag;
use zibo\library\bbcode\tag\ItalicTag;
use zibo\library\bbcode\tag\ListTag;
use zibo\library\bbcode\tag\QuoteTag;
use zibo\library\bbcode\tag\SizeTag;
use zibo\library\bbcode\tag\StrikeTag;
use zibo\library\bbcode\tag\SubscriptTag;
use zibo\library\bbcode\tag\SuperscriptTag;
use zibo\library\bbcode\tag\Tag;
use zibo\library\bbcode\tag\UnderlineTag;
use zibo\library\bbcode\tag\UrlTag;
use zibo\library\bbcode\tag\YoutubeTag;
use zibo\library\String;

use zibo\ZiboException;

use \Exception;

/**
 * Parser for BBCode
 */
class BBCodeParser {

    /**
     * Tags to process
     * @var array
     */
    private $tags;

    /**
     * Constructs a new BBCode parser
     * @param array $tags Tags to process
     * @return null
     */
    public function __construct(array $tags = null) {
        if ($tags === null) {
            $this->setDefaultTags();
        } else {
            $this->tags = $tags;
        }
    }

    /**
     * Adds a tag to process
     * @param zibo\library\bbcode\tag\Tag $tag Tag to add
     * @return null
     */
    public function addTag(Tag $tag) {
        $this->tags[$tag->getTagName()] = $tag;
    }

    /**
     * Removes a tag to provess
     * @param string $name Name of the tag
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or invalid
     * @throws zibo\ZiboException when the tag is not added to the parser
     */
    public function removeTag($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided tag name is empty');
        }

        if (!array_key_exists($name, $this->tags)) {
            throw new ZiboException('Cannot remove tag ' . $name . ': not added to this parser');
        }

        unset($this->tags[$name]);
    }

    /**
     * Parses the BBCode in the provided string to HTML
     * @param string $string String to parsed
     * @return string Parsed string
     */
    public function parse($string) {
        if (String::isEmpty($string)) {
            return '';
        }

        $string= htmlentities($string);
//        $string = htmlspecialchars($string);
//        $string = strip_tags($string);

        if (strrpos($string, Tag::CLOSE) !== false) {
            $string = $this->processTags($string);
        }

        $string = nl2br($string);

        return $string;
    }

    /**
     * Parses the set tags on the provided string
     * @param string $string String to parse
     * @return string Parsed string
     */
    private function processTags($string) {
        $index = 0;
        $tokens = array();

        do {
            $positionOpen = strpos($string, Tag::OPEN, $index);
            if ($positionOpen === false) {
                continue;
            }

            $positionClose = strpos($string, TAG::CLOSE, $positionOpen);
            if (!$positionClose) {
                continue;
            }

            $tagContent = substr($string, $positionOpen + 1, $positionClose - $positionOpen - 1);

            try {
                $tagParser = new BBCodeTag($tagContent);
            } catch (ZiboException $exception) {
                $index = $positionClose;
                continue;
            }

            $tagName = $tagParser->getTagName();

            if ($tagParser->isCloseTag() || !array_key_exists($tagName, $this->tags)) {
                $index = $positionClose;
                continue;
            }

            $tag = $this->tags[$tagName];

            if ($tag->hasCloseTag()) {
                $closeTag = Tag::OPEN . Tag::CLOSE_PREFIX . $tagName . Tag::CLOSE;

                $positionCloseTag = strpos($string, $closeTag, $positionClose);
                if ($positionCloseTag === false) {
                    $index = $positionClose;
                    continue;
                }

                $content = substr($string, $positionClose + 1, $positionCloseTag - $positionClose - 1);
                if ($tag->needsContentParsing()) {
                    $content = $this->processTags($content);
                }

                try {
                    $parsed = $tag->parseTag($content, $tagParser->getParameters());
                    if ($parsed === false) {
                        throw new ZiboException('Could not parse tag ' . $tagName);
                    }
                } catch (Exception $exception) {
                    $index = $positionClose;
                    continue;
                }

                $tokens[] = substr($string, 0, $positionOpen);
                $tokens[] = $parsed;

                $string = substr($string, $positionCloseTag + strlen($closeTag));
                $index = 0;
            } else {
                try {
                    $parsed = $tag->parseTag('', $tagParser->getParameters());
                    if ($parsed === false) {
                        throw new ZiboException('Could not parse tag ' . $tagName);
                    }
                } catch (Exception $exception) {
                    $index = $positionClose;
                    continue;
                }

                $tokens[] = substr($string, 0, $positionOpen);
                $tokens[] = $parsed;

                $string = substr($string, $positionClose);
                $index = 0;
            }
        } while ($positionOpen !== false && $positionClose !== false);

        $tokens[] = $string;

        return implode('', $tokens);
    }

    /**
     * Adds the default tags to this parser
     * @return null
     */
    private function setDefaultTags() {
        $this->addTag(new BoldTag());
        $this->addTag(new ItalicTag());
        $this->addTag(new ColorTag());
        $this->addTag(new SizeTag());
        $this->addTag(new StrikeTag());
        $this->addTag(new SubscriptTag());
        $this->addTag(new SuperscriptTag());
        $this->addTag(new UnderlineTag());

        $this->addTag(new AlignTag());
        $this->addTag(new CenterTag());
        $this->addTag(new CodeTag());
        $this->addTag(new QuoteTag());

        $this->addTag(new ListTag());

        $this->addTag(new UrlTag());
        $this->addTag(new ImageTag());
        $this->addTag(new YoutubeTag());
    }

}