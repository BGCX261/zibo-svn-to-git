<?php

namespace joppa\content\model;

class PaginationProperties {

	private $url;

	private $pages;

	private $page;

	public function __construct($url, $pages, $page) {
		$this->url = $url;
		$this->pages = $pages;
		$this->page = $page;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getPages() {
		return $this->pages;
	}

	public function getPage() {
		return $this->page;
	}

}