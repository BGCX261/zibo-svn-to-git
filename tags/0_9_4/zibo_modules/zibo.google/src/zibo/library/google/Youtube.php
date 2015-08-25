<?php

namespace zibo\library\google;

use zibo\library\Number;
use zibo\library\String;

use zibo\ZiboException;

/**
 * HTML element for a YouTube video
 */
class Youtube {

    /**
     * Default width of a video
     * @var integer
     */
    const DEFAULT_WIDTH = 425;

    /**
     * Default height of a video
     * @var integer
     */
	const DEFAULT_HEIGHT = 344;

	/**
	 * Id of the video
	 * @var string
	 */
	private $videoId;

	/**
	 * Width of the video
	 * @var integer
	 */
	private $width;

	/**
	 * Height of the video
	 * @var itneger
	 */
	private $height;

	/**
	 * Constructs a new YouTube object
	 * @param string $url URL or id of the video
	 * @return null
	 */
    public function __construct($url) {
    	$this->width = self::DEFAULT_WIDTH;
    	$this->height = self::DEFAULT_HEIGHT;
    	$this->videoId = $this->parseUrl($url);
    }

    /**
     * Parses the video id out of the provided URL
     * @param string $url URL or id of the video
     * @return null
     * @throws zibo\ZiboException when the provided URL or video id is invalid
     */
    private function parseUrl($url) {
    	if (String::isEmpty($url)) {
    		throw new ZiboException('Provided url is empty');
    	}

    	$parsedUrl = @parse_url($url);
    	if ($parsedUrl === false) {
    		throw new ZiboException('Provided url ' . $url . ' is invalid');
    	}

    	if (!array_key_exists('host', $parsedUrl)) {
    	    if (strpos($url, '/') || strpos($url, ' ')) {
    	        throw new ZiboException('Provided video id is invalid');
    	    }
    	    return $url;
    	}

    	if (strpos($parsedUrl['host'], 'youtube') === false) {
    		throw new ZiboException('Provided url ' . $url . ' does not point to the YouTube site');
    	}

    	if ($parsedUrl['path'] == '/watch' && isset($parsedUrl['query'])) {
    		$parameters = array();
    		parse_str($parsedUrl['query'], $parameters);
    		if (isset($parameters['v'])) {
    			return $parameters['v'];
    		}
    	} elseif (strpos($parsedUrl['path'], '/v/') === 0) {
    		$id = substr($parsedUrl['path'], 3);

    		$positionAmp = strpos($id, '&');
    		$positionQuestionMark = strpos($id, '?');
    		if ($positionAmp !== false && $positionQuestionMark !== false) {
    			$position = min($positionAmp, $positionQuestionMark);
    			$id = substr($id, 0, $position);
    		} elseif ($positionAmp !== false) {
    			$id = substr($id, 0, $positionAmp);
    		} elseif ($positionQuestionMark !== false) {
    			$id = substr($id, 0, $positionQuestionMark);
    		}

    		return $id;
    	}

    	throw new ZiboException('Could not parse the video id out of url ' . $url);
    }

    /**
     * Gets the id of this video
     * @return string
     */
    public function getVideoId() {
    	return $this->videoId;
    }

    /**
     * Gets the URL of this video
     * @return string
     */
    public function getUrl() {
        $id = $this->getVideoId();
        return 'http://www.youtube.com/watch?v=' . $id;
    }

    /**
     * Sets the width of the video
     * @param integer $width Width of the video in pixels
     * @return null
     */
    public function setWidth($width) {
		if (Number::isNegative($width)) {
			throw new ZiboException('Provided width is invalid');
		}

        $this->width = $width;
    }

    /**
     * Sets the height for this video
     * @param integer $height Height of the video in pixels
     * @return null
     */
    public function setHeight($height) {
		if (Number::isNegative($height)) {
			throw new ZiboException('Provided height is invalid');
		}

        $this->height = $height;
    }

    /**
     * Gets the HTML to embed this video
     * @return string
     */
	public function getHtml() {
		$url = 'http://www.youtube.com/v/' . $this->videoId . '&hl=en&fs=1';

        $html = '<object width="' . $this->width . '" height="' . $this->height . '">';
        $html .= '<param name="movie" value="' . $url . '"></param>';
        $html .= '<param name="allowFullScreen" value="true"></param>';
        $html .= '<param name="allowscriptaccess" value="always"></param>';
        $html .= '<embed src="' . $url . '" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="' . $this->width . '" height="' . $this->height . '"></embed>';
        $html .= '</object>';

        return $html;
	}

}