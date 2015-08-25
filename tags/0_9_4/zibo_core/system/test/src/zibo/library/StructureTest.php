<?php

namespace zibo\library;

use zibo\test\BaseTestCase;
use zibo\ZiboException;

class StructureTest extends BaseTestCase {

    public function testGet() {
        $array = array(
            'key 1' => 'value 1',
            'key 2' => 'value 2',
        );

        $structure = new Structure($array);
        $this->assertEquals('value 2', $structure->get('key 2'));
    }

    public function testGetNested() {
        $array = array(
            'key 1' => 'value 1',
            'key 2' => array(
                'key 3' => array(
                    'key 4' => 'value 2',
                )
            ),
        );

        $structure = new Structure($array);
        $this->assertEquals('value 2', $structure->get('key 2[key 3][key 4]'));
    }

    public function testGetThrowsExceptionWhenNameIsNotWellFormed() {
        $array = array(
            'name' => array(
                'test' => 'test',
            )
        );
        $structure = new Structure($array);
        try {
            $structure->get('name[test]test');
        } catch (ZiboException $e) {
            return;
        }
        $this->fail();
    }

    public function testGetThrowsExceptionWhenNameArrayIsNotClosed() {
        $array = array(
            'name' => array(
                'test' => 'test',
            )
        );
        $structure = new Structure($array);
        try {
            $structure->get('name[testtest');
        } catch (ZiboException $e) {
            return;
        }
        $this->fail();
    }

    public function testSet() {
        $structure = new Structure();
        $structure->set('test', 'value');
        $this->assertEquals('value', $structure->get('test'));
    }

    public function testSetNested() {
        $structure = new Structure();
        $structure->set('key 1', 'value 1');
        $structure->set('key 2[key 3]', 'value 2');
        $this->assertEquals('value 2', $structure->get('key 2[key 3]'));
        $this->assertEquals('value 1', $structure->get('key 1'));
    }

    public function testHas() {
        $array = array(
            'key 1' => 'value 1',
            'key 2' => array(
                'key 3' => array(
                    'key 4' => 'value 2',
                )
            ),
        );

        $structure = new Structure($array);

        $this->assertTrue($structure->has('key 1'));
        $this->assertTrue($structure->has('key 2'));
        $this->assertTrue($structure->has('key 2[key 3]'));
        $this->assertTrue($structure->has('key 2[key 3][key 4]'));
        $this->assertFalse($structure->has('key 2[key 5]'));
    }

    public function testIterator() {
        $array = array(
            'key 1' => 'value 1',
            'key 2' => array(
                'key 3' => array(
                    'key 4' => 'value 2',
                )
            ),
        );

        $structure = new Structure($array);
        $iterator = $structure->getIterator();

        $output = '';
        foreach ($iterator as $key => $value) {
            $output .= $key . '-';
        }
        $this->assertEquals('key 1-key 2[key 3][key 4]-', $output);
    }

    public function testMerge() {
        $array1 = array('key1' => 'value1', 'key2' => 'value2');
        $array2 = array('key1' => 'value2', 'key3' => 'value3');
        $array3 = array('key1' => 'value2', 'key2' => 'value2', 'key3' => 'value3');

        $array = Structure::merge($array1, $array2);
        $this->assertEquals($array3, $array);
    }

}