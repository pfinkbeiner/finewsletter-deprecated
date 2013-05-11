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
 *
 *
 * @package finewsletter
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_Finewsletter_Controller_RecipientController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * recipientRepository
	 *
	 * @var Tx_Finewsletter_Domain_Repository_RecipientRepository
	 */
	protected $recipientRepository;

	/**
	 * injectRecipientRepository
	 *
	 * @param Tx_Finewsletter_Domain_Repository_RecipientRepository $recipientRepository
	 * @return void
	 */
	public function injectRecipientRepository(Tx_Finewsletter_Domain_Repository_RecipientRepository $recipientRepository) {
		$this->recipientRepository = $recipientRepository;
	}

	/**
	 * action new
	 *
	 * @param $newRecipient
	 * @dontvalidate $newRecipient
	 * @return void
	 */
	public function subscribeAction(Tx_Finewsletter_Domain_Model_Recipient $newRecipient = NULL) {
		if ($newRecipient == NULL) { // workaround for fluid bug ##5636
			$newRecipient = t3lib_div::makeInstance('Tx_Finewsletter_Domain_Model_Recipient');
		}
		$this->view->assign('newRecipient', $newRecipient);
	}

	/**
	 * action create
	 *
	 * @param $newRecipient
	 * @return void
	 */
	public function createAction(Tx_Finewsletter_Domain_Model_Recipient $newRecipient) {
		$userValidator = $this->objectManager->get('Tx_Finewsletter_Validator_RecipientValidator');
		// Prevent flashMessage flood
		$this->flashMessageContainer->flush();

		$email = $newRecipient->getEmail();

		if($userValidator->isEmailValid($email) === FALSE) {
			$this->flashMessageContainer->add('Keine gültige E-Mail Addresse.');
			$this->redirect('subscribe');
		} elseif($userValidator->doesEmailExist($email) === TRUE) {
			// Check if user is already subscribed (active)
			$newRecipient = $this->recipientRepository->findOneByEmail($newRecipient->getEmail());
			if ($newRecipient->isActive() === FALSE) {
				$this->flashMessageContainer->add('E-Mail Addresse bereits vorhanden. Erneute verifizierungsemail wurde verschickt.');

				$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');
				$mailService = $this->objectManager->get('Tx_Finewsletter_Service_MailService');
				$emailContent = $mailService->generateEmailContent(array(
					'html'  => $this->settings['mail']['subscription']['templates']['html'],
					'plain' => $this->settings['mail']['subscription']['templates']['plain']
				), array(
					'verifyLink' => $securityService->generateVerifyLink($newRecipient, $this->uriBuilder) 
				), TRUE, TRUE);

				$mailService->sendMail(
					$this->objectManager->get('t3lib_mail_Message'),
					$email,
					$this->settings['mail']['subscription']['subject'],
					$emailContent['html'],
					$emailContent['plain'],
					$this->settings['mail']
				);
				$this->redirect('subscribe');

			} else {
				$this->flashMessageContainer->add('E-Mail Addresse bereits vorhanden.');
				$this->redirect('subscribe');
			}
		} else {
			$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');
			$mailService = $this->objectManager->get('Tx_Finewsletter_Service_MailService');

			$newRecipient->setActive(FALSE);

			$newRecipient->setToken($securityService->generateToken());
			$this->recipientRepository->add($newRecipient);

			$persistenceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
			$persistenceManager->persistAll();

			$emailContent = $mailService->generateEmailContent(array(
				'html'  => $this->settings['mail']['subscription']['templates']['html'],
				'plain' => $this->settings['mail']['subscription']['templates']['plain']
			), array(
				'verifyLink' => $securityService->generateVerifyLink($newRecipient, $this->uriBuilder) 
			), TRUE, TRUE);

			$mailService->sendMail(
				$this->objectManager->get('t3lib_mail_Message'),
				$email,
				$this->settings['mail']['subscription']['subject'],
				$emailContent['html'],
				$emailContent['plain'],
				$this->settings['mail']
			);

			$this->redirect('subscribed');
		}
	}

	/**
	 * action subscribed
	 * Used for addtional view.
	 *
	 * @return void
	 */
	public function subscribedAction() {
	}

	/**
	 * action verify
	 *
	 * @param Tx_Finewsletter_Domain_Model_Recipient $recipient
	 * @param string hash
	 * @return void
	 */
	public function verifyAction($recipient, $hash) {
		$verified = FALSE;
		$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');
		if($securityService->isSecurityHashValid($recipient, $hash) === TRUE) {
			$recipient->setActive(TRUE);
			$this->recipientRepository->update($recipient);
			$verified = TRUE;
		}
		$this->view->assign('verified', $verified);
		$this->view->assign('verifiedEmail', $recipient->getEmail());
	}

	/**
	 * unsubscripe action
	 *
	 * There should be two ways for unsubscribe. 
	 * Immediately by link in newsletter.
	 * With double opt-out via website.
	 *
	 * This carries about double opt-out via website.
	 *
	 * @return void
	 */
	public function unsubscribeAction() {
	}

	/**
	 * remove action
	 *
	 * @param Tx_Finewsletter_Domain_Model_Recipient $recipient
	 * @param string $auth
	 * @return void
	 */
	public function removeAction(Tx_Finewsletter_Domain_Model_Recipient $recipient, $auth = NULL) {
		// Prevent flashMessage flood
		$this->flashMessageContainer->flush();
		$email = $recipient->getEmail();
		if($auth === NULL) {
			$userValidator = $this->objectManager->get('Tx_Finewsletter_Validator_RecipientValidator');

			if($userValidator->isEmailValid($email) === FALSE) {
				$this->flashMessageContainer->add('Keine gültige E-Mail Addresse.');
				$this->redirect('unsubscribe');
			} elseif($userValidator->doesEmailExist($email) === FALSE) {
				$this->flashMessageContainer->add('E-Mail Addresse nicht vorhanden.');
				$this->redirect('unsubscribe');
			} else {
				$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');
				$mailService = $this->objectManager->get('Tx_Finewsletter_Service_MailService');
				$recipient = $this->recipientRepository->findOneByEmail($email);

				$emailContent = $mailService->generateEmailContent(array(
					'html'  => $this->settings['mail']['unsubscribe']['templates']['html'],
					'plain' => $this->settings['mail']['unsubscribe']['templates']['plain']
				), array(
					'unsubscribeLink' => $securityService->generateUnsubscribeLink($recipient, $this->uriBuilder) 
				), TRUE, TRUE);

				$mailService->sendMail(
					$this->objectManager->get('t3lib_mail_Message'),
					$email,
					$this->settings['mail']['unsubscribe']['subject'],
					$emailContent['html'],
					$emailContent['plain'],
					$this->settings['mail']
				);
				$this->flashMessageContainer->add('Bestätigungsemail wurde versendet.');
				$this->redirect('unsubscribe');
			}
		} else {
			$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');
			// Immediate unsubscribe
			$recipient = $this->recipientRepository->findOneByEmail($email);
			if($securityService->isUnsubscribeLinkValid($recipient, $auth) === TRUE) {
				$recipient->setActive(FALSE);
				$this->recipientRepository->update($recipient);
				$this->redirect('unsubscribed');
			} else {
				$this->flashMessageContainer->add('Ein Fehler ist aufgetreten. Bitte versuchen sie es erneut.');
				$this->redirect('unsubscribed');
			}
		}

	}

	/**
	 * unsubscriped action
	 *
	 * @return void
	 */
	public function unsubscribedAction() {
	}
}
?>