<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Registration',
	array(
		'Recipient' => 'new, create, edit, update',
		
	),
	// non-cacheable actions
	array(
		'Recipient' => 'create, update',
		
	)
);

?>