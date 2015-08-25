<?php

namespace zibo\library\orm;

use zibo\core\Zibo;

use zibo\library\orm\definition\field\PropertyField;

use zibo\test\Reflection;

class ModelManagerTest extends OrmTestCase {

    public function testConstruct() {
        $this->assertNotNull(Reflection::getProperty($this->manager, 'models'));
        $this->assertNotNull(Reflection::getProperty($this->manager, 'modelLoader'));
        $this->assertNotNull(Reflection::getProperty($this->manager, 'modelQueryCache'));
    }

    public function testAddAndRemoveModel() {
        $field = new PropertyField('name', 'type');
        $name = 'model';
        $name2 = 'model2';

        $model = $this->createModel($name, array($field));
        $model2 = $this->createModel($name2, array($field));

        $this->manager->addModel($model);
        $this->manager->addModel($model2);

        $models = Reflection::getProperty($this->manager, 'models');

        $this->assertTrue(is_array($models), 'models is not an array');
        $this->assertTrue(count($models) == 2, 'unexpected number of models');
        $this->assertTrue(isset($models[$name]), 'added model not set');
        $this->assertTrue(isset($models[$name2]), 'added model2 not set');
        $this->assertEquals($model, $models[$name], 'model in the manager is not the added model');
        $this->assertEquals($model2, $models[$name2], 'model2 in the manager is not the added model2');

        $this->manager->removeModel($name);

        $models = Reflection::getProperty($this->manager, 'models');

        $this->assertTrue(is_array($models), 'models is not an array');
        $this->assertTrue(count($models) == 1, 'unexpected number of models');
        $this->assertFalse(isset($models[$name]), 'added model still set');
        $this->assertTrue(isset($models[$name2]), 'added model2 not set');
    }

    /**
     * @dataProvider providerRemoveModelThrowsExceptionWhenInvalidModelNamePassed
     * @expectedException zibo\ZiboException
     */
    public function testRemoveModelThrowsExceptionWhenInvalidModelNamePassed($name) {
        $this->manager->removeModel($name);
    }

    public function providerRemoveModelThrowsExceptionWhenInvalidModelNamePassed() {
        return array(
            array(null),
            array(''),
            array($this),
            array('unexistantModel'),
        );
    }

    /**
     * @dataProvider providerHasModel
     */
    public function testHasModel($expected, $name) {
        $result = $this->manager->hasModel($name);

        $this->assertEquals($expected, $result);
    }

    public function providerHasModel() {
        return array(
            array(false, 'unexistantModel'),
            array(true, 'Single'),
        );
    }

}