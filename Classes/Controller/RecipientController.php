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
			$newRecipient = $this->objectManager->get('Tx_Finewsletter_Domain_Model_Recipient');
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

		$email = $newRecipient->getEmail();

		if($userValidator->isEmailValid($email) === FALSE) {
			$this->throwMessage($this->settings['messages']['subscribe']['invalidEmail']);
			$this->redirect('subscribe');
		} elseif($userValidator->doesEmailExist($email) === TRUE) {
			// Check if user is already subscribed (active)
			$newRecipient = $this->recipientRepository->findOneByEmail($newRecipient->getEmail());
			if ($newRecipient->isActive() === FALSE) {
				$this->throwMessage($this->settings['messages']['subscribe']['emailExistsNotActive']);

				$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');
				$mailService = $this->objectManager->get('Tx_Finewsletter_Service_MailService');
				$emailContent = $mailService->generateEmailContent(array(
					'html'  => $this->settings['mail']['subscribe']['templates']['html'],
					'plain' => $this->settings['mail']['subscribe']['templates']['plain']
				), array(
					'verifyLink' => $securityService->generateVerifyLink($newRecipient, $this->uriBuilder) 
				), TRUE, TRUE);

				$mailService->sendMail(
					$this->objectManager->get('t3lib_mail_Message'),
					$email,
					$this->settings['mail']['subscribe']['subject'],
					$emailContent['html'],
					$emailContent['plain'],
					$this->settings['mail']
				);
				$this->redirect('subscribe');

			} else {
				$this->throwMessage($this->settings['messages']['subscribe']['emailExists']);
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
				'html'  => $this->settings['mail']['subscribe']['templates']['html'],
				'plain' => $this->settings['mail']['subscribe']['templates']['plain']
			), array(
				'verifyLink' => $securityService->generateVerifyLink($newRecipient, $this->uriBuilder) 
			), TRUE, TRUE);

			$mailService->sendMail(
				$this->objectManager->get('t3lib_mail_Message'),
				$email,
				$this->settings['mail']['subscribe']['subject'],
				$emailContent['html'],
				$emailContent['plain'],
				$this->settings['mail']
			);

			$this->redirectHandler($this->settings['redirect']['subscribe'], 'subscribed');
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
	 * unsubscribe action
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

			if($userValidator->isEmailValid($email) === FALSE || $email === '') {
				$this->throwMessage($this->settings['messages']['unsubscribe']['invalidEmail']);
				$this->redirect('unsubscribe');
			} elseif($userValidator->doesEmailExist($email) === FALSE) {
				$this->throwMessage($this->settings['messages']['unsubscribe']['unknownEmail']);
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
				$this->throwMessage($this->settings['messages']['unsubscribe']['confirmationSent']);
				$this->redirect('unsubscribe');
			}
		} else {
			$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');
			// Immediate unsubscribe
			$recipient = $this->recipientRepository->findOneByEmail($email);
			if($securityService->isUnsubscribeLinkValid($recipient, $auth) === TRUE) {
				$recipient->setActive(FALSE);
				$this->recipientRepository->update($recipient);
				$this->redirectHandler($this->settings['redirect']['unsubscribe'], 'unsubscribed');
			} else {
				$this->throwMessage($this->settings['messages']['unsubscribe']['invalidConfirmationLink']);
				$this->redirect('unsubscribe');
			}
		}

	}

	/**
	 * unsubscribed action
	 *
	 * @return void
	 */
	public function unsubscribedAction() {
	}

	/**
	 * Redirect handler
	 * Redirects either to page or to action, depending on configuration.
	 *
	 * @param string $pageUid
	 * @param string $action
	 * @return void
	 */
	public function redirectHandler($pageUid, $action) {
		if($pageUid === NULL) {
			$this->redirect($action);
		}else{
			$this->redirect(NULL, NULL, NULL, NULL, $pageUid);
		}
	}

	/**
	 * Throw flashMessage
	 *
	 * @param string $message
	 * @return string
	 */
	public function throwMessage($message) {
		$this->flashMessageContainer->flush();
		$this->flashMessageContainer->add($message);
	}

	/**
	 * Backendmodule export function
	 *
	 * @return void
	 */
	public function exportAction() {
		$recipients = $this->recipientRepository->findByActive(TRUE);
		$this->view->assign('count', count($recipients));
		$this->view->assign('recipients', $recipients);
	}
}
?>