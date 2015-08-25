<?php

namespace zibo\library\orm;

use zibo\core\environment\Environment;
use zibo\core\Zibo;

use zibo\library\config\io\ini\IniConfigIO;
use zibo\library\database\DatabaseManager;
use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\orm\model\SimpleModel;
use zibo\library\orm\ModelManager;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

abstract class OrmTestCase extends BaseTestCase {

    protected $manager;

    protected function setUp() {
        $path = new File(__DIR__ . '/../../../../');

        $this->setUpApplication($path->getPath());

        $browser = new GenericBrowser(new File(getcwd()));
        $configIO = new IniConfigIO(Environment::getInstance(), $browser);

        Zibo::getInstance($browser, $configIO);

        if (!DatabaseManager::getInstance()->hasConnection('mysql')) {
            $this->markTestSkipped('No dsn found for database.connection.mysql, check config/database.ini');
        }

        $this->manager = ModelManager::getInstance();
		Reflection::setProperty($this->manager, 'models', array());
    }

	protected function tearDown() {
		Reflection::setProperty(ModelManager::getInstance(), 'instance', null);
        Reflection::setProperty(DatabaseManager::getInstance(), 'instance', null);
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
	}

   protected function createModel($name, array $fields) {
       $table = new ModelTable($name);

       foreach ($fields as $field) {
           $table->addField($field);
       }

       $meta = new ModelMeta($table);

       return new SimpleModel($meta);
   }

}