<?php

namespace zibo\library\orm;

use zibo\library\validation\exception\ValidationException;

use zibo\library\orm\definition\field\ModelField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\exception\ModelException;
use zibo\library\orm\query\ModelQuery;

class ModelTest extends OrmModelTestCase {

//    public function testFindHasOne() {
//        $userModel = ModelManager::getInstance()->getModel('User');
//        $profileModel = ModelManager::getInstance()->getModel('Profile');
//
//        $profile = $profileModel->create();
//        $profile->id = 1;
//        $profile->user = 1;
//        $profile->extra = 'extra';
//
//        $user1 = $userModel->create();
//        $user1->id = 1;
//        $user1->username = 'user1';
//        $user1->password = 'secret';
//        $user1->profile = $profile;
//        $user2 = $userModel->create();
//        $user2->id = 2;
//        $user2->username = 'user2';
//        $user2->password = 's3cr3t';
//
//        $expectedUsers = array(
//            $user1->id => $user1,
//            $user2->id => $user2,
//        );
//
//        $users = $userModel->find();
//
//        $this->assertNotNull($users);
//        $this->assertTrue(is_array($users), 'result is not an array');
//        $this->assertEquals($expectedUsers, $users);
//    }

//    public function testValidate() {
//        $userModel = ModelManager::getInstance()->getModel('User');
//        $profileModel = ModelManager::getInstance()->getModel('Profile');
//
//        $profile = $profileModel->create();
//        $profile->extra = 'test';
//
//        $user3 = $userModel->create();
//        $user3->username = 'user3';
//        $user3->password = 'secret';
//        $user3->profile = $profile;
//
//        try {
//            $user3 = $userModel->save($user3);
//        } catch (ValidationException $e) {
//        	return;
//        }
//        $this->fail();
//    }

}