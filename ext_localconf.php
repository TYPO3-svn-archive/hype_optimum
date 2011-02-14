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



# RTE

t3lib_extMgm::addPageTSConfig('
RTE {
	default {
		classesAnchor = external, external_window, internal, internal_window, file, email
		classesAnchor.default {
			page = internal
			url = external
			file = file
			mail = email
		}

		proc {
			allowedClasses (
				external, external_window, internal, internal_window, file, email,
				align-left, align-center, align-right, align-justify,
				csc-frame-frame1, csc-frame-frame2,
				component-items, action-items,
				component-items-ordered, action-items-ordered,
				important, name-of-person, detail,
				indent
			)
		}
	}

	classesAnchor {
		externalLink {
			class = external
			type = url
			titleText =
		}
		externalLinkInNewWindow {
			class = external external_window
			type = url
			titleText =
		}
		internalLink {
			class = internal
			type = page
			titleText =
		}
		internalLinkInNewWindow {
			class = internal internal_window
			type = page
			titleText =
		}
		download {
			class = file
			type = file
			titleText =
		}
		mail {
			class = email
			type = mail
			titleText =
		}
	}
}
');



# CACHE
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheBackends']['Tx_HypeOptimum_Utility_Optimizer_Cache_HybridCache'] = 'typo3conf/ext/hype_optimum/Classes/Utility/Optimizer/Cache/HybridCache.php:Tx_HypeOptimum_Utility_Optimizer_Cache_HybridCache';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheFrontends']['Tx_HypeOptimum_Utility_Optimizer_Cache_StringCache'] = 'typo3conf/ext/hype_optimum/Classes/Utility/Optimizer/Cache/StringCache.php:Tx_HypeOptimum_Utility_Optimizer_Cache_StringCache';

if(!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_hypeoptimum'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_hypeoptimum'] = array(
		'frontend' => 'Tx_HypeOptimum_Utility_Optimizer_Cache_StringCache',
		'backend' => 'Tx_HypeOptimum_Utility_Optimizer_Cache_HybridCache',
		'options' => array(
			'cacheTable' => 'tx_hypeoptimum_cache',
			'tagsTable' => 'tx_hypeoptimum_cache_tag',
			'cacheDirectory' => 'typo3temp/'
		),
	);
}

?>