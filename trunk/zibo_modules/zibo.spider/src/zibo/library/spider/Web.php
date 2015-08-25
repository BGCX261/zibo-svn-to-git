<?php

namespace zibo\library\spider;

/**
 * The web to crawl; a collection of nodes
 */
class Web {

    /**
     * The gathered nodes
     * @var array
     */
    private $nodes;

    /**
     * Constructs a new web
     * @return null
     */
    public function __construct() {
        $this->nodes = array();
    }

    /**
     * Gets a node from the web
     * @param string $url The URL of the node
     * @param boolean $create If true, the node will be created if it does not exist
     * @return WebNode|null The node of the provided URL
     */
    public function getNode($url, $create = true) {
        $url = $this->processUrl($url);

        if (!array_key_exists($url, $this->nodes)) {
            if (!$create) {
                return null;
            }

            $this->nodes[$url] = new WebNode($url);
        }

        return $this->nodes[$url];
    }

    /**
     * Gets all the nodes of this web
     * @return array
     */
    public function getNodes() {
        return $this->nodes;
    }

    /**
     * Removes a node from the web
     * @param string $url The URL of the node to remove
     * @return null
     */
    public function removeNode($url) {
        $url = $this->processUrl($url);

        if (array_key_exists($url, $this->nodes)) {
            unset($this->nodes[$url]);
        }
    }

    /**
     * Gets the number of nodes in this web
     * @return integer
     */
    public function countNodes() {
        return count($this->nodes);
    }

    /**
     * Gets the next node
     * @return WebNode|null The next node or null when the end has been reached
     */
    public function getNextPrey() {
        next($this->nodes);
        return current($this->nodes);
    }

    /**
     * Resets the internal pointer of the nodes
     * @return WebNode The first node
     */
    public function resetPrey() {
        reset($this->nodes);
        return current($this->nodes);
    }

    /**
     * Processes the URL, make sure it has no invalid characters
     * @param string $url The URL to process
     * @return string The processed URL
     */
    private function processUrl($url) {
        return str_replace(' ', '%20', $url);
    }

}