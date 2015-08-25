<?php

namespace zibo\spider\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\spider\bite\AnchorSpiderBite;
use zibo\library\spider\bite\CssImageSpiderBite;
use zibo\library\spider\bite\CssImportSpiderBite;
use zibo\library\spider\bite\CssSpiderBite;
use zibo\library\spider\bite\ImageSpiderBite;
use zibo\library\spider\bite\JsSpiderBite;
use zibo\library\spider\Spider;
use zibo\library\spider\SpiderStatus;
use zibo\library\spider\WebNode;
use zibo\library\validation\exception\ValidationException;
use zibo\library\System;

use zibo\spider\form\SpiderForm;
use zibo\spider\report\CssReport;
use zibo\spider\report\ErrorReport;
use zibo\spider\report\ExternalReport;
use zibo\spider\report\IgnoredReport;
use zibo\spider\report\ImageReport;
use zibo\spider\report\JsReport;
use zibo\spider\report\MailtoReport;
use zibo\spider\report\RedirectReport;
use zibo\spider\report\SuccessReport;
use zibo\spider\view\ReportDetailView;
use zibo\spider\view\ReportView;
use zibo\spider\view\SpiderView;
use zibo\spider\view\StatusView;

use \Exception;

class SpiderController extends AbstractController {

    const CONFIG_PHP_COMMAND = 'spider.php.command';

    const DEFAULT_PHP_COMMAND = 'php';

    const PATH_DATA = 'application/data/spider';

    const SUFFIX_CANCEL = '.cancel';

    const SUFFIX_ERROR = '.error';

    const SUFFIX_SPIDER = '.spider';

    const SUFFIX_STATUS = '.status';

    public function indexAction() {
        if (func_get_args()) {
            $this->setError404();
            return;
        }

        $id = $this->getSession()->getId();

        $fileSpider = new File(self::PATH_DATA, $id . self::SUFFIX_SPIDER);

        $baseUrl = $this->request->getBaseUrl();
        $basePath = $this->request->getBasePath();

        $formAction = $basePath;
        if ($basePath == $baseUrl) {
            $formAction .= '/';
        }

        $form = new SpiderForm($formAction);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $fileCancel = new File(self::PATH_DATA, $id . self::SUFFIX_CANCEL);
                $fileCancel->write('1');

                return;
            }

            try {
                $form->validate();

                $url = $form->getUrl();
                $delay = $form->getDelay();

                $ignore = $form->getIgnore();
                $ignore = explode("\n", $ignore);

                $spider = new Spider($url);

                foreach ($ignore as $ignoreRegex) {
                    $ignoreRegex = trim($ignoreRegex);
                    if (!$ignoreRegex) {
                        continue;
                    }

                    $spider->addIgnoreRegex($ignoreRegex);
                }

                $spider->addBite(new AnchorSpiderBite());
                $spider->addBite(new CssSpiderBite());
                $spider->addBite(new CssImageSpiderBite());
                $spider->addBite(new CssImportSpiderBite());
                $spider->addBite(new ImageSpiderBite());
                $spider->addBite(new JsSpiderBite());

                $spider->addReport(new ErrorReport());
                $spider->addReport(new RedirectReport());
                $spider->addReport(new SuccessReport());
                $spider->addReport(new ImageReport());
                $spider->addReport(new CssReport());
                $spider->addReport(new JsReport());
                $spider->addReport(new ExternalReport());
                $spider->addReport(new MailtoReport());
                $spider->addReport(new IgnoredReport());

                $parent = $fileSpider->getParent();
                $parent->create();

                $fileSpider->write(serialize($spider));

                $php = Zibo::getInstance()->getConfigValue(self::CONFIG_PHP_COMMAND, self::DEFAULT_PHP_COMMAND);

                System::execute($php . ' ' . $_SERVER['SCRIPT_FILENAME'] . ' spider/crawl/' . $id . '/' . $delay . ' > /dev/null 2> /dev/null & echo $!');

                return;
            } catch (ValidationException $exception) {
                $form->setValidationException($exception);
            }
        }

        if ($fileSpider->exists()) {
            $fileSpiderContent = $fileSpider->read();

            $spider = unserialize($fileSpiderContent);

            $form->setUrl($spider->getBaseUrl());
            $form->setIsDisabled(true, SpiderForm::FIELD_URL);
            $form->setIsDisabled(true, SpiderForm::BUTTON_SUBMIT);
        }

        $statusUrl = $basePath . '/status/' . $id;
        $reportUrl = $basePath . '/report/' . $id;

        $view = new SpiderView($form, $statusUrl, $reportUrl);
        $view->setTitle('spider.title', true);

        $this->response->setView($view);
    }

    public function crawlAction($id, $delay = 100) {
        $fileSpider = new File(self::PATH_DATA, $id . self::SUFFIX_SPIDER);
        $fileStatus = new File(self::PATH_DATA, $id . self::SUFFIX_STATUS);
        $fileCancel = new File(self::PATH_DATA, $id . self::SUFFIX_CANCEL);

        try {
            $fileSpiderContent = $fileSpider->read();

            $spider = unserialize($fileSpiderContent);

            $spider->crawl($delay, $fileStatus, $fileCancel);

            $fileSpider->write(serialize($spider));
        } catch (Exception $exception) {
            $fileError = new File(self::PATH_DATA, $id . self::SUFFIX_ERROR);
            $fileError->write($exception->getMessage() . "\n\n" . $exception->getTraceAsString());

            $fileStatus->delete();
        }
    }

    public function statusAction($id) {
        $fileStatus = new File(self::PATH_DATA, $id . self::SUFFIX_STATUS);

        if ($fileStatus->exists()) {
            $status = new SpiderStatus();
            $status->read($fileStatus);
        } else {
            $status = null;
        }

        $view = new StatusView($status);
        $this->response->setView($view);
    }

    public function reportAction($id, $url = null) {
        $spider = null;
        $url = null;

        if (isset($_GET['url'])) {
            $url = $_GET['url'];
        }

        $fileSpider = new File(self::PATH_DATA, $id . self::SUFFIX_SPIDER);
        if ($fileSpider->exists()) {
            $fileSpiderContent = $fileSpider->read();
            $spider = unserialize($fileSpiderContent);
        }

        if ($url) {
            $url = urldecode($url);

            if (!$spider) {
                $node = new WebNode($url);
            } else {
                $web = $spider->getWeb();
                $node = $web->getNode($url);
            }

            $view = new ReportDetailView($node);

            $this->response->setView($view);

            return;
        }

        if ($spider) {
            $web = $spider->getWeb();
            $reports = $spider->getReports();

            foreach ($reports as $report) {
                $report->setWeb($web);
            }
        } else {
            $reports = array();
        }

        $view = new ReportView($reports);
        $this->response->setView($view);
    }

}