<?php

namespace zibo\library\emoticons;

use zibo\library\html\Image;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Parser to replace emoticons in a text with an image
 */
class EmoticonParser {

    /**
     * Array with the available emoticons. The emoticon as key and the url to the image of the emoticon as value.
     * @var array
     */
    private $emoticons;

    /**
     * Construct this emoticon parser
     * @param array $emoticons optional emoticon array: the emoticon as key and the url to the image of the emoticon as value
     * @return null
     */
    public function __construct(array $emoticons = null) {
        if (!$emoticons) {
            $emoticons = $this->getDefaultEmoticons();
        }
        $this->emoticons = $emoticons;
    }

    /**
     * Replace the emoticons in the provided text with their images set in this parser
     * @param string $text
     * @return string the provided text with the emoticons replaced with their images (in html format)
     */
    public function parse($text) {
        foreach ($this->emoticons as $emoticon => $emoticonUrl) {
            $image = new Image($emoticonUrl);
            $image = $image->getHtml();

            $text = str_replace($emoticon, $image, $text);
            $text = str_replace(htmlspecialchars($emoticon), $image, $text);
        }

        return $text;
    }

    /**
     * Get an array with all the emoticons
     * @return array Array with the emoticon as key and the url to the image of the emoticon as value
     */
    public function getEmoticons() {
        return $this->emoticons;
    }

    /**
     * Get the url of the image of the provided emoticon
     * @param string $emoticon the emoticon
     * @return string absolute or relative url to the image of the provided emoticon
     * @throws zibo\ZiboException when an invalid emoticon is provided or when the emoticon is not set in this parser
     */
    public function getEmoticonImage($emoticon) {
        if (String::isEmpty($emoticon)) {
            throw new ZiboException('Provided emoticon is empty');
        }
        if (!array_key_exists($emoticon, $this->emoticons)) {
            throw new ZiboException('Could not find an image for ' . $emoticon);
        }

        return $this->emoticons[$emoticon];
    }

    /**
     * Set a emoticon to this parser
     * @param string $emoticon the emoticon
     * @param string $url absolute or relative url to the image of the emoticon
     * @return null
     */
    public function setEmoticonImage($emoticon, $url) {
        $this->emoticons[$emoticon] = $url;
    }

    /**
     * Get the default emoticons for when no emoticons are passed in the constructor of this emoticon parser.
     * @return array Array with the emoticon as key and the url of the emoticon image as value
     */
    protected function getDefaultEmoticons() {
        $emoticons = array(
            '>-D' => 'web/images/emoticons/evilgrin.png',
            '>D' => 'web/images/emoticons/evilgrin.png',
            ':-D' => 'web/images/emoticons/grin.png',
            ':D' => 'web/images/emoticons/grin.png',
            ':-d' => 'web/images/emoticons/grin.png',
            ':d' => 'web/images/emoticons/grin.png',
            '8-)' => 'web/images/emoticons/happy.png',
            '8)' => 'web/images/emoticons/happy.png',
            ':-)' => 'web/images/emoticons/smile.png',
            ':)' => 'web/images/emoticons/smile.png',
            ';-)' => 'web/images/emoticons/wink.png',
            ';)' => 'web/images/emoticons/wink.png',
            ':-O' => 'web/images/emoticons/surprised.png',
            ':O' => 'web/images/emoticons/surprised.png',
            ':-o' => 'web/images/emoticons/surprised.png',
            ':o' => 'web/images/emoticons/surprised.png',
            ':-P' => 'web/images/emoticons/tongue.png',
            ':P' => 'web/images/emoticons/tongue.png',
            ':-p' => 'web/images/emoticons/tongue.png',
            ':p' => 'web/images/emoticons/tongue.png',
            ':-(' => 'web/images/emoticons/unhappy.png',
            ':(' => 'web/images/emoticons/unhappy.png',
        );

        return $emoticons;
    }

}