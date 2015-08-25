<?php

namespace zibo\library\orm\loader;

use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\definition\field\PropertyField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\exception\ModelException;
use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\orm\model\LocalizedModel;
use zibo\library\orm\model\SimpleModel;
use zibo\library\orm\OrmTestCase;

use zibo\test\Reflection;

class ModelRegisterTest extends OrmTestCase {

    private $register;

    protected function setUp() {
        parent::setUp();

        $this->register = new ModelRegister();
    }

    public function testRegisterModel() {
        $field = new PropertyField('field', 'type');

        $model = $this->createModel('table', array($field));
        $model2 = $this->createModel('table2', array($field));

        $this->register->registerModel($model);
        $this->register->registerModel($model2);

        $models = Reflection::getProperty($this->register, 'models');

        $this->assertTrue(is_array($models), 'models is not an array');

        $this->assertTrue(isset($models[$model2->getName()]), $model2->getName() . ' is not set');
        $this->assertEquals($model2, $models[$model2->getName()]);

        $this->assertTrue(isset($models[$model->getName()]), $model->getName() . ' is not set');
        $this->assertEquals($model, $models[$model->getName()]);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testHasModelThrowsExceptionWhenEmptyNameProvided() {
        $this->register->hasModel('');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testGetModelThrowsExceptionWhenEmptyNameProvided() {
        $this->register->getModel('');
    }

    /**
     * @expectedException zibo\library\orm\exception\OrmException
     */
    public function testGetModelThrowsExceptionWhenModelDoesNotExist() {
        $this->register->getModel('unexistant');
    }

    public function testRegisterModelWithLinkedHasManyModelsRegistersLinkModel() {
        $hasMany1 = new HasManyField('field', 'table2');
        $hasMany2 = new HasManyField('field', 'table1');

        $model1 = $this->createModel('table1', array($hasMany1));
        $model2 = $this->createModel('table2', array($hasMany2));

        $this->register->registerModel($model1);
        $this->register->registerModel($model2);

        $linkModelName = 'table1table2';

        $linkModel = $this->register->getModel($linkModelName);
        $linkModelTable = $linkModel->getMeta()->getModelTable();
        $fields = $linkModelTable->getFields();

        $this->assertEquals($linkModelName, $hasMany1->getLinkModelName());
        $this->assertEquals($linkModelName, $hasMany2->getLinkModelName());

        $found = array();
        foreach ($fields as $field) {
            if ($field->getName() == 'table1') {
                $this->assertEquals('table1', $field->getRelationModelName());
            }

            if ($field->getName() == 'table2') {
                $this->assertEquals('table2', $field->getRelationModelName());
            }

            $found[] = $field->getName();
        }
        $this->assertEquals(array('id', 'table1', 'table2'), $found, 'link table has not the expected fields');

        $model1 = $this->register->getModel('table1');
        $table1 = $model1->getMeta()->getModelTable();
        $field1 = $table1->getField('field');
        $this->assertEquals($linkModelName, $field1->getLinkModelName(), 'field of table1 has no link model');

        $model2 = $this->register->getModel('table2');
        $table2 = $model2->getMeta()->getModelTable();
        $field2 = $table2->getField('field');
        $this->assertEquals($linkModelName, $field2->getLinkModelName(), 'field of table2 has no link model');
    }

    public function testRegisterModelWithLinkedHasManyModelsToSelfRegistersLinkModel() {
        $hasMany1 = new HasManyField('field', 'tableWithLinkToSelf');

        $model1 = $this->createModel('tableWithLinkToSelf', array($hasMany1));

        $this->register->registerModel($model1);

        $linkModelName = 'tableWithLinkToSelftableWithLinkToSelf';

        $linkModel = $this->register->getModel($linkModelName);
        $linkModelTable = $linkModel->getMeta()->getModelTable();
        $fields = $linkModelTable->getFields();

        $this->assertEquals($linkModelName, $hasMany1->getLinkModelName());

        $found = array();
        foreach ($fields as $field) {
            if ($field->getName() == 'tableWithLinkToSelf1') {
                $this->assertEquals('tableWithLinkToSelf', $field->getRelationModelName());
            }
            if ($field->getName() == 'tableWithLinkToSelf2') {
                $this->assertEquals('tableWithLinkToSelf', $field->getRelationModelName());
            }
            $found[] = $field->getName();
        }
        $this->assertEquals(array('id', 'tableWithLinkToSelf1', 'tableWithLinkToSelf2'), $found);
    }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testRegisterModelWithLinkedHasManyModelsToSelfDoesNotRegisterLinkModelWhenLinkHasBelongsToSelf() {
        $tableName = 'tableTest';

        $hasMany = new HasManyField('field1', $tableName);
        $belongsTo = new BelongsToField('field2', $tableName);

        $model1 = $this->createModel($tableName, array($hasMany, $belongsTo));

        $this->register->registerModel($model1);

        $linkModelName = $tableName . $tableName;

        $this->register->getModel($linkModelName);
   }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testRegisterModelWithNoLinkedHasManyModelsDoesRegisterLinkModel() {
        $hasMany = new HasManyField('field1', 'tableEmpty');

        $model1 = $this->createModel('tableWithLinkToTableEmpty', array($hasMany));
        $model2 = $this->createModel('tableEmpty', array());

        $this->register->registerModel($model1);
        $this->register->registerModel($model2);

        $linkModelName = 'tableEmptytableWithLinkToTableEmpty';

        $this->register->getModel($linkModelName);
    }

    public function testRegisterModelWithLocalizationRegistersLocalizedModel() {
        $field1 = new PropertyField('field1', 'string');
        $field1->setIsLocalized(true);

        $field2 = new PropertyField('field2', 'string');

        $model = $this->createModel('model', array($field1, $field2));

        $this->register->registerModel($model);

        try {
            $model = $this->register->getModel($model->getName());

            $localizedModel = $this->register->getModel($model->getName() . LocalizedModel::MODEL_SUFFIX);

            $dataField = $localizedModel->getMeta()->getField(LocalizedModel::FIELD_DATA);
            $localeField = $localizedModel->getMeta()->getField(LocalizedModel::FIELD_LOCALE);

            $localizedField = $localizedModel->getMeta()->getField('field1');

            $this->assertFalse($localizedModel->getMeta()->hasField('field2'));
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @expectedException zibo\library\orm\exception\OrmException
     */
    public function testRegisterModelWithLocalizationDoesntRegisterLocalizedLinkModelWhenBothLinksAreLocalized() {
        $field1 = new HasManyField('field1', 'Model2');
        $field1->setIsLocalized(true);

        $model1 = $this->createModel('model1', array($field1));

        $field2 = new BelongsToField('field2', 'Model1');
        $field2->setIsLocalized(true);

        $model2 = $this->createModel('model2', array($field2));

        $this->register->registerModel($model1);
        $this->register->registerModel($model2);

        $localizedModel = $this->register->getModel('model1' . LocalizedModel::MODEL_SUFFIX . 'model2');
   }

}