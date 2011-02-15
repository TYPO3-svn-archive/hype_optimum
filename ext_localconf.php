<?php

if(!defined('TYPO3_MODE'))
	die('Access denied.');



# TYPO3 CONFIGURATION

# absRefPrefix directories
$GLOBALS['TYPO3_CONF_VARS']['FE']['additionalAbsRefPrefixDirectories'] .= ',typo3temp/';



# XCLASS

# Page renderer
$GLOBALS['TYPO3_CONF_VARS']['FE']['XCLASS']['t3lib/class.t3lib_pagerenderer.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Xclass/class.ux_t3lib_pagerenderer.php';



# HOOKS

# Concatenation
$GLOBALS['TYPO3_CONF_VARS']['FE']['concatenateHandler'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Hook/class.tx_hypeoptimum_pagerenderer.php:tx_hypeoptimum_pagerenderer->concatenateFiles';

# Scripts compression
$GLOBALS['TYPO3_CONF_VARS']['FE']['jsCompressHandler'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Hook/class.tx_hypeoptimum_pagerenderer.php:tx_hypeoptimum_pagerenderer->minifyScripts';

# Styles compression
$GLOBALS['TYPO3_CONF_VARS']['FE']['cssCompressHandler'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Hook/class.tx_hypeoptimum_pagerenderer.php:tx_hypeoptimum_pagerenderer->minifyStyles';

# Cache
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Hook/class.tx_hypeoptimum_tcemain.php:tx_hypeoptimum_tcemain->clearCache';



# CACHE

# Registration
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheBackends']['Tx_HypeOptimum_Utility_Cache_HybridCache'] = 'typo3conf/ext/hype_optimum/Classes/Utility/Cache/HybridCache.php:Tx_HypeOptimum_Utility_Cache_HybridCache';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheFrontends']['Tx_HypeOptimum_Utility_Cache_StringCache'] = 'typo3conf/ext/hype_optimum/Classes/Utility/Cache/StringCache.php:Tx_HypeOptimum_Utility_Cache_StringCache';

# Configuration
if(!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_hypeoptimum'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_hypeoptimum'] = array(
		'frontend' => 'Tx_HypeOptimum_Utility_Cache_StringCache',
		'backend' => 'Tx_HypeOptimum_Utility_Cache_HybridCache',
		'options' => array(
			'cacheTable' => 'tx_hypeoptimum_cache',
			'tagsTable' => 'tx_hypeoptimum_cache_tag',
			'cacheDirectory' => 'typo3temp/'
		),
	);
}

?>