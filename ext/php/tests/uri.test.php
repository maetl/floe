<?php
require_once 'server/UriPath.class.php';

class UriPathReaderTest extends UnitTestCase {

	function testEmptyBasePath() {
		$path = new UriPath("/");
		$this->assertTrue($path->isEmpty());
		$this->assertEqual("/", $path->getPath());
		$this->assertEqual(0, count($path->getSegments()));
		$this->assertEqual("", $path->getIdentity());
		$this->assertEqual("", $path->getResource());
	}
	
	function testSingleSegmentPath() {
		$path = new UriPath("/entry");
		$this->assertEqual(1, count($path->getSegments()));
		$this->assertEqual("/entry", $path->getPath());
		$this->assertEqual("entry", $path->getIdentity());
		$this->assertEqual("entry", $path->getResource());
	}
	
	function testMultiSegmentPath() {
		$path = new UriPath("/content/entry/title");
		$parts = $path->getSegments();
		$this->assertEqual(3, count($parts));
		$this->assertEqual("title", $parts[0]);
		$this->assertEqual("entry", $parts[1]);
		$this->assertEqual("content", $parts[2]);
	}
	
	function testFragmentIdentifier() {
		$path = new UriPath("/content/entry/title#heading");
		$this->assertEqual("title#heading", $path->getResource());
		$this->assertEqual("title", $path->getIdentity());
		$this->assertEqual("heading", $path->getFragment());
	}
	
	function testPlusEncodedPath() {
		$path = new UriPath("/entries/an+encoded+title");
		$this->assertEqual("an encoded title", $path->getIdentity());
	}
	
	function testAutoEncodedPath() {
		$path = new UriPath("/entries/an%20encoded%20title");
		$this->assertEqual("an encoded title", $path->getIdentity());
	}	
	
	function testQueryString() {
		$path = new UriPath("/?entry=title&id=123");
		$this->assertTrue($path->isEmpty());
		$this->assertEqual(2, count($path->getParameters()));
		$this->assertEqual("title", $path->getParameter("entry"));
		$this->assertEqual("123", $path->getParameter("id"));
	}
	
	function testSingleSegmentPathWithQueryString() {
		$path = new UriPath("/search?q=a+search+phrase");
		$this->assertEqual("search", $path->getResource());
		$this->assertEqual("q=a+search+phrase", $path->getQuery());
		$this->assertEqual("a search phrase", $path->getParameter("q"));
	}
	
	function testMultiSegmentPathWithQueryString() {
		$path = new UriPath("/entries/2005?page=3&tag=design");
		$this->assertEqual(2, count($path->getSegments()));
		$this->assertEqual("2005", $path->getResource());
		$this->assertEqual("2005", $path->getIdentity());
		$this->assertEqual("page=3&tag=design", $path->getQuery());
		$this->assertEqual(2, count($path->getParameters()));
		$this->assertEqual("3", $path->getParameter("page"));
		$this->assertEqual("design", $path->getParameter("tag"));
	}
	
	function testQueryParametersAsArray() {
		$path = new UriPath("/base/object?q[0]=hello&q[1]=world&m[0]=foo&m[1]=bar");
		$this->assertEqual(2, count($path->getParameters()));
		$q = $path->getParameter('q');
		$m = $path->getParameter('m');
		$this->assertIsA($q, 'Array');
		$this->assertEqual("hello", $q[0]);
		$this->assertEqual("world", $q[1]);
		$this->assertEqual("foo", $m[0]);
		$this->assertEqual("bar", $m[1]);
	}

	function testMultiSegmentPathWithExtension() {
		$path = new UriPath("/books/title.txt");
		$this->assertEqual("txt", $path->getExtension());
		$this->assertEqual("title.txt", $path->getResource());
		$this->assertEqual("title", $path->getIdentity());
	}
	
	function testMultiSegmentPathWithAspect() {
		$path = new UriPath("/base/object;aspect");
		$this->assertEqual("aspect", $path->getAspect());
		$this->assertEqual("object", $path->getResource());
		$this->assertEqual("object", $path->getIdentity());
	}
	
	function testMultiSegmentPathWithAspectAndExtension() {
		$path = new UriPath("/base/identity.xml;edit");
		$this->assertEqual("edit", $path->getAspect());
		$this->assertEqual("identity.xml", $path->getResource());
		$this->assertEqual("xml", $path->getExtension());
		$this->assertEqual("identity", $path->getIdentity());
	}
	
	function testCanRemoveBadCharacters() {
		$path = new UriPath('/base/object.php?key=value<?php echo $_REQUEST; ?>');
		$this->assertEqual("object.php", $path->getResource());
		$this->assertEqual("value", $path->getParameter("key"));
	}

}

?>