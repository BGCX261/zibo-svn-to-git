<?php

namespace zibo\library\database\driver;

use zibo\library\database\Dsn;
use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;

use zibo\ZiboException;

use \Exception;

class AbstractTransactionDriverTest extends BaseTestCase {

    /**
     * @dataProvider providerImport
     */
    public function testImport(array $sqls, $content) {
        $parent = new File('application/data');
        $parent->create();

        $file = new File($parent, 'tmp.sql');
        $file->write($content);

        $driver = new DriverMock(new Dsn('protocol://host/database'));
        $driver->connect();
        $driver->import($file);

        array_unshift($sqls, 'BEGIN');
        $sqls[] = 'COMMIT';

        $this->assertEquals($sqls, $driver->getSqls());

        $file->delete();
    }

    public function providerImport() {
        $sqls1 = array(
            'INSERT INTO table VALUES ("value", "value2")',
            'INSERT INTO table2 VALUES ("value", "value2")',
            'INSERT INTO table3 VALUES ("value", "value2")',
        );
        $content1 = '
INSERT INTO table VALUES ("value", "value2");
INSERT INTO table2 VALUES ("value", "value2");
INSERT INTO table3 VALUES ("value", "value2");
        ';

        $sqls2 = array(
            'INSERT INTO table VALUES ("value", "value2")',
            'INSERT INTO table2 VALUES ("value;", "value2")',
        );

        $content2 = '
INSERT INTO table VALUES ("value", "value2");
INSERT INTO table2 VALUES ("value;", "value2");
        ';

        $sqls3 = array(
            'SET FOREIGN_KEY_CHECKS=0',
            'DROP TABLE IF EXISTS `Continent`',
            'CREATE TABLE `Continent` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`code` varchar(255) DEFAULT NULL,PRIMARY KEY (`id`),KEY `code` (`code`)) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8',
        );

        $content3 = "
/*
MySQL Backup
Source Server Version: 5.1.41
Source Database: maxhavelaar_test
Date: 10/14/2010 16:27:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
--  Table structure for `Continent`
-- ----------------------------
DROP TABLE IF EXISTS `Continent`;
CREATE TABLE `Continent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
        ";

        return array(
            array($sqls1, $content1),
            array($sqls2, $content2),
            array($sqls3, $content3),
        );
    }

}