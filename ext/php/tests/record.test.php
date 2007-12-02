<?php
require_once 'simpletest/autorun.php';
require_once 'simpletest/mock_objects.php';
require_once 'classes/repository/Record.class.php';

if (!defined('DB_HOST')) {
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'floe_test');
	define('DB_USER', 'default');
	define('DB_PASS', 'launch');
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

class ModelWithBasicPropertiesTest extends UnitTestCase {

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


class OneToManyAssociationTest extends UnitTestCase {

	function setUp() {
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


class Player extends Record {
	
	function __define() {
		$this->property('name', 'string');
	}
	
}

class Footballer extends Player {
	
	function __define() {
		$this->property('club', 'string');
	}
	
}

class Cricketer extends Player {
	
	function __define() {
		$this->property('topScore', 'int');
	}
	
}

class Bowler extends Cricketer {
	
	function __define() {
		$this->property('wicketsTaken', 'int');
	}
	
}


class SingleTableInheritanceTest extends UnitTestCase {
	
	function setUp() {
		$adaptor = StorageAdaptor::instance();
		$adaptor->createTable("players", array('name'=>'string', 'topScore'=>'int', 'wicketsTaken'=>'int', 'club'=>'string', 'type'=>'string'));
	}
	
	function testCanAccessBaseRecord() {
		$player = new Player();
		$player->name = "Ritchie McCaw";
		$player->save();
		$id = $player->id;
		unset($player);
		$player = new Player($id);
		$this->assertEqual("Ritchie McCaw", $player->name);
	}
	
	function testCanAccessInheritedRecord() {
		$player = new Cricketer();
		$player->name = "Ricky Ponting";
		$player->topScore = 314;
		$player->save();
		$id = $player->id;
		unset($player);
		$player = new Cricketer($id);
		$this->assertEqual("Ricky Ponting", $player->name);
		$this->assertEqual(314, $player->topScore);
	}
	
	function testCanAccessMultipleInheritedRecords() {
		$player = new Cricketer();
		$player->name = "Ricky Ponting";
		$player->topScore = 257;
		$player->save();
		$punter = $player->id;
		unset($player);
		
		$player = new Bowler();
		$player->name = "Andrew Flintoff";
		$player->topScore = 167;
		$player->wicketsTaken = 297;
		$player->save();
		$freddie = $player->id;
		unset($player);
		
		$player = new Footballer();
		$player->name = "David Beckham";
		$player->club = "LA Galaxy";
		$player->save();
		$becks = $player->id;
		unset($player);
		
		$player = new Cricketer($punter);
		$this->assertEqual("Ricky Ponting", $player->name);
		$this->assertEqual(257, $player->topScore);
		$this->assertEqual("Cricketer", $player->type);
		
		$player = new Bowler($freddie);
		$this->assertEqual("Andrew Flintoff", $player->name);
		$this->assertEqual(167, $player->topScore);
		$this->assertEqual(297, $player->wicketsTaken);
		$this->assertEqual("Bowler", $player->type);
		
		$player = new Footballer($becks);
		$this->assertEqual("David Beckham", $player->name);
		$this->assertEqual("LA Galaxy", $player->club);
		$this->assertEqual("Footballer", $player->type);
	}
	
	function tearDown() {
		$adaptor = StorageAdaptor::instance();
		$adaptor->dropTable("players");
	}
	
}

?>