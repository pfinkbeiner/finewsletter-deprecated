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
class Tx_Finewsletter_Service_MailServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Finewsletter_Service_MailService
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_Finewsletter_Service_MailService();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function sendMailForTestFlagTrueDoesNotCallSendOnTheMailerObject() {
		// Set testing state.
		$mailSettings = array(
			'test' => 'true'
		);

		$mailerMock = $this->getMock('t3lib_mail_Message', array('send'));
		$mailerMock->expects($this->never())
			->method('send');

		$this->assertSame(
			TRUE,
			$this->fixture->sendMail($mailerMock, 'test@example.net', 'My Subject', '<p>Some HTML</p>', 'Some plain text', $mailSettings)
		);
	}

	/**
	 * @test
	 */
	public function sendMailForTestFlagFalseDoesCallSendOnTheMailerObject() {
		$mailSettings = array(
			'test' => 'false',
			'senderEmail' => 'from@example.net',
			'senderName'  => 'From',
		);

		$mailerMock = $this->getMock('t3lib_mail_Message', array('setFrom', 'setTo', 'setSubject', 'setBody', 'addPart', 'setSender', 'send'));
		$mailerMock->expects($this->once())
			->method('setFrom')
			->with(array('from@example.net' => 'From'))
			->will($this->returnValue($mailerMock));
		$mailerMock->expects($this->once())
			->method('setTo')
			->with('recipient@example.net')
			->will($this->returnValue($mailerMock));
		$mailerMock->expects($this->once())
			->method('setSubject')
			->with('My Subject')
			->will($this->returnValue($mailerMock));
		$mailerMock->expects($this->once())
			->method('setBody')
			->with('<p>Some HTML</p>', 'text/html')
			->will($this->returnValue($mailerMock));
		$mailerMock->expects($this->once())
			->method('addPart')
			->with('Some plain text', 'text/plain')
			->will($this->returnValue($mailerMock));
		$mailerMock->expects($this->once())
			->method('setSender')
			->with('from@example.net', 'From')
			->will($this->returnValue($mailerMock));
		$mailerMock->expects($this->once())
			->method('send');

		$this->assertSame(
			TRUE,
			$this->fixture->sendMail($mailerMock, 'recipient@example.net', 'My Subject', '<p>Some HTML</p>', 'Some plain text', $mailSettings)
		);
	}

	/**
	 * @test
	 */
	public function sendMailForEmptyRecipientReturnsFalse() {
		$mailSettings = array(
			'test' => 'false',
		);

		$mailer = new t3lib_mail_Message();

		$this->assertSame(
			FALSE,
			$this->fixture->sendMail($mailer, NULL, NULL, NULL, NULL, $mailSettings)
		);
	}
}
?>