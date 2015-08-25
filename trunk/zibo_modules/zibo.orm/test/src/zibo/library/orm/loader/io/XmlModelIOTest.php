<?php

namespace zibo\library\orm\loader\io;

use zibo\core\Zibo;

use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\HasOneField;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\definition\field\ModelField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\exception\ModelException;
use zibo\library\orm\OrmTestCase;

use zibo\ZiboException;

class XmlModelIOTest extends OrmTestCase {

	public function testReadModelFromPath() {
        $io = new XmlModelIO();
	    $path = new File(__DIR__ . '/../../../../../../');

		$models = $io->readModelsFromPath($path);

		$this->assertTrue(is_array($models), 'result is not an array');

		$this->assertEquals(11, count($models), 'not the expected number of models');

		$blogFields = $models['Blog']->getMeta()->getModelTable()->getFields();
		$this->assertEquals('Blog', $models['Blog']->getName());
		$this->assertEquals(4, count($blogFields), 'unexpected number of fields in Blog model');
		$fields = array('id', 'title', 'text', 'comments');
		foreach ($fields as $field) {
			$this->assertTrue(isset($blogFields[$field]), 'expected field ' . $field . ' not set');
		}
		$this->assertTrue($blogFields['comments'] instanceof HasManyField, 'comments is not an hasMany field');

		$commentFields = $models['BlogComment']->getMeta()->getModelTable()->getFields();
		$this->assertEquals('BlogComment', $models['BlogComment']->getName());
		$this->assertEquals(5, count($commentFields), 'unexpected number of fields in BlogComment model');
		$fields = array('id', 'blog', 'name', 'email', 'comment');
		foreach ($fields as $field) {
			$this->assertTrue(isset($commentFields[$field]), 'expected field ' . $field . ' not set');
		}
		$this->assertTrue($commentFields['blog'] instanceof BelongsToField, 'blog is not an belongsTo field');

		$userFields = $models['User']->getMeta()->getModelTable()->getFields();
		$validators = $userFields['password']->getValidators();
		$this->assertEquals('User', $models['User']->getName());
		$this->assertFalse(empty($validators), 'No validators found for password field in user');

		$singleTable = $models['Single']->getMeta()->getModelTable();
		$singleFields = $singleTable->getFields();
		$singleIndexes = $singleTable->getIndexes();
		$singleIndex = array_pop($singleIndexes);
		$singleDataFormats = $singleTable->getDataFormats();
		$this->assertEquals('Single', $models['Single']->getName());
		$this->assertEquals(3, count($singleFields), 'unexpected number of fields in the Single model');
		$this->assertEquals(1, count($singleIndexes), 'unexpected number of indexes in the Single model');
		$this->assertEquals('index', $singleIndex->getName());
		$this->assertEquals(2, count($singleIndex->getFields()));
		$this->assertEquals(2, count($singleDataFormats), 'unexpected number of data formats in the Single model');
	}

}