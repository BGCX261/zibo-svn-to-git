<?php

namespace zibo\library\spider;

/**
 * Data container of a node in the spider web
 */
class WebNode {

    /**
     * Type flag for a CSS stylesheet
     * @var string
     */
    const TYPE_CSS = 'css';

    /**
     * Type flag for a external reference
     * @var string
     */
    const TYPE_EXTERNAL = 'external';

    /**
     * Type flag for a image
     * @var string
     */
    const TYPE_IMAGE = 'image';

    /**
     * Type flag for a JS script
     * @var string
     */
    const TYPE_JS = 'js';

    /**
     * Type flag for a mailto link
     * @var string
     */
    const TYPE_MAILTO = 'mailto';

    /**
     * Type flag for a ignored node
     * @var string
     */
    const TYPE_IGNORED = 'ignore';

    /**
     * The URL of this node
     * @var string
     */
    private $url;

    /**
     * The HTTP response from this node
     * @var HttpResponse
     */
    private $response;

    /**
     * Error message which occured when processing this node
     * @var string
     */
    private $error;

    /**
     * Type flags
     * @var array
     */
    private $types;

    /**
     * Nodes which are linked from this node
     * @var array
     */
    private $links;

    /**
     * Nodes which reference this node
     * @var array
     */
    private $references;

    /**
     * Constructs a new node
     * @param string $url URL of this node
     * @return null
     */
    public function __construct($url) {
        $this->url = $url;

        $this->response = null;
        $this->error = null;

        $this->types = array();
        $this->links = array();
        $this->references = array();
    }

    /**
     * Gets the URL of this node
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Sets the response from this node
     * @param HttpResponse $response
     * @return null
     */
    public function setResponse(HttpResponse $response) {
        $this->response = $response;
    }

    /**
     * Gets the response from this node
     * @return HttpResponse
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Sets the error which occured while crawling this node
     * @param string $error Error message which occured while crawling
     * @return null
     */
    public function setError($error) {
        $this->error = $error;
    }

    /**
     * Gets the error which occured while crawling node
     * @param string Error message which occured while crawling
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Adds a type flag to this node
     * @param string $type
     * @return null
     * @see TYPE_EXTERNAL, TYPE_MAILTO
     */
    public function addType($type) {
        $this->types[$type] = $type;
    }

    /**
     * Checks if this node has a certain type flag
     * @param string $type Flag to check
     * @return boolean
     */
    public function hasType($type) {
        return array_key_exists($type, $this->types);
    }

    /**
     * Gets the type flags of this node
     * @return array Array with flags
     * @see TYPE_EXTERNAL, TYPE_MAILTO
     */
    public function getTypes() {
        return $this->types;
    }

    /**
     * Adds a link
     * @param WebNode $link A node which is linked from this node
     * @return null
     */
    public function addLink(WebNode $link) {
        $this->links[$link->getUrl()] = $link;
    }

    /**
     * Gets all the nodes which are linked from this node
     * @return array Array with WebNode objects
     */
    public function getLinks() {
        ksort($this->links);

        return $this->links;
    }

    /**
     * Adds a reference
     * @param WebNode $reference A node which has a link to this node
     * @return null
     */
    public function addReference(WebNode $reference) {
        $this->references[$reference->getUrl()] = $reference;
    }

    /**
     * Gets all the nodes which have a reference to this node
     * @return array Array with WebNode objects
     */
    public function getReferences() {
        ksort($this->references);

        return $this->references;
    }

}