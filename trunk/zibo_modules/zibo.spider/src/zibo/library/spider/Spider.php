<?php

namespace zibo\library\spider;

use zibo\library\filesystem\File;
use zibo\library\spider\bite\SpiderBite;
use zibo\library\spider\report\SpiderReport;
use zibo\library\xml\dom\Document;
use zibo\library\String;

use \Exception;

/**
 * A spider crawls through a website to gather information about it
 */
class Spider {

    /**
     * The base URL of this spider
     * @var string
     */
    private $baseUrl;

    /**
     * Collection of gathered nodes with their information
     * @var array
     */
    private $web;

    /**
     * The bite implementations to gather information
     * @var array
     */
    private $bites;

    /**
     * Flag to see whether the crawl should be limited to nodes inside the base URL
     * @var boolean
     */
    private $willBiteExternalNodes;

    /**
     * The reports for this spider
     * @var array
     */
    private $reports;

    /**
     * Regular expressions to match URL's which should be ignored
     * @var array
     */
    private $ignore;

    /**
     * Constructs a new spider
     * @param string $baseUrl The base URL to crawl
     * @return null
     */
    public function __construct($baseUrl) {
        $this->baseUrl = $baseUrl;

        $this->web = new Web();
        $this->web->getNode($this->baseUrl);

        $this->bites = array();
        $this->willBiteExternalNodes = false;

        $this->reports = array();
        $this->ignore = array();
    }

    /**
     * Adds a bite to this spider. A bite is a piece of code which will gather information from the nodes.
     * @param zibo\library\spider\bite\SpiderBite $bite The bite to add
     * @return null
     */
    public function addBite(SpiderBite $bite) {
        $this->bites[] = $bite;
    }

    /**
     * Adds a report to this spider. A report shows gathered information when the crawling is done.
     * @param zibo\library\spider\report\SpiderReport $report The report to add
     * @return null
     */
    public function addReport(SpiderReport $report) {
        $this->reports[] = $report;
    }

    /**
     * Adds a regular expression to match URL's which should be ignored
     * @param string $ignoreRegex Regular expression
     * @return null
     */
    public function addIgnoreRegex($ignoreRegex) {
        if (String::isEmpty($ignoreRegex)) {
            throw new ZiboException('Could not add an empty regular expression');
        }

        $this->ignore[] = $ignoreRegex;
    }

    /**
     * Starts the crawling
     * @param integer $delay Delay between each page in miliseconds
     * @param zibo\library\filesystem\File $statusFile File where the status of the crawling process is written
     * @param zibo\library\filesystem\File $cancelFile File which will cancel/stop the crawling process when exists
     * @return null
     */
    public function crawl($delay = 1000, File $statusFile = null, File $cancelFile = null) {
        $prey = $this->web->resetPrey();

        $start = time();
        $index = 0;

        $isCancelled = false;

        while ($prey) {
            if ($cancelFile && $cancelFile->exists()) {
                $cancelFile->delete();

                $isCancelled = true;

                break;
            }

            usleep($delay * 1000);

            $index++;

            $url = $prey->getUrl();

            if ($this->shouldIgnore($url)) {
                $prey->addType(WebNode::TYPE_IGNORED);

                $prey = $this->web->getNextPrey();
                continue;
            }

            if ($statusFile) {
                $status = new SpiderStatus($url, $index, $this->web->countNodes(), $start);
                $status->write($statusFile);
            }

            if (String::startsWith($url, 'mailto:')) {
                $prey->addType(WebNode::TYPE_MAILTO);

                $prey = $this->web->getNextPrey();
                continue;
            }

            try {
                $crawl = new Crawl($url);
                $crawl->performCrawl();

                $response = $crawl->getResponse();

                $prey->setResponse($response);

                if ($response->isRedirect()) {
                    $location = $response->getHeader('Location');

                    if (!String::looksLikeUrl($location)) {
                        if ($location[0] == '/') {
                            $base = $crawl->getBaseUrl();
                        } else {
                            $base = $crawl->getBasePath();
                        }

                        $location = rtrim($base, '/') . '/' . ltrim($location, '/');
                    }

                    if ($url == $location) {
                        throw new Exception('Redirect loop');
                    }

                    $locationNode = $this->web->getNode($location);
                    $locationNode->addReference($prey);

                    $prey->addLink($locationNode);
                }

                if (!String::startsWith($url, $this->baseUrl)) {
                    $prey->addType(WebNode::TYPE_EXTERNAL);

                    if (!$this->willBiteExternalNodes) {
                        $prey = $this->web->getNextPrey();
                        continue;
                    }
                }

                $this->bite($prey, $crawl->getBaseUrl(), $crawl->getBasePath());
            } catch (Exception $exception) {
                if ($crawl) {
                    $response = $crawl->getResponse();

                    if ($response) {
                        $prey->setResponse($response);
                    }
                }

                $prey->setError($exception->getMessage());
            }

            $prey = $this->web->getNextPrey();
        }

        if (!$isCancelled) {
            if ($statusFile) {
                $status = new SpiderStatus("reports", $index, $this->web->countNodes(), $start);
                $status->write($statusFile);
            }

            foreach ($this->reports as $report) {
                $report->setWeb($this->web);
            }
        }

        if ($statusFile) {
            $status = new SpiderStatus(null, $index, $this->web->countNodes(), $start, time());
            $status->write($statusFile);
        }
    }

    /**
     * Checks if the provided URL should be ignored
     * @param string $url URL to check
     * @return boolean True to ignore the url, false otherwise
     */
    private function shouldIgnore($url) {
        foreach ($this->ignore as $ignoreRegex) {
            if (preg_match('/' . str_replace('/', '\\/', $ignoreRegex) . '/', $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Bites a prey to gather the needed information
     * @param WebNode $prey The current node to check
     * @param string $preyBaseUrl The base URL of the prey
     * @param string $preyBasePath The base path of the prey
     * @return null
     */
    private function bite(WebNode $prey, $preyBaseUrl, $preyBasePath) {
        $dom = null;

        $response = $prey->getResponse();
        $contentType = $response->getHeader('Content-Type');
        $content = $response->getContent();

        if (String::startsWith($contentType, 'text/html') && $content) {
            $dom = new Document('1.0', 'utf8');

            try {
                $result = @$dom->loadHTML($content);

                if (!$result) {
                    $error = error_get_last();
                    throw new Exception($error['message']);
                }
            } catch (Exception $exception) {
                $prey->setError($exception->getMessage());
            }
        }

        foreach ($this->bites as $bite) {
            $bite->bite($this->web, $prey, $preyBaseUrl, $preyBasePath, $dom);
        }
    }

    /**
     * Gets the base URL of the spider
     * @return string
     */
    public function getBaseUrl() {
        return $this->baseUrl;
    }

    /**
     * Gets the reports of this spider
     * @return array Array with SpiderReport objects
     */
    public function getReports() {
        return $this->reports;
    }

    /**
     * Gets the web of this spider
     * @return Web Collection of the nodes
     */
    public function getWeb() {
        return $this->web;
    }

}