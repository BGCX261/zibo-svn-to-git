<?php

namespace zibo\library\spider\report;

use zibo\library\i18n\translation\Translator;
use zibo\library\spider\Web;

/**
 * Interface for a report of the spider crawl
 */
interface SpiderReport {

    /**
     * Sets the resulting web of the crawl to this report
     * @param zibo\library\spider\Web $web The resulting web of the crawl
     * @return null
     */
    public function setWeb(Web $web);

    /**
     * Gets the title of this report
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getTitle(Translator $translator);

    /**
     * Gets the view of this report
     * @return zibo\core\view\HtmlView
     */
    public function getView();

}