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
class Tx_Finewsletter_Validator_RecipientValidator implements t3lib_Singleton {

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
	 * Does user already exist?
	 *
	 * @param string $email
	 * @return boolean
	 */
	public function doesEmailExist($email) {
		$status = TRUE;
		if(count($this->recipientRepository->findByEmail($email)) === 0) {
			$status = FALSE;
		}
		return $status;
	}

	/**
	 * valid email address?
	 *
	 * @param string $email
	 * @return boolean
	 */
	public function isEmailValid($email) {
		$status = FALSE;
		if(!empty($email) && preg_match(' / ^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)* @ (?: (?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[a-z]{2}|aero|asia|biz|cat|com|edu|coop|gov|info|int|invalid|jobs|localdomain|mil|mobi|museum|name|net|org|pro|tel|travel)| localhost| (?:(?:\d{1,2}|1\d{1,2}|2[0-5][0-5])\.){3}(?:(?:\d{1,2}|1\d{1,2}|2[0-5][0-5]))) \b /ix', $email)) {
			$status = TRUE;
		}
		return $status;
	}

	/**
	 * is field empty?
	 *
	 * @param string $value
	 * @return boolean
	 */
	public function isFieldEmpty($value) {
		$status = FALSE;
		if(trim($value) === '' || $value === NULL) {
			$status = !$status;
		}
		return $status;
	}
}
?>