<?php

########################################################################
# Extension Manager/Repository config file for ext "hype_optimum".
#
# Auto generated 30-11-2009 20:28
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
	'dependencies' => 0,
	'conflicts' => 'speedy,sourceopt,mc_css_js_compressor',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => 'typo3temp/hype/optimum/',
	'modify_tables' => '',
	'clearCacheOnload' => TRUE,
	'lockType' => '',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.3.dev-4.4.99',
			'0' => 'extbase',
		),
		'conflicts' => array(
			'speedy' => '',
			'sourceopt' => '',
			'mc_css_js_compressor' => '',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:103:{s:12:"ext_icon.gif";s:4:"525b";s:17:"ext_localconf.php";s:4:"1efe";s:14:"ext_tables.php";s:4:"dcd0";s:47:"Classes/Hooks/class.user_t3lib_pagerenderer.php";s:4:"d7b4";s:38:"Configuration/TypoScript/constants.txt";s:4:"9763";s:34:"Configuration/TypoScript/setup.txt";s:4:"29f6";s:37:"Resources/Private/Code/min/README.txt";s:4:"27e8";s:37:"Resources/Private/Code/min/config.php";s:4:"9bb7";s:43:"Resources/Private/Code/min/groupsConfig.php";s:4:"54db";s:36:"Resources/Private/Code/min/index.php";s:4:"c1d9";s:36:"Resources/Private/Code/min/utils.php";s:4:"bd62";s:44:"Resources/Private/Code/min/builder/_index.js";s:4:"c7a6";s:40:"Resources/Private/Code/min/builder/bm.js";s:4:"b68a";s:44:"Resources/Private/Code/min/builder/index.php";s:4:"f492";s:46:"Resources/Private/Code/min/builder/ocCheck.php";s:4:"4152";s:49:"Resources/Private/Code/min/builder/rewriteTest.js";s:4:"c4ca";s:42:"Resources/Private/Code/min/lib/FirePHP.php";s:4:"f619";s:40:"Resources/Private/Code/min/lib/JSMin.php";s:4:"5716";s:44:"Resources/Private/Code/min/lib/JSMinPlus.php";s:4:"9d98";s:41:"Resources/Private/Code/min/lib/Minify.php";s:4:"da3f";s:54:"Resources/Private/Code/min/lib/HTTP/ConditionalGet.php";s:4:"f976";s:47:"Resources/Private/Code/min/lib/HTTP/Encoder.php";s:4:"4e67";s:47:"Resources/Private/Code/min/lib/Minify/Build.php";s:4:"6e32";s:45:"Resources/Private/Code/min/lib/Minify/CSS.php";s:4:"61cc";s:58:"Resources/Private/Code/min/lib/Minify/CommentPreserver.php";s:4:"86ba";s:46:"Resources/Private/Code/min/lib/Minify/HTML.php";s:4:"e774";s:57:"Resources/Private/Code/min/lib/Minify/ImportProcessor.php";s:4:"5ce5";s:47:"Resources/Private/Code/min/lib/Minify/Lines.php";s:4:"d642";s:48:"Resources/Private/Code/min/lib/Minify/Logger.php";s:4:"b284";s:48:"Resources/Private/Code/min/lib/Minify/Packer.php";s:4:"25e6";s:48:"Resources/Private/Code/min/lib/Minify/Source.php";s:4:"f705";s:55:"Resources/Private/Code/min/lib/Minify/YUICompressor.php";s:4:"1384";s:56:"Resources/Private/Code/min/lib/Minify/CSS/Compressor.php";s:4:"d04c";s:57:"Resources/Private/Code/min/lib/Minify/CSS/UriRewriter.php";s:4:"1690";s:51:"Resources/Private/Code/min/lib/Minify/Cache/APC.php";s:4:"2766";s:52:"Resources/Private/Code/min/lib/Minify/Cache/File.php";s:4:"1f06";s:56:"Resources/Private/Code/min/lib/Minify/Cache/Memcache.php";s:4:"fa20";s:57:"Resources/Private/Code/min/lib/Minify/Controller/Base.php";s:4:"7661";s:58:"Resources/Private/Code/min/lib/Minify/Controller/Files.php";s:4:"18a4";s:59:"Resources/Private/Code/min/lib/Minify/Controller/Groups.php";s:4:"12c1";s:59:"Resources/Private/Code/min/lib/Minify/Controller/MinApp.php";s:4:"88a6";s:57:"Resources/Private/Code/min/lib/Minify/Controller/Page.php";s:4:"02bc";s:61:"Resources/Private/Code/min/lib/Minify/Controller/Version1.php";s:4:"4369";s:44:"Resources/Private/Code/min/lib/Solar/Dir.php";s:4:"6c88";s:40:"Resources/Private/Code/speedy/config.php";s:4:"9fa4";s:39:"Resources/Private/Code/speedy/index.php";s:4:"8900";s:41:"Resources/Private/Code/speedy/install.php";s:4:"4e7a";s:44:"Resources/Private/Code/speedy/php_speedy.php";s:4:"e872";s:50:"Resources/Private/Code/speedy/controller/admin.php";s:4:"960e";s:55:"Resources/Private/Code/speedy/controller/compressor.php";s:4:"60df";s:48:"Resources/Private/Code/speedy/libs/css/forms.css";s:4:"2441";s:47:"Resources/Private/Code/speedy/libs/css/grid.css";s:4:"c392";s:47:"Resources/Private/Code/speedy/libs/css/grid.png";s:4:"893e";s:45:"Resources/Private/Code/speedy/libs/css/ie.css";s:4:"a321";s:48:"Resources/Private/Code/speedy/libs/css/print.css";s:4:"6132";s:48:"Resources/Private/Code/speedy/libs/css/reset.css";s:4:"3386";s:53:"Resources/Private/Code/speedy/libs/css/typography.css";s:4:"f80c";s:48:"Resources/Private/Code/speedy/libs/js/builder.js";s:4:"039d";s:49:"Resources/Private/Code/speedy/libs/js/controls.js";s:4:"fcf6";s:49:"Resources/Private/Code/speedy/libs/js/dragdrop.js";s:4:"e07e";s:48:"Resources/Private/Code/speedy/libs/js/effects.js";s:4:"c96b";s:50:"Resources/Private/Code/speedy/libs/js/prototype.js";s:4:"ca9b";s:47:"Resources/Private/Code/speedy/libs/js/slider.js";s:4:"9ed3";s:46:"Resources/Private/Code/speedy/libs/js/sound.js";s:4:"d654";s:48:"Resources/Private/Code/speedy/libs/php/jsmin.php";s:4:"fd0e";s:53:"Resources/Private/Code/speedy/libs/php/user_agent.php";s:4:"c82f";s:47:"Resources/Private/Code/speedy/libs/php/view.php";s:4:"b500";s:54:"Resources/Private/Code/speedy/view/admin_container.php";s:4:"aba8";s:44:"Resources/Private/Code/speedy/view/error.php";s:4:"ddbf";s:61:"Resources/Private/Code/speedy/view/install_enter_password.php";s:4:"0f47";s:59:"Resources/Private/Code/speedy/view/install_set_password.php";s:4:"537b";s:54:"Resources/Private/Code/speedy/view/install_stage_1.php";s:4:"f56e";s:54:"Resources/Private/Code/speedy/view/install_stage_2.php";s:4:"7a1d";s:54:"Resources/Private/Code/speedy/view/install_stage_3.php";s:4:"6b68";s:44:"Resources/Private/Code/speedy_old/config.php";s:4:"c11c";s:48:"Resources/Private/Code/speedy_old/php_speedy.php";s:4:"f4cf";s:54:"Resources/Private/Code/speedy_old/controller/admin.php";s:4:"0b5f";s:59:"Resources/Private/Code/speedy_old/controller/compressor.php";s:4:"6042";s:52:"Resources/Private/Code/speedy_old/libs/css/forms.css";s:4:"2441";s:51:"Resources/Private/Code/speedy_old/libs/css/grid.css";s:4:"cc3e";s:51:"Resources/Private/Code/speedy_old/libs/css/grid.png";s:4:"893e";s:49:"Resources/Private/Code/speedy_old/libs/css/ie.css";s:4:"a321";s:52:"Resources/Private/Code/speedy_old/libs/css/print.css";s:4:"6132";s:52:"Resources/Private/Code/speedy_old/libs/css/reset.css";s:4:"3386";s:57:"Resources/Private/Code/speedy_old/libs/css/typography.css";s:4:"f80c";s:52:"Resources/Private/Code/speedy_old/libs/js/builder.js";s:4:"039d";s:53:"Resources/Private/Code/speedy_old/libs/js/controls.js";s:4:"fcf6";s:53:"Resources/Private/Code/speedy_old/libs/js/dragdrop.js";s:4:"e07e";s:52:"Resources/Private/Code/speedy_old/libs/js/effects.js";s:4:"c96b";s:54:"Resources/Private/Code/speedy_old/libs/js/prototype.js";s:4:"ca9b";s:51:"Resources/Private/Code/speedy_old/libs/js/slider.js";s:4:"9ed3";s:50:"Resources/Private/Code/speedy_old/libs/js/sound.js";s:4:"d654";s:56:"Resources/Private/Code/speedy_old/libs/php/jsmin.old.php";s:4:"fd0e";s:52:"Resources/Private/Code/speedy_old/libs/php/jsmin.php";s:4:"ed40";s:51:"Resources/Private/Code/speedy_old/libs/php/view.php";s:4:"3c43";s:58:"Resources/Private/Code/speedy_old/view/admin_container.php";s:4:"aba8";s:48:"Resources/Private/Code/speedy_old/view/error.php";s:4:"ddbf";s:65:"Resources/Private/Code/speedy_old/view/install_enter_password.php";s:4:"0f47";s:63:"Resources/Private/Code/speedy_old/view/install_set_password.php";s:4:"537b";s:58:"Resources/Private/Code/speedy_old/view/install_stage_1.php";s:4:"f56e";s:58:"Resources/Private/Code/speedy_old/view/install_stage_2.php";s:4:"668d";s:58:"Resources/Private/Code/speedy_old/view/install_stage_3.php";s:4:"2915";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"fb98";}',
	'suggests' => array(
	),
);

?>