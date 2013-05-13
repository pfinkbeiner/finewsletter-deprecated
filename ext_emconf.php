<?php

########################################################################
# Extension Manager/Repository config file for ext: "finewsletter"
#
# Auto generated by Extension Builder 2013-05-07
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Simple Newsletter',
	'description' => 'A damn simple newsletter extension with a subscription (double opt-in) and unsubscribtion (single / double opt-out).',
	'category' => 'plugin',
	'author' => 'Patrick Finkbeiner',
	'author_email' => 'finkbeiner.patrick@gmail.com',
	'author_company' => 'finkbeiner.me',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'extbase' => '1.3',
			'fluid' => '1.3',
			'typo3' => '4.5',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>