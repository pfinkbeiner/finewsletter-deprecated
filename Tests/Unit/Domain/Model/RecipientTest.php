<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Patrick Finkbeiner <finkbeiner.patrick@gmail.com>, finkbeiner.me
 *  			
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Test case for class Tx_Finewsletter_Domain_Model_Recipient.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Simple Newsletter Registration
 *
 * @author Patrick Finkbeiner <finkbeiner.patrick@gmail.com>
 */
class Tx_Finewsletter_Domain_Model_RecipientTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_Finewsletter_Domain_Model_Recipient
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_Finewsletter_Domain_Model_Recipient();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getEmailReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setEmailForStringSetsEmail() { 
		$this->fixture->setEmail('john@doe.com');

		$this->assertSame(
			'john@doe.com',
			$this->fixture->getEmail()
		);
	}

	/**
	 * @test
	 */
	public function getNameReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setNameForStringSetsName() { 
		$this->fixture->setName('John');

		$this->assertSame(
			'John',
			$this->fixture->getName()
		);
	}

	/**
	 * @test
	 */
	public function getFirstNameReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setFirstNameForStringSetsFirstName() { 
		$this->fixture->setFirstName('John');

		$this->assertSame(
			'John',
			$this->fixture->getFirstName()
		);
	}

	/**
	 * @test
	 */
	public function getLastNameReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setLastNameForStringSetsLastName() { 
		$this->fixture->setLastName('Doe');

		$this->assertSame(
			'Doe',
			$this->fixture->getLastName()
		);
	}

	/**
	 * @test
	 */
	public function getLanguageReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setLanguageForStringSetsLanguage() { 
		$this->fixture->setLanguage('English');

		$this->assertSame(
			'English',
			$this->fixture->getLanguage()
		);
	}
	
	/**
	 * @test
	 */
	public function getActiveReturnsInitialValueForBoolean() { 
		$this->assertSame(
			FALSE,
			$this->fixture->getActive()
		);
	}

	/**
	 * @test
	 */
	public function setActiveForBooleanSetsActive() { 
		$this->fixture->setActive(TRUE);

		$this->assertSame(
			TRUE,
			$this->fixture->getActive()
		);
	}
	
}
?>