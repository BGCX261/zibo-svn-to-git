<?php

namespace zibo\library\validation;

use zibo\core\Zibo;

use zibo\library\config\IniIO;
use zibo\library\validation\filter\TrimFilter;
use zibo\library\validation\validator\RequiredValidator;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class ValidationFactoryTest extends BaseTestCase {

    private $factory;

    protected function setUp() {
        $browser = $this->getMock('zibo\\core\\filesystem\\FileBrowser');
        $configIO = new ConfigIOMock();
        $zibo = new Zibo($browser, $configIO);

        $this->factory = new ValidationFactory($zibo);
    }

    public function testRegisterValidator() {
        $validatorName = 'required';
        $validatorClass = 'zibo\\library\\validation\\validator\\RequiredValidator';

        $this->factory->registerValidator($validatorName, $validatorClass);

        $validators = Reflection::getProperty($this->factory, 'validators');

        $this->assertArrayHasKey($validatorName, $validators);
        $this->assertContains($validatorClass, $validators);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterValidatorWithEmptyNameThrowsException() {
        $validatorClass = 'zibo\\library\\validation\\validator\\RequiredValidator';
        $this->factory->registerValidator('', $validatorClass);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterValidatorWithEmptyClassThrowsException() {
        $this->factory->registerValidator('required', '');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterValidatorWithInvalidClassThrowsException() {
        $validatorClass = 'zibo\\library\\validation\\invalid';
        $this->factory->registerValidator('required', $validatorClass);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterValidatorWithNoValidatorClassThrowsException() {
        $validatorClass = 'zibo\\library\\String';
        $this->factory->registerValidator('required', $validatorClass);
    }

    public function testCreateValidator() {
        $validatorName = 'required';
        $validatorClass = 'zibo\\library\\validation\\validator\\RequiredValidator';
        $this->factory->registerValidator($validatorName, $validatorClass);

        $validator = $this->factory->createValidator($validatorName);

        $result = $validator instanceof RequiredValidator;
        $this->assertTrue($result);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testCreateValidatorWithInvalidValidatorThrowsException() {
        $this->factory->createValidator('unexistant');
    }

    public function testRegisterFilter() {
        $filterName = 'trim';
        $filterClass = 'zibo\\library\\validation\\filter\\TrimFilter';

        $this->factory->registerFilter($filterName, $filterClass);

        $filters = Reflection::getProperty($this->factory, 'filters');

        $this->assertArrayHasKey($filterName, $filters);
        $this->assertContains($filterClass, $filters);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterFilterWithEmptyNameThrowsException() {
        $filterClass = 'zibo\\library\\validation\\filter\\TrimFilter';
        $this->factory->registerFilter('', $filterClass);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterFilterWithEmptyClassThrowsException() {
        $this->factory->registerFilter('trim', '');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterFilterWithUnexistantClassThrowsException() {
        $filterClass = 'zibo\\library\\validation\\invalid';
        $this->factory->registerFilter('trim', $filterClass);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterFilterWithNoFilterClassThrowsException() {
        $filterClass = 'zibo\\library\\String';
        $this->factory->registerFilter('trim', $filterClass);
    }

    public function testCreateFilter() {
        $filterName = 'filter';
        $filterClass = 'zibo\\library\\validation\\filter\\TrimFilter';
        $this->factory->registerFilter($filterName, $filterClass);

        $filter = $this->factory->createFilter($filterName);

        $result = $filter instanceof TrimFilter;
        $this->assertTrue($result);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testCreateFilterWithInvalidFilterThrowsException() {
        $this->factory->createFilter('unexistant');
    }

}