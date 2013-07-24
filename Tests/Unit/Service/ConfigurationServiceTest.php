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
*  the Free Software Foundation; either version 3 of the License, or
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
 * Test case for class Tx_Finewsletter_Service_ConfigurationService.
 *
 * @package finewsletter
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 * @author Patrick Finkbeiner <finkbeiner.patrick@gmail.com>, finkbeiner.me
 */
class Tx_Finewsletter_Service_ConfigurationServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Finewsletter_Service_ConfigurationService
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_Finewsletter_Service_ConfigurationService();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function buildLanguageArrayReturnsInvalidArray() {
		$invalidArray = array('en', 'English');

		$this->assertNotEquals($invalidArray, $this->fixture->buildLanguageArray('en:English'));
	}

	/**
	 * @test
	 */
	public function buildLanguageArrayReturnsValidArray() {
		$validArray = array('en' => 'English');

		$this->assertEquals($validArray, $this->fixture->buildLanguageArray('en:English'));
	}

	/**
	 * @test
	 */
	public function buildLanguageArrayReturnsValidMultipleArray() {
		$validArray = array(
			'en' => 'English',
			'de' => 'German',
			'es' => 'Spanish'
		);

		$this->assertEquals($validArray, $this->fixture->buildLanguageArray('en:English | de:German | es:Spanish'));
	}

	/**
	 * @test
	 */
	public function buildLanguageArrayReturnsInvalidMultipleArray() {
		$invalidArray = array( 'en','English', 'de','German', 'es','Spanish');

		$this->assertNotEquals($invalidArray, $this->fixture->buildLanguageArray('en:English | de:German | es:Spanish'));
	}
}
?>