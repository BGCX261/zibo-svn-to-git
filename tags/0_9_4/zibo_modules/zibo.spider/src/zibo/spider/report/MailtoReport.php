<?php

namespace zibo\spider\report;

use zibo\library\i18n\translation\Translator;
use zibo\library\spider\report\SpiderReport;
use zibo\library\spider\Web;
use zibo\library\spider\WebNode;

use zibo\spider\view\ReportTableView;

/**
 * Spider report of all the gathered mailto's
 */
class MailtoReport implements SpiderReport {

    /**
     * Translation key for the title of this report
     * @var string
     */
    const TRANSLATION_TITLE = 'spider.title.report.mailto';

    /**
     * Nodes which are taken into the report
     * @var array
     */
    private $nodes;

    /**
     * Sets the resulting web of the crawl to this report
     * @param zibo\library\spider\Web $web The resulting web of the crawl
     * @return null
     */
    public function setWeb(Web $web) {
        $this->nodes = array();

        $nodes = $web->getNodes();
        foreach ($nodes as $node) {
            if (!$node->hasType(WebNode::TYPE_MAILTO) || $node->hasType(WebNode::TYPE_IGNORED)) {
                continue;
            }

            $this->nodes[$node->getUrl()] = $node;
        }

        ksort($this->nodes);
    }

    /**
     * Gets the title of this report
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getTitle(Translator $translator) {
        return $translator->translate(self::TRANSLATION_TITLE, array('total' => count($this->nodes)));
    }

    /**
     * Gets the view of this report
     * @return zibo\core\view\HtmlView
     */
    public function getView() {
        return new ReportTableView($this->nodes);
    }

}