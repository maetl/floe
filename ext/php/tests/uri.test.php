<?php
require_once "simpletest/autorun.php";
require_once 'server/UriPath.class.php';

class UriPathReaderTest extends UnitTestCase {

	function testEmptyBasePath() {
		$path = new UriPath("/");
		$this->assertTrue($path->isEmpty());
		$this->assertEqual("/", $path->path());
		$this->assertEqual(0, count($path->segments()));
		$this->assertEqual("", $path->segment(0));
		$this->assertEqual("", $path->segment(1));
		$this->assertEqual("", $path->identity());
		$this->assertEqual("", $path->resource());
	}
	
	function testSingleSegmentPath() {
		$path = new UriPath("/entry");
		$this->assertEqual(1, count($path->segments()));
		$this->assertEqual("/entry", $path->path());
		$this->assertEqual("entry", $path->identity());
		$this->assertEqual("entry", $path->resource());
	}
	
	function testMultiSegmentPath() {
		$path = new UriPath("/content/entry/title");
		$parts = $path->segments();
		$this->assertEqual(3, count($parts));
		$this->assertEqual("title", $parts[2]);
		$this->assertEqual("entry", $parts[1]);
		$this->assertEqual("content", $parts[0]);
	}
	
	function testSegmentPathMethod() {
		$path = new UriPath("/content/entry/subject");
		$this->assertEqual("content", $path->segment(0));
		$this->assertEqual("entry", $path->segment(1));
		$this->assertEqual("subject", $path->segment(2));
	}
	
	function testMultiSegmentPathMethods() {
		$path = new UriPath("/content/entry/subject/id");
		$this->assertEqual(array("content", "entry", "subject", "id"), $path->segments());
		$this->assertEqual(array("content", "entry", "subject", "id"), $path->segmentsFrom(0));
		$this->assertEqual(array("entry", "subject", "id"), $path->segmentsFrom(1));
		$this->assertEqual(array("subject", "id"), $path->segmentsFrom(2));
		$this->assertEqual(array("id"), $path->segmentsFrom(3));
	}
	
	function testFragmentIdentifier() {
		$path = new UriPath("/content/entry/title#heading");
		$this->assertEqual("title#heading", $path->resource());
		$this->assertEqual("title", $path->identity());
		$this->assertEqual("heading", $path->fragment());
	}
	
	function testPlusEncodedPath() {
		$path = new UriPath("/entries/an+encoded+title");
		$this->assertEqual("an encoded title", $path->identity());
	}
	
	function testAutoEncodedPath() {
		$path = new UriPath("/entries/an%20encoded%20title");
		$this->assertEqual("an encoded title", $path->identity());
	}	
	
	function testQueryString() {
		$path = new UriPath("/?entry=title&id=123");
		$this->assertTrue($path->isEmpty());
		$this->assertEqual(2, count($path->parameters()));
		$this->assertEqual("title", $path->parameter("entry"));
		$this->assertEqual("123", $path->parameter("id"));
	}
	
	function testSingleSegmentPathWithQueryString() {
		$path = new UriPath("/search?q=a+search+phrase");
		$this->assertEqual("search", $path->resource());
		$this->assertEqual("q=a+search+phrase", $path->query());
		$this->assertEqual("a search phrase", $path->parameter("q"));
	}
	
	function testMultiSegmentPathWithQueryString() {
		$path = new UriPath("/entries/2005?page=3&tag=design");
		$this->assertEqual(2, count($path->segments()));
		$this->assertEqual("2005", $path->resource());
		$this->assertEqual("2005", $path->identity());
		$this->assertEqual("page=3&tag=design", $path->query());
		$this->assertEqual(2, count($path->parameters()));
		$this->assertEqual("3", $path->parameter("page"));
		$this->assertEqual("design", $path->parameter("tag"));
	}
	
	function testQueryParametersAsArray() {
		$path = new UriPath("/base/object?q[0]=hello&q[1]=world&m[0]=foo&m[1]=bar");
		$this->assertEqual(2, count($path->parameters()));
		$q = $path->parameter('q');
		$m = $path->parameter('m');
		$this->assertIsA($q, 'Array');
		$this->assertEqual("hello", $q[0]);
		$this->assertEqual("world", $q[1]);
		$this->assertEqual("foo", $m[0]);
		$this->assertEqual("bar", $m[1]);
	}

	function testMultiSegmentPathWithExtension() {
		$path = new UriPath("/books/title.txt");
		$this->assertEqual("txt", $path->extension());
		$this->assertEqual("title.txt", $path->resource());
		$this->assertEqual("title", $path->identity());
	}
	
	function testMultiSegmentPathWithAspect() {
		$path = new UriPath("/base/object;aspect");
		$this->assertEqual("aspect", $path->aspect());
		$this->assertEqual("object", $path->resource());
		$this->assertEqual("object", $path->identity());
	}
	
	function testMultiSegmentPathWithAspectAndExtension() {
		$path = new UriPath("/base/identity.xml;edit");
		$this->assertEqual("edit", $path->aspect());
		$this->assertEqual("identity.xml", $path->resource());
		$this->assertEqual("xml", $path->extension());
		$this->assertEqual("identity", $path->identity());
	}
	
	function testCanRemoveBadCharacters() {
		$path = new UriPath('/base/object.php?key=value<?php echo $_REQUEST; ?>');
		$this->assertEqual("object.php", $path->resource());
		$this->assertEqual("value", $path->parameter("key"));
	}

}

?>