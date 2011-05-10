<?php
require_once 'simpletest/autorun.php';

require_once dirname(__FILE__).'/../src/repository/rules/EmailRule.class.php';
require_once dirname(__FILE__).'/../src/repository/rules/AlphanumericRule.class.php';
require_once dirname(__FILE__).'/../src/repository/rules/MatchingRule.class.php';
require_once dirname(__FILE__).'/../src/repository/rules/NumericRule.class.php';
require_once dirname(__FILE__).'/../src/repository/rules/SocialSecurityNumberRule.class.php';

class EmailRuleTest extends UnitTestCase {

	function testAcceptsValidEmails() {
		$rule = new EmailRule();
		$this->assertTrue($rule->validate("you@example.com"));
		$this->assertTrue($rule->validate("first.last@example.com"));
		$this->assertTrue($rule->validate("you+sub@example.com"));
		$this->assertFalse($rule->validate("x@is.a.valid.address"));		
	}

	function testRejectsInvalidEmails() {
		$rule = new EmailRule();
		$this->assertFalse($rule->validate("you@example"));
		$this->assertFalse($rule->validate("first.com"));
		$this->assertFalse($rule->validate("@"));
	}
}

class AlphanumericRuleTest extends UnitTestCase {

	function testAcceptsOnlyAlphaChars() {
		$rule = new AlphanumericRule();
		$this->assertTrue($rule->validate("abcde"));
		$this->assertTrue($rule->validate("abcde123"));
		$this->assertTrue($rule->validate("1234"));
	}
	
	function testRejectsNonAlphaChars() {
		$rule = new AlphanumericRule();
		$this->assertFalse($rule->validate("*@#$"));
		$this->assertFalse($rule->validate(""));
		$this->assertFalse($rule->validate("..."));
	}
}

class MatchingRuleTest extends UnitTestCase {

	function testAcceptsMatching() {
		$rule = new MatchingRule("hello");
		$this->assertTrue($rule->validate("hello"));
		$rule = new MatchingRule(1);
		$this->assertTrue($rule->validate(1));
	}
	
	function testRejectsNonMatching() {
		$rule = new MatchingRule("hello");
		$this->assertFalse($rule->validate("world"));
		$rule = new MatchingRule(1);
		$this->assertFalse($rule->validate(2));
		// empty values are considered invalid
		$rule = new MatchingRule("");
		$this->assertFalse($rule->validate(""));
	}
}

class NumericRuleTest extends UnitTestCase {

	function testAcceptsAllNumericTypes() {
		$rule = new NumericRule();
		$this->assertTrue($rule->validate(1));
		$this->assertTrue($rule->validate(2.0));
		$this->assertTrue($rule->validate(9999));
		$this->assertTrue($rule->validate("10"));
		$this->assertTrue($rule->validate(pi()));
	}

	function testRejectsAllNonNumericTypes() {
		$rule = new NumericRule();
		$this->assertFalse($rule->validate("hello"));
		$this->assertFalse($rule->validate(new stdClass));
		$this->assertFalse($rule->validate(array(1)));
	}
}

class SocialSecurityNumberRuleTest extends UnitTestCase {

	function testAcceptsValidSSNS() {
		$rule = new SocialSecurityNumberRule();
		$this->assertTrue($rule->validate("762-65-4320"));
		$this->assertTrue($rule->validate("132-11-4320"));
	}
	
	function testRejectsInvalidSSNS() {
		$rule = new SocialSecurityNumberRule();
		$this->assertFalse($rule->validate(""));
		$this->assertFalse($rule->validate("aaa"));
		$this->assertFalse($rule->validate("000-00-0000"));
		$this->assertFalse($rule->validate("666-11-4320"));
		$this->assertFalse($rule->validate("774-11-4320"));
	}

}

?>