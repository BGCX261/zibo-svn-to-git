<?php

namespace zibo\test;

use zibo\library\cli\Cli;
use zibo\library\filesystem\File;

use \Exception;
use \PHPUnit_Framework_TestSuite;
use \PHPUnit_Runner_IncludePathTestCollector;
use \PHPUnit_TextUI_TestRunner;

require_once 'PHPUnit/Framework.php';

class Suite {

    const ARGUMENT_TEST = 'test';
    const ARGUMENT_REPORT_CODE_COVERAGE = 'code-coverage';
    const ARGUMENT_VERBOSE = 'verbose';

    const DIRECTORY_TEST = 'test';

    const SUITE_NAME = 'ZiboTest';

    private $cli;

    public function __construct() {
        $this->cli = new Cli();
    }

    public function run() {
        if (!$this->cli->isCli()) {
            header('Content-Type: text/html; charset=utf-8');
            echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
            echo '<pre>';
        }

        $tests = $this->getTests();
        $testPaths = $this->getTestPaths($tests);

        $arguments = array();

        if ($this->shouldBeVerbose()) {
            $arguments['verbose'] = true;
        }

        if ($this->shouldReportCodeCoverage()) {
            $arguments['coverage-html'] = true;
            $arguments['reportDirectory'] = './report';
        }

        foreach ($testPaths as $testPath) {
            $this->runTestSuite($testPath, $arguments);
        }
    }

    private function runTestSuite($testPath, $arguments) {
        $testTokens = explode('/', $testPath);
        $testModule = strtoupper(array_pop($testTokens));

        echo $testModule . "\n";
        echo str_repeat('=', strlen($testModule)) . "\n\n";

        $testCollector = new PHPUnit_Runner_IncludePathTestCollector(array($testPath));

        $suite = new PHPUnit_Framework_TestSuite(self::SUITE_NAME);

        $suite->addTestFiles($testCollector->collectTests());

        PHPUnit_TextUI_TestRunner::run($suite, $arguments);

        echo "\n\n";
    }

    private function getTestPaths($tests) {
        global $autoloader;

        $testPaths = $autoloader->getBrowser()->getIncludePaths(false);
        foreach ($testPaths as $index => $testPath) {
            if (file_exists($testPath . File::DIRECTORY_SEPARATOR . self::DIRECTORY_TEST)) {
                continue;
            }

            unset($testPaths[$index]);
        }

        if (!$tests) {
            return $testPaths;
        }

        $filteredTestPaths = array();
        foreach ($tests as $test) {
            $found = false;
            $testLength = strlen($test);
            foreach ($testPaths as $testPath) {
                $testPos = strpos($testPath, $test);
                if ($testPos === false) {
                    continue;
                }

                if ($testPos == strlen($testPath) - $testLength) {
                    $filteredTestPaths[] = $testPath;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new Exception('Could not find test \'' . $test . '\' in the include path');
            }
        }

        return $filteredTestPaths;
    }

    private function getTests() {
        $test = null;
        if ($this->cli->isCli()) {
            $test = $this->cli->getArgument(self::ARGUMENT_TEST);
        } elseif (isset($_GET[self::ARGUMENT_TEST])) {
            $test = $_GET[self::ARGUMENT_TEST];
        }

        if (!$test) {
            return array();
        }

        return explode(',', $test);
    }

    private function shouldReportCodeCoverage() {
        $reportCodeCoverage = false;

        if ($this->cli->isCli()) {
            $reportCodeCoverage = $this->cli->getArgument(self::ARGUMENT_REPORT_CODE_COVERAGE) == 1;
        } elseif (isset($_GET[self::ARGUMENT_REPORT_CODE_COVERAGE])) {
            $reportCodeCoverage = $_GET[self::ARGUMENT_REPORT_CODE_COVERAGE] == 1;
        }

        //($reportCodeCoverage);
        return $reportCodeCoverage;
    }

    private function shouldBeVerbose() {
        $shouldBeVerbose = false;

       if ($this->cli->isCli()) {
            $shouldBeVerbose = $this->cli->getArgument(self::ARGUMENT_VERBOSE) == 1;
        } elseif (isset($_GET[self::ARGUMENT_VERBOSE])) {
            $shouldBeVerbose = $_GET[self::ARGUMENT_VERBOSE] == 1;
        }
        return $shouldBeVerbose;
    }

}