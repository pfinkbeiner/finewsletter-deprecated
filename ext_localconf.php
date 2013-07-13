<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Registration',
	array(
		'Recipient' => 'subscribe, create, subscribed, verify, unsubscribe, unsubscribed, remove',
	),
	// non-cacheable actions
	array(
		'Recipient' => 'create, remove, verify',
	)
);

?>