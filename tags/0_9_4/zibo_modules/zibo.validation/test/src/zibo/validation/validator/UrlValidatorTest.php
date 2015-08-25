<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class UrlValidatorTest extends BaseTestCase {

    public function testRegexAlpha() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => true,
            ),
            array(
                'value' => '@',
                'expected' => false,
            ),
            array(
                'value' => '.',
                'expected' => false,
            ),
            array(
                'value' => 'D',
                'expected' => true,
            ),
            array(
                'value' => '5',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexAlpha');
    }

    public function testRegexDigit() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => false,
            ),
            array(
                'value' => '@',
                'expected' => false,
            ),
            array(
                'value' => 'D',
                'expected' => false,
            ),
            array(
                'value' => '5',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexDigit');
    }

    public function testRegexAlphaDigit() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => true,
            ),
            array(
                'value' => 'D',
                'expected' => true,
            ),
            array(
                'value' => '.',
                'expected' => false,
            ),
            array(
                'value' => '@',
                'expected' => false,
            ),
            array(
                'value' => '5',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexAlphaDigit');
    }

    public function testRegexSafe() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => false,
            ),
            array(
                'value' => '7',
                'expected' => false,
            ),
            array(
                'value' => '@',
                'expected' => false,
            ),
            array(
                'value' => '$',
                'expected' => true,
            ),
            array(
                'value' => '+',
                'expected' => true,
            ),
            array(
                'value' => '_',
                'expected' => true,
            ),
            array(
                'value' => '.',
                'expected' => true,
            ),
            array(
                'value' => '-',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexSafe');
    }

    public function testRegexExtra() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => false,
            ),
            array(
                'value' => '7',
                'expected' => false,
            ),
            array(
                'value' => '@',
                'expected' => false,
            ),
            array(
                'value' => '!',
                'expected' => true,
            ),
            array(
                'value' => '*',
                'expected' => true,
            ),
            array(
                'value' => "'",
                'expected' => true,
            ),
            array(
                'value' => '(',
                'expected' => true,
            ),
            array(
                'value' => ')',
                'expected' => true,
            ),
            array(
                'value' => ',',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexExtra');
    }

    public function testRegexNational() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => false,
            ),
            array(
                'value' => '7',
                'expected' => false,
            ),
            array(
                'value' => '{',
                'expected' => true,
            ),
            array(
                'value' => '}',
                'expected' => true,
            ),
            array(
                'value' => '|',
                'expected' => true,
            ),
            array(
                'value' => '\\',
                'expected' => true,
            ),
            array(
                'value' => '^',
                'expected' => true,
            ),
            array(
                'value' => "~",
                'expected' => true,
            ),
            array(
                'value' => '[',
                'expected' => true,
            ),
            array(
                'value' => ']',
                'expected' => true,
            ),
            array(
                'value' => '`',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexNational');
    }

    public function testRegexReserved() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => false,
            ),
            array(
                'value' => '7',
                'expected' => false,
            ),
            array(
                'value' => '{',
                'expected' => false,
            ),
            array(
                'value' => '}',
                'expected' => false,
            ),
            array(
                'value' => ';',
                'expected' => true,
            ),
            array(
                'value' => '/',
                'expected' => true,
            ),
            array(
                'value' => '?',
                'expected' => true,
            ),
            array(
                'value' => ":",
                'expected' => true,
            ),
            array(
                'value' => '@',
                'expected' => true,
            ),
            array(
                'value' => '&',
                'expected' => true,
            ),
            array(
                'value' => '=',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexReserved');
    }

    public function testRegexHex() {
        $tests = array(
            array(
                'value' => 't',
                'expected' => false,
            ),
            array(
                'value' => '7',
                'expected' => true,
            ),
            array(
                'value' => '{',
                'expected' => false,
            ),
            array(
                'value' => 'A',
                'expected' => true,
            ),
            array(
                'value' => 'B',
                'expected' => true,
            ),
            array(
                'value' => "C",
                'expected' => true,
            ),
            array(
                'value' => 'D',
                'expected' => true,
            ),
            array(
                'value' => 'E',
                'expected' => true,
            ),
            array(
                'value' => 'F',
                'expected' => true,
            ),
            array(
                'value' => 'a',
                'expected' => true,
            ),
            array(
                'value' => 'b',
                'expected' => true,
            ),
            array(
                'value' => "c",
                'expected' => true,
            ),
            array(
                'value' => 'd',
                'expected' => true,
            ),
            array(
                'value' => 'e',
                'expected' => true,
            ),
            array(
                'value' => 'f',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexHex');
    }

    public function testRegexEscape() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => false,
            ),
            array(
                'value' => '%6D',
                'expected' => true,
            ),
            array(
                'value' => 'de55',
                'expected' => false,
            ),
            array(
                'value' => '.',
                'expected' => false,
            ),
            array(
                'value' => '%f3',
                'expected' => true,
            ),
            array(
                'value' => '%33',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexEscape');
    }

    public function testRegexUnreserved() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => true,
            ),
            array(
                'value' => '%6D',
                'expected' => false,
            ),
            array(
                'value' => 'de55',
                'expected' => false,
            ),
            array(
                'value' => '*',
                'expected' => true,
            ),
            array(
                'value' => '3',
                'expected' => true,
            ),
            array(
                'value' => 'z3t',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexUnreserved');
    }

    public function testRegexUchar() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => true,
            ),
            array(
                'value' => '%6D',
                'expected' => true,
            ),
            array(
                'value' => 'de55',
                'expected' => false,
            ),
            array(
                'value' => '*',
                'expected' => true,
            ),
            array(
                'value' => '3',
                'expected' => true,
            ),
            array(
                'value' => '99',
                'expected' => false,
            ),
            array(
                'value' => '?',
                'expected' => false,
            ),
            array(
                'value' => '^',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexUchar');
    }

    public function testRegexXchar() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => true,
            ),
            array(
                'value' => '%6D',
                'expected' => true,
            ),
            array(
                'value' => 'de55',
                'expected' => false,
            ),
            array(
                'value' => '*',
                'expected' => true,
            ),
            array(
                'value' => '3',
                'expected' => true,
            ),
            array(
                'value' => '99',
                'expected' => false,
            ),
            array(
                'value' => '?',
                'expected' => true,
            ),
            array(
                'value' => '^',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexXchar');
    }

    public function testRegexDigits() {
        $tests = array(
            array(
                'value' => 'a',
                'expected' => false,
            ),
            array(
                'value' => '%6D',
                'expected' => false,
            ),
            array(
                'value' => 'de55',
                'expected' => false,
            ),
            array(
                'value' => '*',
                'expected' => false,
            ),
            array(
                'value' => '3',
                'expected' => true,
            ),
            array(
                'value' => '99',
                'expected' => true,
            ),
            array(
                'value' => '?',
                'expected' => false,
            ),
            array(
                'value' => '^',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexDigits');
    }

    public function testRegexScheme() {
        $tests = array(
            array(
                'value' => 'mysql',
                'expected' => true,
            ),
            array(
                'value' => '=&6D',
                'expected' => false,
            ),
            array(
                'value' => 'de55',
                'expected' => true,
            ),
            array(
                'value' => 'è',
                'expected' => false,
            ),
            array(
                'value' => '3.',
                'expected' => true,
            ),
            array(
                'value' => '99',
                'expected' => true,
            ),
            array(
                'value' => '?',
                'expected' => false,
            ),
            array(
                'value' => '^',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexScheme');
    }

    public function testRegexUser() {
        $tests = array(
            array(
                'value' => 'username',
                'expected' => true,
            ),
            array(
                'value' => '=&6D',
                'expected' => true,
            ),
            array(
                'value' => 'de55',
                'expected' => true,
            ),
            array(
                'value' => 'è',
                'expected' => false,
            ),
            array(
                'value' => '3',
                'expected' => true,
            ),
            array(
                'value' => '99',
                'expected' => true,
            ),
            array(
                'value' => '?',
                'expected' => true,
            ),
            array(
                'value' => '^',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexUser');
    }

    public function testRegexTopLabel() {
        $tests = array(
            array(
                'value' => 'username',
                'expected' => true,
            ),
            array(
                'value' => '=&6D',
                'expected' => false,
            ),
            array(
                'value' => 'de55',
                'expected' => true,
            ),
            array(
                'value' => '34EZ',
                'expected' => false,
            ),
            array(
                'value' => '3',
                'expected' => false,
            ),
            array(
                'value' => 'a9',
                'expected' => true,
            ),
            array(
                'value' => 'test-pc',
                'expected' => true,
            ),
            array(
                'value' => 'test_pc',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexTopLabel');
    }

    public function testRegexDomainLabel() {
        $tests = array(
            array(
                'value' => 'username',
                'expected' => true,
            ),
            array(
                'value' => '=&6D',
                'expected' => false,
            ),
            array(
                'value' => 'de55',
                'expected' => true,
            ),
            array(
                'value' => '34EZ',
                'expected' => true,
            ),
            array(
                'value' => '3',
                'expected' => true,
            ),
            array(
                'value' => 'a9',
                'expected' => true,
            ),
            array(
                'value' => 'test-pc',
                'expected' => true,
            ),
            array(
                'value' => 'test_pc',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexDomainLabel');
    }

    public function testRegexHostNumber() {
        $tests = array(
            array(
                'value' => 'username',
                'expected' => false,
            ),
            array(
                'value' => '=&6D',
                'expected' => false,
            ),
            array(
                'value' => '127.0.0.1',
                'expected' => true,
            ),
            array(
                'value' => '255.25.255.225',
                'expected' => true,
            ),
            array(
                'value' => '32.5s6.24.20',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexHostNumber');
    }

    public function testRegexHostName() {
        $tests = array(
            array(
                'value' => 'username.3r',
                'expected' => false,
            ),
            array(
                'value' => '=&6D',
                'expected' => false,
            ),
            array(
                'value' => 'test-pc',
                'expected' => true,
            ),
            array(
                'value' => 'test-pc.be',
                'expected' => true,
            ),
            array(
                'value' => 'vs001.virtualserver.company-name.toplevel',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexHostName');
    }

    public function testRegexHostPort() {
        $tests = array(
            array(
                'value' => 'username.3r',
                'expected' => false,
            ),
            array(
                'value' => '=&6D',
                'expected' => false,
            ),
            array(
                'value' => 'test-pc',
                'expected' => true,
            ),
            array(
                'value' => 'test-pc.be:EZ',
                'expected' => false,
            ),
            array(
                'value' => 'vs001.virtualserver.company-name.toplevel',
                'expected' => true,
            ),
            array(
                'value' => 'test-pc.be:245',
                'expected' => true,
            ),
            array(
                'value' => 'vs001.virtualserver.company-name.toplevel:88888',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexHostPort');
    }

    public function testRegexLogin() {
        $tests = array(
            array(
                'value' => 'username.3r',
                'expected' => false,
            ),
            array(
                'value' => 'username:password@test-pc',
                'expected' => true,
            ),
            array(
                'value' => 'test-pc.be:EZ',
                'expected' => false,
            ),
            array(
                'value' => 'email@company-name.toplevel:passw0rd@vs001.virtualserver.company-name.toplevel',
                'expected' => false,
            ),
            array(
                'value' => 'username@test-pc.be:245',
                'expected' => true,
            ),
            array(
                'value' => 'invalid ^ username@vs001.virtualserver.company-name.toplevel:88888',
                'expected' => false,
            ),
            array(
                'value' => 'username:password@localhost:3306',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexLogin');
    }

    public function testRegexFtpSegment() {
        $tests = array(
            array(
                'value' => 'username:3r',
                'expected' => true,
            ),
            array(
                'value' => 'username:password@test-pc',
                'expected' => true,
            ),
            array(
                'value' => 'test-pc.be:EZ',
                'expected' => true,
            ),
            array(
                'value' => 'email@company-name.toplevel:passw0rd@vs001.virtualserver.company-name.toplevel',
                'expected' => true,
            ),
            array(
                'value' => 'username@test-pc.be:245',
                'expected' => true,
            ),
            array(
                'value' => 'invalid ^ username@vs001.virtualserver.company-name.toplevel:88888',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexFtpSegment');
    }

    public function testRegexFtpPath() {
        $tests = array(
            array(
                'value' => 'username:3r',
                'expected' => true,
            ),
            array(
                'value' => 'username:password@test-pc/',
                'expected' => false,
            ),
            array(
                'value' => 'test-pc.be:EZ/test',
                'expected' => true,
            ),
            array(
                'value' => 'email@company-name.toplevel:passw0rd@vs001.virtualserver.company-name.toplevel',
                'expected' => true,
            ),
            array(
                'value' => 'username@test-pc.be:245',
                'expected' => true,
            ),
            array(
                'value' => 'invalid ^ username@vs001.virtualserver.company-name.toplevel:88888',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexFtpPath');
    }

    public function testRegexFtp() {
        $tests = array(
            array(
                'value' => 'ftp://username:password@ftp.domain:11/test/folder',
                'expected' => true,
            ),
            array(
                'value' => 'username:password@test-pc',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexFtp');
    }

    public function testRegexFile() {
        $tests = array(
            array(
                'value' => 'file//code.google.com:80/p/zibo',
                'expected' => false,
            ),
            array(
                'value' => 'file://test-pc/test/file',
                'expected' => true,
            ),
        );

        $this->performRegexTest($tests, 'regexFile');
    }


    public function testRegexHttp() {
        $tests = array(
            array(
                'value' => 'http://code.google.com:80/p/zibo',
                'expected' => true,
            ),
            array(
                'value' => 'username:password@test-pc',
                'expected' => false,
            ),
        );

        $this->performRegexTest($tests, 'regexHttp');
    }

    protected function performRegexTest($tests, $property) {
        $validator = new UrlValidator();
        $regex = Reflection::getProperty($validator, $property);
        $regexValidator = new RegexValidator(array('regex' => '/^' . $regex . '$/'));

        foreach ($tests as $test) {
            $result = $regexValidator->isValid($test['value']);
            $this->assertEquals($test['expected'], $result, $test['value'] . ' - ' . $regex);
        }
    }

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $expected) {
        $code = 'error.validation.url';
        $message = 'Field is not a valid url';
        $validator = new UrlValidator();


        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result, $value);

        if (!$expected) {
            $regex = Reflection::getProperty($validator, 'regex');

            $expectedParameters = array(
                'value' => $value,
                'regex' => $regex
            );
            $expectedErrors = array(new ValidationError($code, $message, $expectedParameters));

            $resultErrors = $validator->getErrors();

            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerIsValid() {
        return array(
            array('http://www.google.com/', true),
            array('http://www.google.com', true),
            array('www.google.com', false),
            array('file://etc/passwd', true),
            array('ftp://ftp.google.com', true),
            array('ftp://user:password@ftp.google.com/folder', true),
        );

    }

}