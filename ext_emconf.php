<?php

########################################################################
# Extension Manager/Repository config file for ext "hype_optimum".
#
# Auto generated 27-07-2011 00:05
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Hype Optimum',
	'description' => 'Optimizes the output of CSS, JS and HTML files and more.',
	'category' => 'fe',
	'author' => 'Thomas "Thasmo" Deinhamer',
	'author_email' => 'thasmo@gmail.com',
	'shy' => 0,
	'version' => '1.6.7',
	'dependencies' => '',
	'conflicts' => 'speedy,sourceopt,mc_css_js_compressor',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => 'typo3temp/hype/optimum/',
	'modify_tables' => '',
	'clearCacheOnload' => 1,
	'lockType' => '',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.5.0-4.5.99',
		),
		'conflicts' => array(
			'speedy' => '',
			'sourceopt' => '',
			'mc_css_js_compressor' => '',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:67:{s:12:"ext_icon.gif";s:4:"9a50";s:17:"ext_localconf.php";s:4:"9659";s:14:"ext_tables.php";s:4:"dcd0";s:14:"ext_tables.sql";s:4:"ee47";s:10:"readme.txt";s:4:"a455";s:50:"Classes/Hook/class.tx_hypeoptimum_pagerenderer.php";s:4:"865d";s:45:"Classes/Hook/class.tx_hypeoptimum_tcemain.php";s:4:"3d55";s:45:"Classes/Utility/Cache/Backend/HybridCache.php";s:4:"d2ab";s:46:"Classes/Utility/Cache/Frontend/StringCache.php";s:4:"c81a";s:53:"Classes/Utility/Concatenator/AbstractConcatenator.php";s:4:"cf06";s:54:"Classes/Utility/Concatenator/ConcatenatorInterface.php";s:4:"ea05";s:51:"Classes/Utility/Concatenator/ScriptConcatenator.php";s:4:"fe1f";s:50:"Classes/Utility/Concatenator/StyleConcatenator.php";s:4:"a344";s:47:"Classes/Utility/Optimizer/AbstractOptimizer.php";s:4:"5def";s:48:"Classes/Utility/Optimizer/OptimizerInterface.php";s:4:"90e5";s:45:"Classes/Utility/Optimizer/ScriptOptimizer.php";s:4:"e434";s:44:"Classes/Utility/Optimizer/StyleOptimizer.php";s:4:"9d54";s:51:"Classes/Utility/Optimizer/Filter/AbstractFilter.php";s:4:"2d42";s:52:"Classes/Utility/Optimizer/Filter/FilterInterface.php";s:4:"34f2";s:68:"Classes/Utility/Optimizer/Filter/ScriptFilter/MinifyScriptFilter.php";s:4:"395d";s:63:"Classes/Utility/Optimizer/Filter/StyleFilter/CdnStyleFilter.php";s:4:"6997";s:65:"Classes/Utility/Optimizer/Filter/StyleFilter/CleanStyleFilter.php";s:4:"8f2d";s:65:"Classes/Utility/Optimizer/Filter/StyleFilter/EmbedStyleFilter.php";s:4:"206c";s:66:"Classes/Utility/Optimizer/Filter/StyleFilter/ImportStyleFilter.php";s:4:"91a2";s:66:"Classes/Utility/Optimizer/Filter/StyleFilter/MinifyStyleFilter.php";s:4:"a304";s:46:"Classes/Xclass/class.ux_t3lib_pagerenderer.php";s:4:"bb9f";s:38:"Configuration/TypoScript/constants.txt";s:4:"480e";s:34:"Configuration/TypoScript/setup.txt";s:4:"25c9";s:37:"Resources/Private/Code/min/README.txt";s:4:"27e8";s:37:"Resources/Private/Code/min/config.php";s:4:"9bb7";s:43:"Resources/Private/Code/min/groupsConfig.php";s:4:"54db";s:36:"Resources/Private/Code/min/index.php";s:4:"c1d9";s:36:"Resources/Private/Code/min/utils.php";s:4:"bd62";s:44:"Resources/Private/Code/min/builder/_index.js";s:4:"c7a6";s:40:"Resources/Private/Code/min/builder/bm.js";s:4:"b68a";s:44:"Resources/Private/Code/min/builder/index.php";s:4:"f492";s:46:"Resources/Private/Code/min/builder/ocCheck.php";s:4:"4152";s:49:"Resources/Private/Code/min/builder/rewriteTest.js";s:4:"c4ca";s:42:"Resources/Private/Code/min/lib/FirePHP.php";s:4:"f619";s:40:"Resources/Private/Code/min/lib/JSMin.php";s:4:"5716";s:44:"Resources/Private/Code/min/lib/JSMinPlus.php";s:4:"9d98";s:41:"Resources/Private/Code/min/lib/Minify.php";s:4:"da3f";s:54:"Resources/Private/Code/min/lib/HTTP/ConditionalGet.php";s:4:"f976";s:47:"Resources/Private/Code/min/lib/HTTP/Encoder.php";s:4:"4e67";s:47:"Resources/Private/Code/min/lib/Minify/Build.php";s:4:"6e32";s:45:"Resources/Private/Code/min/lib/Minify/CSS.php";s:4:"61cc";s:58:"Resources/Private/Code/min/lib/Minify/CommentPreserver.php";s:4:"86ba";s:46:"Resources/Private/Code/min/lib/Minify/HTML.php";s:4:"e774";s:57:"Resources/Private/Code/min/lib/Minify/ImportProcessor.php";s:4:"5ce5";s:47:"Resources/Private/Code/min/lib/Minify/Lines.php";s:4:"d642";s:48:"Resources/Private/Code/min/lib/Minify/Logger.php";s:4:"b284";s:48:"Resources/Private/Code/min/lib/Minify/Packer.php";s:4:"25e6";s:48:"Resources/Private/Code/min/lib/Minify/Source.php";s:4:"f705";s:55:"Resources/Private/Code/min/lib/Minify/YUICompressor.php";s:4:"1384";s:56:"Resources/Private/Code/min/lib/Minify/CSS/Compressor.php";s:4:"d04c";s:57:"Resources/Private/Code/min/lib/Minify/CSS/UriRewriter.php";s:4:"1625";s:51:"Resources/Private/Code/min/lib/Minify/Cache/APC.php";s:4:"2766";s:52:"Resources/Private/Code/min/lib/Minify/Cache/File.php";s:4:"1f06";s:56:"Resources/Private/Code/min/lib/Minify/Cache/Memcache.php";s:4:"fa20";s:57:"Resources/Private/Code/min/lib/Minify/Controller/Base.php";s:4:"7661";s:58:"Resources/Private/Code/min/lib/Minify/Controller/Files.php";s:4:"18a4";s:59:"Resources/Private/Code/min/lib/Minify/Controller/Groups.php";s:4:"12c1";s:59:"Resources/Private/Code/min/lib/Minify/Controller/MinApp.php";s:4:"88a6";s:57:"Resources/Private/Code/min/lib/Minify/Controller/Page.php";s:4:"02bc";s:61:"Resources/Private/Code/min/lib/Minify/Controller/Version1.php";s:4:"4369";s:44:"Resources/Private/Code/min/lib/Solar/Dir.php";s:4:"6c88";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"fb98";}',
	'suggests' => array(
	),
);

?>