<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Patrick Finkbeiner <finkbeiner.patrick@gmail.com>
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
 * MailService.
 *
 * @package Finewsletters
 */ 
class Tx_Finewsletter_Service_MailService implements t3lib_Singleton {

	/**
	 * Send a mail via the supplied mailer instance
	 *
	 * @param t3lib_mail_Message $mailer
	 * @param string $recipient
	 * @param string $subject
	 * @param string $messageHtml
	 * @param string $messagePlaintext
	 * @param array $mailSettings contains senderEmail, senderName
	 * @return boolean
	 */
	public function sendMail(t3lib_mail_Message $mailer, $recipient, $subject, $messageHtml = '', $messagePlaintext = '', array $mailSettings) {

		$status = TRUE;

		// Add this for testing… no messages are getting send out!
		if ($mailSettings['test'] === 'true') {
			$now = new DateTime('now');
			$mail = 'From: ' . $mailSettings['senderEmail'] . ' ' . $mailSettings['senderName'] . chr(10) .
				'To: ' . $recipient . chr(10) .
				'Subject: ' . $subject . chr(10) .
				'Date: ' . $now->format('Y-m-d H:i:s') . chr(10) .
				'--------------------------------------' . chr(10) .
				$messagePlaintext . chr(10) .
				'--------------------------------------' . chr(10) .
				chr(10);

			file_put_contents(t3lib_div::getFileAbsFileName('EXT:finewsletter/mails.txt'), $mail, FILE_APPEND);
		} else {
			try {
				$mailer->setFrom(Array($mailSettings['senderEmail'] => $mailSettings['senderName']))
					->setTo($recipient)
					->setSubject($subject)
					->setBody($messageHtml, 'text/html')
					->addPart($messagePlaintext, 'text/plain')
					->setSender($mailSettings['senderEmail'], $mailSettings['senderName'])
					->send();
			} catch(Exception $e) {
				t3lib_div::sysLog($e->getMessage(), 'finewsletter' ,3);
				$status = !$status;
			}
		}

		return $status;
	}

	/**
	 * Generate content with a FLUID template
	 *
	 * @param array $templateLocations array with two keys, html and plain
	 * @param array $contents Content in key:value form…
	 * @param boolean $html generate html?
	 * @param boolean $plain generate plain text?
	 * @return array
	 */
	public function generateEmailContent(array $templateLocations, array $contents, $html = TRUE, $plain = TRUE) {
		$mailContents = Array(
			'html' => NULL,
			'plain' => NULL
		);

		if ($html === TRUE) {
			$view = t3lib_div::makeInstance('Tx_Fluid_View_StandaloneView');
			$view->setTemplatePathAndFilename(t3lib_div::getFileAbsFileName($templateLocations['html']));
			foreach ($contents as $key => $value) {
				$view->assign($key, $value);
			}
			$mailContents['html'] = $view->render();
		}

		if ($plain === TRUE) {
			$view = t3lib_div::makeInstance('Tx_Fluid_View_StandaloneView');
			$view->setTemplatePathAndFilename(t3lib_div::getFileAbsFileName($templateLocations['plain']));
			foreach ($contents as $key => $value) {
				$view->assign($key, $value);
			}
			$mailContents['plain'] = $view->render();
		}

		return $mailContents;
	}
}
?>