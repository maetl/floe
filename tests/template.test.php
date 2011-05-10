<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../src/server/template/PhpTemplate.class.php';
//require_once dirname(__FILE__).'/../src/server/template/SmartyTemplate.class.php';
require_once dirname(__FILE__).'/../src/server/template/TwigTemplate.class.php';

if (!defined('FloeApp_Templates')) define('FloeApp_Templates', dirname(__FILE__).'/resources/templates/');

class PhpTemplateTest extends UnitTestCase {
	
	function testTemplateRenderPlain() {
		$template = new PhpTemplate();
		$body = $template->render('hello');
		$this->assertPattern("/<h1>Hello World<\/h1>/", $body);
	}
	
	function testTemplateRenderVars() {
		$template = new PhpTemplate();
		$template->assign('booleanVar', true);
		$template->assign('integerVar', 999);
		$template->assign('stringVar', 'sesame');
		$template->assign('arrayVar', array("green","eggs","boiled","ham"));
		$object = new stdClass; $object->var = "value";
		$template->assign('objectVar', $object);
		$body = $template->render('vars');
		$this->assertPattern("/<li>booleanVar: 1<\/li>/", $body);
		$this->assertPattern("/<li>integerVar: 999<\/li>/", $body);
		$this->assertPattern("/<li>stringVar: sesame<\/li>/", $body);
		$this->assertPattern("/<li>arrayVar: green,eggs,boiled,ham<\/li>/", $body);
		$this->assertPattern("/<li>objectVar: value<\/li>/", $body);
	}
	
	function testTemplateRenderWrappedLayout() {
		$template = new PhpTemplate();
		$template->wrap('wrapper');
		$template->assign('foo', 'bar');
		$body = $template->render('hello');
		$this->assertPattern("/<span>bar<\/span>/", $body);
		$this->assertPattern("/<div><h1>Hello World<\/h1><\/div>/", $body);
	}
	
	function testTemplateRenderEmbeddedSubTemplate() {
		$template = new PhpTemplate();
		$template->assign('foo', 'parent');
		$template->assign('bar', 'child');
		$body = $template->render('parent');
		$this->assertPattern("/<span>parent<\/span>/", $body);
		$this->assertPattern("/<span>child<\/span>/", $body);
		$this->assertPattern("/<h1>Hello World<\/h1>/", $body);
	}
}

class SmartyTemplateTest extends UnitTestCase {
	
	function skip() {
		$this->skipIf(true);
	}
	
	function testTemplateRenderPlain() {
		$template = new SmartyTemplate();
		$body = $template->render('hello');
		$this->assertPattern("/<h1>Hello World<\/h1>/", $body);
	}
	
	function testTemplateRenderVars() {
		$template = new SmartyTemplate();
		$template->assign('booleanVar', true);
		$template->assign('integerVar', 999);
		$template->assign('stringVar', 'sesame');
		$template->assign('arrayVar', array("green","eggs","boiled","ham"));
		$object = new stdClass; $object->var = "value";
		$template->assign('objectVar', $object);
		$body = $template->render('vars');
		$this->assertPattern("/<li>booleanVar: 1<\/li>/", $body);
		$this->assertPattern("/<li>integerVar: 999<\/li>/", $body);
		$this->assertPattern("/<li>stringVar: sesame<\/li>/", $body);
		$this->assertPattern("/<li>arrayVar: green,eggs,boiled,ham<\/li>/", $body);
		$this->assertPattern("/<li>objectVar: value<\/li>/", $body);
	}
	
	function testTemplateRenderWrappedLayout() {
		$template = new SmartyTemplate();
		$template->wrap('wrapper');
		$template->assign('foo', 'bar');
		$body = $template->render('hello');
		$this->assertPattern("/<span>bar<\/span>/", $body);
		$this->assertPattern("/<div><h1>Hello World<\/h1><\/div>/", $body);
	}	
}

class TwigTemplateTest extends UnitTestCase {
	
	function testTemplateRenderPlain() {
		$template = new TwigTemplate();
		$body = $template->render('hello');
		$this->assertPattern("/<h1>Hello World<\/h1>/", $body);
	}
	
	function testTemplateRenderVars() {
		$template = new TwigTemplate();
		$template->assign('booleanVar', true);
		$template->assign('integerVar', 999);
		$template->assign('stringVar', 'sesame');
		$template->assign('arrayVar', array("green","eggs","boiled","ham"));
		$object = new stdClass; $object->var = "value";
		$template->assign('objectVar', $object);
		$body = $template->render('vars.twig');
		$this->assertPattern("/<li>booleanVar: 1<\/li>/", $body);
		$this->assertPattern("/<li>integerVar: 999<\/li>/", $body);
		$this->assertPattern("/<li>stringVar: sesame<\/li>/", $body);
		$this->assertPattern("/<li>arrayVar: green,eggs,boiled,ham<\/li>/", $body);
		$this->assertPattern("/<li>objectVar: value<\/li>/", $body);
	}
}

?>