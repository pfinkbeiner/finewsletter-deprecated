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
 * ConfigurationService.
 * This service manages everything related to configuration with the extension itself.
 *
 * @package Finewsletters
 */ 
class Tx_Finewsletter_Service_ConfigurationService {

	/**
	 * Builds array for language config.
	 * Get something like en:English
	 * returns an array like array(
	 *		'en' => 'English'
	 * );
	 *
	 * @param string $languages.
	 * @return array
	 */
	public function buildLanguageArray($languages) {
		$data = explode(" | ", $languages);
		$resultArray = array();
		foreach($data as $row) {
			$result = explode(":", $row);
			$resultArray[$result[0]] = $result[1];
		}
		return $resultArray;
	}

}
?>