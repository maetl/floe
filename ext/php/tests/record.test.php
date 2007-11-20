<?php
require_once 'simpletest/autorun.php';
require_once 'classes/repository/Record.class.php';
//require_once 'classes/repository/Collection.class.php';

		define('DB_HOST', 'localhost');
		define('DB_NAME', 'floe_test');
		define('DB_USER', 'default');
		define('DB_PASS', 'launch');

class ActiveModelTest extends UnitTestCase {
	function ActiveModelTest() {
		parent::UnitTestCase();
	}
}

class Dog extends Record {
	function __define() {
		$this->property("age", "integer");
		$this->property("breed", "string");
		$this->property("name", "string");
	}
	function isPuppy() {
		return ($this->age < 3);
	}
}

//class Dogs extends Collection { }

class ModelWithBasicPropertiesTest extends ActiveModelTest {

	function setUp() {
		$dogModel = new Dog();
		$adaptor = StorageAdaptor::instance();
		$adaptor->createTable("dogs", $dogModel->properties());
	}

	function testCreateAndStoreNewInstance() {
		$dog = new Dog();
		$dog->age = 2;
		$dog->breed = "Terrier";
		$dog->name = "Jack";
		$this->assertTrue($dog->save());
		$id = $dog->id;
		unset($dog);
		$adaptor = StorageAdaptor::instance();
		$adaptor->selectById("dogs", $id);
		$dog = $adaptor->getRecord();
		$this->assertIsA($dog, 'Dog');
		$this->assertEqual(2, $dog->age);
		$this->assertEqual("Terrier", $dog->breed);
		$this->assertEqual("Jack", $dog->name);
		$this->assertTrue($dog->isPuppy());
		
		
		
	}
	
	function tearDown() {
		$adaptor = StorageAdaptor::instance();
		$adaptor->dropTable("dogs");
	}

}

/*
class Cat extends Record {
	function __define() {
		$this->property("age", "integer");
		$this->property("name", "string");
	}
}

class MumCat extends Cat {
	function __define() {
		parent::__define();
		$this->hasChildren("kittens");
	}
}

class Kitten extends Record {
	function __define() {
		parent::__define();
		$this->hasParent("MumCat");
	}
}
*/

class Project extends Record {
	function __define() {
		$this->property("name", "string");
		$this->hasMany("tasks");
	}
}

class Task extends Record {
	function __define() {
		$this->property("name", "string");
		$this->belongsTo("project");
	}
}

class QueryLogger implements LogHandler {
	var $history = array();
	function emit($level, $message) {
		if ($level == Level::Info) {
			$this->history[] = $message;
		}
	}
	function __destruct() {
		foreach($this->history as $message) {
			echo $message, "<br>";
		}
	}
}


class OneToManyAssociationTest extends ActiveModelTest {

	function setUp() {
		//EventLogger::handler(new QueryLogger());
		$projectModel = new Project();
		$taskModel = new Task();
		$adaptor = StorageAdaptor::instance();
		$adaptor->createTable("projects", $projectModel->properties());
		$adaptor->createTable("tasks", $taskModel->properties());
	}

	function testCreateAndStoreNewInstanceWithRelations() {
		$project = new Project();
		$project->name = "Default Project";
		
		$task = new Task();
		$task->name = "do something";
		
		$task2 = new Task();
		$task2->name = "something else";
		
		$project->tasks = $task;
		$project->tasks = $task2;
		
		
		
		$this->assertTrue($project->save());
		$id = $project->id;
		unset($project);
		
		$adaptor = StorageAdaptor::instance();
		$adaptor->selectById("projects", $id);
		$proj = $adaptor->getRecord();
		
		$this->assertEqual("Default Project", $proj->name);
		$this->assertEqual(2, count($proj->tasks));
		$this->assertEqual("do something", $proj->tasks[0]->name);
		$this->assertEqual("something else", $proj->tasks[1]->name);
	}
	
	function tearDown() {
		$adaptor = StorageAdaptor::instance();
		$adaptor->dropTable("projects");
		$adaptor->dropTable("tasks");
		//EventLogger::pop();
	}

}

class Post extends Record {
	
	function __define() {
		$this->property("title", "string");
		$this->hasManyRelations("topics");
	}
	
}

class Topic extends Record {
	
	function __define() {
		$this->property("name", "string");
		$this->hasManyRelations("posts");
	}
	
}

class ManyToManyRelationshipTest extends UnitTestCase {
	
	function setUp() {
		$post = new Post();
		$topic = new Topic();
		$adaptor = StorageAdaptor::instance();
		$adaptor->createTable("posts", $post->properties());
		$adaptor->createTable("topics", $topic->properties());
		$adaptor->createTable("posts_topics", array("post_id" => "integer", "topic_id" => "integer"));
	}
	
	function testCreateAndStoreNewInstance() {
		$post = new Post();
		$post->title = "Hello World";
		
		$topic1 = new Topic();
		$topic1->name = "hello";
		
		$topic2 = new Topic();
		$topic2->name = "world";
		
		$post->topics = $topic1;
		$post->topics = $topic2;
		
		$this->assertTrue($post->save());
		$id = $post->id;
		unset($post);
		$adaptor = StorageAdaptor::instance();
		$adaptor->selectById("posts", $id);
		$post = $adaptor->getRecord();
		
		$this->assertEqual("Hello World", $post->title);
		$this->assertEqual("hello", $post->topics[0]->name);
		$this->assertEqual("world", $post->topics[1]->name);
	}

	function tearDown() {
		$adaptor = StorageAdaptor::instance();
		$adaptor->dropTable("posts");
		$adaptor->dropTable("topics");
		$adaptor->dropTable("posts_topics");
	}
	
}


?>