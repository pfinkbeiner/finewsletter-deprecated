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
class Tx_Finewsletter_Service_SecurityService implements t3lib_Singleton {

	/**
	 * Generates a unique identifier (UUID) according to RFC 4122.
	 *
	 * @return string
	 */
	public function generateToken() {
		return sprintf( '%02x%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x3fff ) | 0x8000 );
	}

	/**
	 * Generates a security hash for verify link
	 *
	 * @param Tx_Finewsletter_Domain_Model_Recipient $recipient
	 * @return string
	 */
	public function generateSecurityHash($recipient) {
		$md5Email = md5($recipient->getEmail());
		$md5Token = md5($recipient->getToken());

		$hash = $md5Email . $md5Token;
		// Short URL
		return substr($hash, 16, 32);
	}

	/**
	 * Generate verify Link
	 *
	 * @param Tx_Finewsletter_Domain_Model_Recipient $newRecipient
	 * @param Tx_Extbase_MVC_Web_Routing_UriBuilder $uriBuilder
	 * @return string
	 */
	public function generateVerifyLink(Tx_Finewsletter_Domain_Model_Recipient $newRecipient, Tx_Extbase_MVC_Web_Routing_UriBuilder $uriBuilder = NULL) {
		$uriBuilder = ($uriBuilder === NULL) ? $this->uriBuilder : $uriBuilder;
		$uri = $uriBuilder
			->reset()
			->setCreateAbsoluteUri(TRUE)
			->setUseCacheHash(FALSE)
			->uriFor('activate', array( 'recipient' => $newRecipient, 'hash' => $this->generateSecurityHash($newRecipient)));
		return $uri;
	}
	
}
?>