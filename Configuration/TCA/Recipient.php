<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_finewsletter_domain_model_recipient'] = array(
	'ctrl' => $TCA['tx_finewsletter_domain_model_recipient']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, email, name, first_name, last_name, language, active, token',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, email, name, first_name, last_name, language, active, token,--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_finewsletter_domain_model_recipient',
				'foreign_table_where' => 'AND tx_finewsletter_domain_model_recipient.pid=###CURRENT_PID### AND tx_finewsletter_domain_model_recipient.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'email' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:finewsletter/Resources/Private/Language/locallang_db.xml:tx_finewsletter_domain_model_recipient.email',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:finewsletter/Resources/Private/Language/locallang_db.xml:tx_finewsletter_domain_model_recipient.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'first_name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:finewsletter/Resources/Private/Language/locallang_db.xml:tx_finewsletter_domain_model_recipient.firstName',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'last_name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:finewsletter/Resources/Private/Language/locallang_db.xml:tx_finewsletter_domain_model_recipient.lastName',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'language' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:finewsletter/Resources/Private/Language/locallang_db.xml:tx_finewsletter_domain_model_recipient.language',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'active' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:finewsletter/Resources/Private/Language/locallang_db.xml:tx_finewsletter_domain_model_recipient.active',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'token' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:finewsletter/Resources/Private/Language/locallang_db.xml:tx_finewsletter_domain_model_recipient.token',
			'config' => array(
				'type' => 'input',
				'default' => 0
			),
		),
	),
);

?>