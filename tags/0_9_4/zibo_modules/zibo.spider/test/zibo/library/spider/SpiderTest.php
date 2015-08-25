<?php

namespace zibo\library\spider;

use zibo\library\spider\bite\AnchorSpiderBite;
use zibo\library\spider\bite\CssSpiderBite;
use zibo\library\spider\bite\ImageSpiderBite;
use zibo\library\spider\bite\JsSpiderBite;

use zibo\test\BaseTestCase;

class SpiderTest extends BaseTestCase {

    public function testSpider() {
        $spider = new Spider('http://localhost/kayalion/scrape');
        $spider->addBite(new AnchorSpiderBite());
        $spider->addBite(new CssSpiderBite());
        $spider->addBite(new ImageSpiderBite());
        $spider->addBite(new JsSpiderBite());

        $spider->crawl();
    }

}