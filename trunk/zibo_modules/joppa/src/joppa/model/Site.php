<?php

namespace joppa\model;

/**
 * Site data container
 */
class Site {

	/**
	 * Id of this site
	 * @var int
	 */
	public $id;

	/**
	 * The node which represents this site
	 * @var int|Node
	 */
	public $node;

	/**
	 * Flag to see whether this is the default site
	 * @var boolean
	 */
	public $isDefault;

	/**
	 * Node of the default page for this site
	 * @var int|Node
	 */
	public $defaultNode;

	/**
	 * Localization method of this site
	 * @var string
	 */
	public $localizationMethod;

	/**
	 * Internal version number for this site
	 * @var int
	 */
	public $version;

}