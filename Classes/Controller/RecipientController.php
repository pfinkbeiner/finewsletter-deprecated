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
		$this->view->assign('newRecipient', $newRecipient);
		if($this->settings['fields']['language']['values'] !== NULL) {
			$languages = explode('|', str_replace(' | ','|', $this->settings['fields']['language']['values']));
			$this->view->assign('languages', array_combine($languages, $languages));
		}
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
			# Validate other fields if necessary.
			# Validate for name
			if((int) $this->settings['fields']['name']['required'] === 1){
				if($userValidator->isFieldEmpty($newRecipient->getName()) === TRUE) {
					$this->throwMessage($this->settings['fields']['name']['error']);
					$this->redirect('subscribe');
				}
			}
			# Validate for firstName
			if((int) $this->settings['fields']['firstName']['required'] === 1){
				if($userValidator->isFieldEmpty($newRecipient->getFirstName()) === TRUE) {
					$this->throwMessage($this->settings['fields']['firstName']['error']);
					$this->redirect('subscribe');
				}
			}
			# Validate for lastName
			if((int) $this->settings['fields']['lastName']['required'] === 1){
				if($userValidator->isFieldEmpty($newRecipient->getLastName()) === TRUE) {
					$this->throwMessage($this->settings['fields']['lastName']['error']);
					$this->redirect('subscribe');
				}
			}

			$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');

			# Ensure recipient is not active by default
			$newRecipient->setActive(FALSE);

			# Generate a token for user.
			$newRecipient->setToken($securityService->generateToken());
			$this->recipientRepository->add($newRecipient);

			$persistenceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
			$persistenceManager->persistAll();

			# Send confirmation mail
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
	public function verifyAction($recipient = NULL, $hash) {
		if($recipient === NULL) {
			$redirectHandler($this->settings['redirect']['verifyFailed'], 'verifyFailed');
		}
		$verified = FALSE;
		$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');
		if($securityService->isSecurityHashValid($recipient, $hash) === TRUE) {
			$recipient->setActive(TRUE);
			$this->recipientRepository->update($recipient);
			$verified = TRUE;
			$this->redirectHandler($this->settings['redirect']['afterSubscribe'], 'verified');
		}
		$this->view->assign('verified', $verified);
		$this->view->assign('verifiedEmail', $recipient->getEmail());
	}

	/**
	 * action verfied
	 * after successful confirmation
	 *
	 * @return void
	 */
	public function verifiedAction() {
	}

	/**
	 * action verifyFailed
	 * user doens't exists
	 *
	 * @return void
	 */
	public function verifyFailedAction() {
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
				// Double optOut?
				if((int) $this->settings['global']['double-opt-out'] === 1) {
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
				} else {
					// Immediate unsubscribe without double opt-out
					$recipient = $this->recipientRepository->findOneByEmail($email);
					$recipient->setActive(FALSE);
					$this->recipientRepository->remove($recipient);
					$this->redirectHandler($this->settings['redirect']['afterUnsubscribe'], 'unsubscribed');
				}
			}
		} else {
			// Immediate unsubscribe by link
			$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');
			$recipient = $this->recipientRepository->findOneByEmail($email);
			if($securityService->isUnsubscribeLinkValid($recipient, $auth) === TRUE) {
				$this->recipientRepository->remove($recipient);
				$this->redirectHandler($this->settings['redirect']['afterUnsubscribe'], 'unsubscribed');
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
			$this->redirect(NULL, NULL, NULL, NULL, (int) $pageUid);
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