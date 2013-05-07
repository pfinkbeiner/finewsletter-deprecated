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
	public function newAction(Tx_Finewsletter_Domain_Model_Recipient $newRecipient = NULL) {
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
		$this->flashMessageContainer->flush();

		$email = $newRecipient->getEmail();

		if($userValidator->isEmailValid($email) === FALSE) {
			$this->flashMessageContainer->add('Keine gültige E-Mail Addresse.');
			$this->redirect('new');
		} elseif($userValidator->doesEmailExist($email) === TRUE) {
			// Check if user is already subscribed (active)
			if ($userValidator->isUserInactive === TRUE) {
				$this->flashMessageContainer->add('E-Mail Addresse bereits vorhanden. Erneute verifizierungsemail wurde verschickt.');
				$this->redirect('new');
			} else {
				$this->flashMessageContainer->add('E-Mail Addresse bereits vorhanden.');
				$this->redirect('new');
			}
		} else {
			$securityService = $this->objectManager->get('Tx_Finewsletter_Service_SecurityService');
			//$mailService = $this->objectManager->get('Tx_Finewsletter_Service_MailService');

			$newRecipient->setActive(FALSE);
			$newRecipient->setToken($securityService->generateToken());
			$this->recipientRepository->add($newRecipient);


//			$emailContent = $mailService->generateEmailContent(array(
//				'html'  => $this->settings['mail']['registration']['templates']['html'],
//				'plain' => $this->settings['mail']['registration']['templates']['plain']
//			), array(
//				'verifyLink' => $securityService->generateVerifyLink($email, $this->uriBuilder) 
//			), TRUE, TRUE);
//
//			$mailService->sendMail(
//				$this->objectManager->get('t3lib_mail_Message'),
//				$userWhoLostHisPassword->getEmail(),
//				$this->settings['mail']['userPasswordRecovery']['subject'],
//				$emailContent['html'],
//				$emailContent['plain'],
//				$this->settings['mail']
//			);
//
			$this->redirect('subscribed');
		}
	}

	/**
	 * action edit
	 *
	 * @param $recipient
	 * @return void
	 */
	public function editAction(Tx_Finewsletter_Domain_Model_Recipient $recipient) {
		$this->view->assign('recipient', $recipient);
	}

	/**
	 * action update
	 *
	 * @param $recipient
	 * @return void
	 */
	public function updateAction(Tx_Finewsletter_Domain_Model_Recipient $recipient) {
		$this->recipientRepository->update($recipient);
		$this->flashMessageContainer->add('Your Recipient was updated.');
		$this->redirect('list');
	}

	/**
	 * action subscribed
	 *
	 * @return void
	 */
	public function subscribedAction() {
	}

}
?>