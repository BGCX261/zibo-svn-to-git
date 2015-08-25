<?php

namespace zibo\library\orm;

use zibo\core\Zibo;

use zibo\library\database\DatabaseManager;
use zibo\library\filesystem\File;
use zibo\library\orm\loader\io\XmlModelIO;
use zibo\library\orm\loader\ModelRegister;
use zibo\library\orm\ModelDefiner;
use zibo\library\orm\ModelManager;

use zibo\ZiboException;

abstract class OrmModelTestCase extends OrmTestCase {

	const SQL_SETUP = 'data/setup.sql';

	const SQL_TEARDOWN = 'data/teardown.sql';

	private $sqls;

	private $modelName;

	protected $model;

	public function __construct($modelName = null) {
        $this->modelName = $modelName;
        $this->sqls = array();
	}

    protected function setUp() {
        parent::setUp();

        $path = new File(getcwd(), Zibo::DIRECTORY_APPLICATION);
        $modelIO = new XmlModelIO();
        $models = $modelIO->readModelsFromPath($path);

        $register = new ModelRegister();
        $register->registerModels($models);

        $models = $register->getModels();

        $definer = new ModelDefiner();
        $definer->defineModels($models);

		$connection = DatabaseManager::getInstance()->getConnection();
		$connection->executeFile(new File(self::SQL_SETUP));

        if ($this->modelName != null) {
            $this->model = ModelManager::getInstance()->getModel($this->modelName);
        }

        Zibo::getInstance()->registerEventListener(Zibo::EVENT_LOG, array($this, 'logSql'));
    }

	public function tearDown() {
		$connection = DatabaseManager::getInstance()->getConnection();
		$connection->executeFile(new File(self::SQL_TEARDOWN));

//        $definer = new ModelDefiner();
//        $definer->deleteModels($models);

        parent::tearDown();
	}

    protected function findById($id) {
        $this->assertNotNull($this->model, 'no model set to this test case');

        $data = $this->model->findById($id);

        $this->assertNotNull($data, 'test record #' . $id . ' not found');
        $this->assertTrue(is_object($data), 'test record #' . $id . ' is not an object');

        return $data;
    }

	protected function getSqls() {
		return $this->sqls;
	}

	public function logSql($title, $message = '', $type = 0, $name = '') {
		if ($name == DatabaseManager::LOG_NAME) {
			$this->sqls[] = substr($title, 8);
		}
	}

}