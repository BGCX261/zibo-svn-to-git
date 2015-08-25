<?php

namespace zibo\tinymce\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\Zibo;

use zibo\library\String;

use zibo\tinymce\view\TinyMCEListView;

use zibo\ZiboException;

/**
 * Controller for dynamic image and link list
 */
class TinyMCEController extends AbstractController {

    /**
     * Event to generate the image list
     * @var string
     */
	const EVENT_PRE_IMAGE_LIST = 'tinymce.list.image.pre';

	/**
	 * Event to generate the link list
	 * @var string
	 */
	const EVENT_PRE_LINK_LIST = 'tinymce.list.link.pre';

	/**
	 * Name of the images variable
	 * @var string
	 */
	const VAR_IMAGES = 'tinyMCEImageList';

	/**
	 * Name of the links variable
	 * @var string
	 */
	const VAR_LINKS = 'tinyMCELinkList';

	/**
	 * Values for the images variable
	 * @var array
	 */
	private $images = array();

	/**
	 * Values for the links variable
	 * @var array
	 */
	private $links = array();

	/**
	 * Action to set the dynamic images list to the view
	 *
	 * Before setting the view, the event EVENT_PRE_IMAGE_LIST will be executed with this controller as argument.
	 * Use this event to attach images to the dynamic images list.
	 * @return null
	 */
	public function imagesAction() {
		Zibo::getInstance()->runEvent(self::EVENT_PRE_IMAGE_LIST, $this);
		$view = new TinyMCEListView(self::VAR_IMAGES, $this->getImages());
		$this->response->setView($view);
	}

	/**
	 * Action to set the dynamic links list to the view
     *
     * Before setting the view, the event EVENT_PRE_LINK_LIST will be executed with this controller as argument.
     * Use this event to attach links to the dynamic links list.
	 * @return null
	 */
	public function linksAction() {
		Zibo::getInstance()->runEvent(self::EVENT_PRE_LINK_LIST, $this);
		$view = new TinyMCEListView(self::VAR_LINKS, $this->getLinks());
		$this->response->setView($view);
	}

	/**
     * Adds an image to the dynamic images list
     * @param string $image URL to the image
     * @param string $label label for the image
     * @return null
	 */
	public function addImage($image, $label = null) {
		if (String::isEmpty($image)) {
			throw new ZiboException('Provided image is empty');
		}
		if (String::isEmpty($label)) {
			$label = $image;
		}
		$this->images[$image] = $label;
	}

	/**
	 * Gets all the images of the dynamic images list
	 * @return array Array with the URL as key and the label as value
	 */
	public function getImages() {
		return $this->images;
	}

    /**
     * Adds a link to the dynamic links list
     * @param string $link URL of the link
     * @param string $label label for the link
     * @return null
     */
	public function addLink($link, $label = null) {
		if (String::isEmpty($link)) {
			throw new ZiboException('Provided link is empty');
		}
		if (String::isEmpty($label)) {
			$label = $link;
		}
		$this->links[$link] = $label;
	}

    /**
     * Gets all the links of the dynamic links list
     * @return array Array with the URL as key and the label as value
     */
	public function getLinks() {
		return $this->links;
	}

}