<?php

namespace zibo\library\html\form\field;

use zibo\core\view\HtmlView;
use zibo\core\Zibo;

use zibo\library\emoticons\EmoticonParser;
use zibo\library\html\Image;

use zibo\ZiboException;

/**
 * Text field with bbcode toolbar
 */
class BBCodeField extends TextField {

	const SCRIPT_BBCODE = 'web/scripts/bbcode.js';

	const STYLE_BBCODE = 'web/styles/bbcode.css';

	private $bbcode = array(
        'b' => array('open' => '[b]', 'close' => '[/b]', 'image' => 'web/images/bbcode/bold.png'),
        'i' => array('open' => '[i]', 'close' => '[/i]', 'image' => 'web/images/bbcode/italic.png'),
        'u' => array('open' => '[u]', 'close' => '[/u]', 'image' => 'web/images/bbcode/underline.png'),
        's' => array('open' => '[s]', 'close' => '[/s]', 'image' => 'web/images/bbcode/strike.png'),
        'align left' => array('open' => '[align=left]', 'close' => '[/align]', 'image' => 'web/images/bbcode/align.left.png'),
        'align center' => array('open' => '[align=center]', 'close' => '[/align]', 'image' => 'web/images/bbcode/align.center.png'),
        'align right' => array('open' => '[align=right]', 'close' => '[/align]', 'image' => 'web/images/bbcode/align.right.png'),
        'list' => array('open' => "[list]\\n[*]\\n", 'close' => "[*]\\n[*]\\n[/list]", 'image' => 'web/images/bbcode/list.png'),
        'url' => array('open' => '[url]', 'close' => '[/url]', 'image' => 'web/images/bbcode/url.png'),
        'email' => array('open' => '[email]', 'close' => '[/email]', 'image' => 'web/images/bbcode/email.png'),
        'img' => array('open' => '[img]', 'close' => '[/img]', 'image' => 'web/images/bbcode/img.png'),
        'youtube' => array('open' => '[youtube]', 'close' => '[/youtube]', 'image' => 'web/images/bbcode/youtube.png'),
	);

	private $emoticonParser;

    public function __construct($name, $defaultValue = null, $isDisabled = false) {
    	parent::__construct($name, $defaultValue, $isDisabled);

    	$zibo = Zibo::getInstance();
		$zibo->registerEventListener(Zibo::EVENT_PRE_RESPONSE, array($this, 'preResponse'));
	}

	public function setEmoticonParser(EmoticonParser $emoticonParser) {
		$this->emoticonParser = $emoticonParser;
	}

	public function getEmoticonParser() {
		return $this->emoticonParser;
	}

	public function getHtml() {
		$html = parent::getHtml();

		$id = $this->getId();

		$ep = $this->getEmoticonParser();
        if ($ep) {
        	$emoticons = $ep->getEmoticons();
            $toolbar = '<div id="' . $id . 'EmoticonToolbar" class="bbcodeToolbar">';
            $images = array();
            foreach ($emoticons as $emoticon => $image) {
            	if (in_array($image, $images)) {
            		continue;
            	}

            	$images[] = $image;

            	$image = new Image($image);
            	$image->setAttribute('alt', $emoticon);
            	$image->setAttribute('title', $emoticon);

            	$emoticon = addslashes($emoticon);

            	$toolbar .= '<a href="#" onclick="return bbcodeAdd(\'' . $id . "', ' " . $emoticon . '\');">';
            	$toolbar .= $image->getHtml();
            	$toolbar .= '</a> ';
            }
            $toolbar .= '</div>';

            $html = $toolbar . $html;
        }

        $toolbar = '<div id="' . $id . 'BBCodeToolbar" class="bbcodeToolbar">';
        foreach ($this->bbcode as $code => $bbcode) {
            $open = $bbcode['open'];
            if (isset($bbcode['close'])) {
               $close = $bbcode['close'];
            } else {
                $close = false;
            }

            $image = new Image($bbcode['image']);
            $image->setAttribute('alt', $code);
            $image->setAttribute('title', $code);

            if (!$close) {
                $toolbar .= '<a href="#" onclick="return bbcodeAdd(\'' . $id . "', '" . $open . '\');">';
            } else {
                $toolbar .= '<a href="#" onclick="return bbcodeAdd(\'' . $id . "', '" . $open . "', '" . $close . '\');">';
            }
            $toolbar .= $image->getHtml();
            $toolbar .= '</a> ';
        }
        $toolbar .= '</div>';

        $html = $toolbar . $html;

		return $html;
	}

	public function preResponse() {
		$response = Zibo::getInstance()->getResponse();
		$view = $response->getView();
		if (!($view instanceof HtmlView)) {
			return;
		}

		$view->addStyle(self::STYLE_BBCODE);
		$view->addJavascript(self::SCRIPT_BBCODE);
	}

}