<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Registration',
	'Fi Newsletter'
);

$pluginSignature = str_replace('_','',$_EXTKEY) . '_' . 'registration';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/FlexFormNewsletter.xml');

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Simple Newsletter Registration');

			t3lib_extMgm::addLLrefForTCAdescr('tx_finewsletter_domain_model_recipient', 'EXT:finewsletter/Resources/Private/Language/locallang_csh_tx_finewsletter_domain_model_recipient.xml');
			t3lib_extMgm::allowTableOnStandardPages('tx_finewsletter_domain_model_recipient');
			$TCA['tx_finewsletter_domain_model_recipient'] = array(
				'ctrl' => array(
					'title'	=> 'LLL:EXT:finewsletter/Resources/Private/Language/locallang_db.xml:tx_finewsletter_domain_model_recipient',
					'label' => 'email',
					'tstamp' => 'tstamp',
					'crdate' => 'crdate',
					'cruser_id' => 'cruser_id',
					'dividers2tabs' => TRUE,
					'versioningWS' => 2,
					'versioning_followPages' => TRUE,
					'origUid' => 't3_origuid',
					'languageField' => 'sys_language_uid',
					'transOrigPointerField' => 'l10n_parent',
					'transOrigDiffSourceField' => 'l10n_diffsource',
					'delete' => 'deleted',
					'enablecolumns' => array(
						'disabled' => 'hidden',
						'starttime' => 'starttime',
						'endtime' => 'endtime',
					),
					'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Recipient.php',
					'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_finewsletter_domain_model_recipient.gif'
				),
			);

?>