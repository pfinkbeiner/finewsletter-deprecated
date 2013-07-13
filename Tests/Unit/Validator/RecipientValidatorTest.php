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
 * Test case for class Tx_Finewsletter_Service_MailService.
 *
 * @package finewsletter
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 * @author Patrick Finkbeiner <finkbeiner.patrick@gmail.com>, finkbeiner.me
 */
class Tx_Finewsletter_Validator_RecipientValidatorTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Finewsletter_Validator_RecipientValidatorTest
	 */
	protected $fixture;

	/**
	 * @var Tx_Finewsletter_Domain_Model_Recipient
	 */
	protected $recipient;

	public function setUp() {
		$this->fixture = new Tx_Finewsletter_Validator_RecipientValidator();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function isEmailValidReturnsFalseOnFakeEmail() {
		$this->assertSame(
			FALSE,
			$this->fixture->isEmailValid('not a real email')
		);
	}

	/**
	 * @test
	 */
	public function isEmailValidReturnsTrueOnRealEmail() {
		$this->assertSame(
			TRUE,
			$this->fixture->isEmailValid('john@doe.com')
		);
	}
}
?>