<?php
/*                                                                        *
 * Copyright notice
 *
 * (c) 2013 Patrick Finkbeiner <finkbeiner.patrick@gmail.com>, finkbeiner.me
 * 
 * All rights reserved
 *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * Translate a key from locallang. Either the default locallang file or from given path.
 * default: "finewsletter/Resources/Private/Language/".
 */
class Tx_Finewsletter_ViewHelpers_TranslateViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Translate a given key or use the tag body as default.
	 *
	 * @param string $key The locallang key
	 * @param string $default if the given locallang key could not be found, this value is used. . If this argument is not set, child nodes will be used to render the default
	 * @param boolean $htmlEscape TRUE if the result should be htmlescaped. This won't have an effect for the default value
	 * @param array $arguments Arguments to be replaced in the resulting string
	 * @return string The translated key or tag body if key doesn't exist
	 */
	public function render($key, $default = NULL, $htmlEscape = TRUE, array $arguments = NULL) {
		$frameworkConfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		
		// If languageRootPath is set, use new locallang.xml location instead…
		if(isset($frameworkConfiguration['view']['languageRootPath']) && !empty($frameworkConfiguration['view']['languageRootPath'])) {
			$key = 'LLL:'.$frameworkConfiguration['view']['languageRootPath'].':'.$key;
		}
		$request = $this->controllerContext->getRequest();
		$extensionName = $request->getControllerExtensionName();
		$value = Tx_Extbase_Utility_Localization::translate($key, $extensionName, $arguments);
		if ($value === NULL) {
			$value = $default !== NULL ? $default : $this->renderChildren();
			if (is_array($arguments)) {
				$value = vsprintf($value, $arguments);
			}
		} elseif ($htmlEscape) {
			$value = htmlspecialchars($value);
		}
		return $value;
	}
}

?>