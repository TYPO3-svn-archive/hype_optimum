<?php

if(!defined('TYPO3_MODE'))
	die('Access denied.');



# add default setup & constants
t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Hype Optimum');

?>