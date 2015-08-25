<?php

namespace zibo\library\orm\query;

use zibo\core\Zibo;

use zibo\library\config\IniReader;
use zibo\library\database\query\Field;
use zibo\library\database\query\FunctionField;
use zibo\library\database\query\Table;
use zibo\library\orm\exception\ModelException;
use zibo\library\orm\OrmModelTestCase;

use zibo\test\Reflection;

use zibo\ZiboException;

class ModelQueryTest extends OrmModelTestCase {

//    public function testConstructThrowsExceptionWithInvalidOperator() {
//		try {
//			new ModelQuery('Category', 'TEST');
//		} catch (ModelException $e) {
//			return;
//		}
//		$this->fail();
//	}
//
//    public function testCount() {
//        $query = new ModelQuery('Blog');
//        $count = $query->count();
//        $this->assertEquals(3, $count);
//    }
//
//    public function testQueryRecursive() {
//        $query = new ModelQuery('Blog');
//        $blogs = $query->query();
//
//        $this->assertNotNull($blogs, 'blogs is null');
//        $this->assertFalse(empty($blogs), 'blogs is empty');
//        $this->assertEquals(3, count($blogs), 'expecting 3 blogs');
//        $this->assertTrue(isset($blogs[3]), 'blog #3 is not retrieved');
//
//        $blog = $blogs[3];
//        $this->assertTrue(is_object($blog), 'blog #3 is not an object');
//        $this->assertEquals('Third blog', $blog->title, 'blog #3 doesn\'t have the expected title');
//        $this->assertEquals('lorum ipsum', $blog->text, 'blog #3 doesn\'t have the expected text');
//        $this->assertEquals(2, count($blog->comments), 'expecting 2 comments on blog #3');
//
//        $comment = $blog->comments[3];
//        $this->assertEquals('John Doe', $comment->name, 'comment #3 doesn\'t have the expected name');
//        $this->assertEquals('Whooooooooooaaaa', $comment->comment, 'comment #3 doesn\'t have the expected comment');
//    }
//
//    public function testQueryNotRecursive() {
//        $query = new ModelQuery('Blog');
//        $query->setRecursive(false);
//        $blogs = $query->query();
//
//        $this->assertNotNull($blogs, 'blogs is null');
//        $this->assertFalse(empty($blogs), 'blogs is empty');
//        $this->assertEquals(3, count($blogs), 'expecting 3 blogs');
//        $this->assertTrue(isset($blogs[3]), 'blog #3 is not retrieved');
//
//        $blog = $blogs[3];
//        $this->assertTrue(is_object($blog), 'blog #3 is not an object');
//        $this->assertEquals('Third blog', $blog->title, 'blog #3 doesn\'t have the expected title');
//        $this->assertEquals('lorum ipsum', $blog->text, 'blog #3 doesn\'t have the expected text');
//        $this->assertFalse(isset($blog->comments), 'expecting no comments');
//    }
//
//    public function testQueryWithSetLimit() {
//        $query = new ModelQuery('Blog');
//        $query->setLimit(1, 2);
//        $blogs = $query->query();
//
//        $this->assertNotNull($blogs, 'blogs is null');
//        $this->assertFalse(empty($blogs), 'blogs is empty');
//        $this->assertEquals(1, count($blogs), 'expecting 3 blogs');
//        $this->assertTrue(isset($blogs[3]), 'blog #3 is not retrieved');
//    }
//
//    public function testQueryWithSetFields() {
//        $query = new ModelQuery('Blog');
//        $query->setFields('{id}, {title}');
//        $query->addFields('{comments}');
//        $blogs = $query->query();
//
//        $this->assertNotNull($blogs, 'blogs is null');
//        $this->assertFalse(empty($blogs), 'blogs is empty');
//        $this->assertEquals(3, count($blogs), 'expecting 3 blogs');
//        $this->assertTrue(isset($blogs[3]), 'blog #3 is not retrieved');
//
//        $blog = $blogs[3];
//        $this->assertTrue(is_object($blog), 'blog #3 is not an object');
//        $this->assertEquals('Third blog', $blog->title, 'blog #3 doesn\'t have the expected title');
//        $this->assertNull($blog->text, 'blog #3 has unexpected text');
//        $this->assertNotNull($blog->comments, 'blog #3 has unexpected comments');
//    }
//
//    public function testQueryWithAddedCountFunctionAndGroupBy() {
//        $query = new ModelQuery('Blog');
//        $query->setDistinct(true);
//        $query->addFields('COUNT({comments.id}) AS numComments');
//        $query->addGroupBy('{id}');
//        $blogs = $query->query();
//
//        $this->assertNotNull($blogs, 'blogs is null');
//        $this->assertFalse(empty($blogs), 'blogs is empty');
//        $this->assertEquals(3, count($blogs), 'expecting 3 blogs');
//        $this->assertTrue(isset($blogs[3]), 'blog #3 is not retrieved');
//
//        $this->assertEquals(1, $blogs[1]->numComments, 'numComments not set for blog #1');
//        $this->assertEquals(0, $blogs[2]->numComments, 'numComments not set for blog #2');
//        $this->assertEquals(2, $blogs[3]->numComments, 'numComments not set for blog #3');
//    }

}