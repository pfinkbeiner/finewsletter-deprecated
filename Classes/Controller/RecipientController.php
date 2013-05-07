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
		$this->recipientRepository->add($newRecipient);
		$this->flashMessageContainer->add('Your new Recipient was created.');
		$this->redirect('list');
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

}
?>