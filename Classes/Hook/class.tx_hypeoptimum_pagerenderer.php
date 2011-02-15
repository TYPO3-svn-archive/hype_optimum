<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Thomas "Thasmo" Deinhamer <thasmo@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

# setup Minify
set_include_path(t3lib_extMgm::extPath('hype_optimum', 'Resources/Private/Code/min/lib') . PATH_SEPARATOR . get_include_path());
require_once(t3lib_extMgm::extPath('hype_optimum', 'Resources/Private/Code/min/lib/Minify/CSS/Compressor.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Resources/Private/Code/min/lib/Minify/CSS/UriRewriter.php'));

/**
 *
 */
class tx_hypeoptimum_pagerenderer {

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var Tx_HypeOptimum_Utility_Optimizer_StyleOptimizer
	 */
	protected $styleOptimizer;

	/**
	 * @var Tx_HypeOptimum_Utility_Optimizer_ScriptOptimizer
	 */
	protected $scriptOptimizer;

	/**
	 *
	 */
	public function __construct() {
		$this->initialize();
		$this->loadOptimizers();
	}

	/**
	 * Initializes settings, class autoloader etc.
	 * @return void
	 */
	protected function initialize() {

		# retrieve settings
		$this->settings = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hypeoptimum.'];

		# load the class autoloader
		spl_autoload_register(array(t3lib_div::makeInstance('Tx_Extbase_Utility_ClassLoader'), 'loadClass'));

		# be sure to get realtime file information
		clearstatcache();
	}

	/**
	 * Loads the optimizers and their configured filters
	 * @return void
	 */
	protected function loadOptimizers() {
		# instantiate style optimizer
		$this->styleOptimizer = t3lib_div::makeInstance('Tx_HypeOptimum_Utility_Optimizer_StyleOptimizer');
		$this->styleOptimizer->setBasePath(realpath(PATH_site . 'typo3conf/ext/hype_project/Resources/Public/Media/style'));
		$this->styleOptimizer->addFilter(t3lib_div::makeInstance('Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_ImportStyleFilter'));
		$this->styleOptimizer->addFilter(t3lib_div::makeInstance('Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_CleanStyleFilter'));
		$this->styleOptimizer->addFilter(t3lib_div::makeInstance('Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_MinifyStyleFilter'));
		//$this->styleOptimizer->addFilter(t3lib_div::makeInstance('Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_EmbedStyleFilter'));

		# instantiate script optimizer
		$this->scriptOptimizer = t3lib_div::makeInstance('Tx_HypeOptimum_Utility_Optimizer_ScriptOptimizer');
		$this->scriptOptimizer->addFilter(t3lib_div::makeInstance('Tx_HypeOptimum_Utility_Optimizer_Filter_ScriptFilter_MinifyScriptFilter'));
	}

	/**
	 *
	 */
	public function minifyScripts(&$groups, $renderer) {

		# skip if disabled
		if($this->settings['disable'] || $this->settings['script.']['disableMinification']) {
			return;
		}

		# compress inlined scripts
		// @todo implement a cache for inlined scripts
		foreach($groups['jsInline'] as $identifier => $options) {
			$groups['jsInline'][$identifier]['code'] = $this->scriptOptimizer->optimize($options['code']);
		}

		# compress libraries
		foreach($groups['jsLibs'] as $identifier => $options) {

			# skip if configured
			if(!$options['compress']) {
				continue;
			}

			# external files
			if(filter_var($options['file'], FILTER_VALIDATE_URL) !== FALSE) {
				$groups['jsLibs'][$identifier]['external'] = 1;
				continue;
			}

			# set current path
			$currentPath = realpath(PATH_site . $options['file']);
			$newPath = $this->scriptOptimizer->optimizeFile($currentPath);

			# add new file
			if(file_exists($newPath)) {
				$groups['jsLibs'][$identifier]['file'] = $newPath;
			}
		}

		# compress files
		$newGroups = array();
		foreach($groups['jsFiles'] as $file => $options) {

			# skip if configured
			if(!$options['compress']) {
				continue;
			}

			# external files
			if(filter_var($file, FILTER_VALIDATE_URL) !== FALSE) {
				$newGroups[$file] = $options;
				$newGroups[$file]['external'] = 1;
				continue;
			}

			# set current path
			$currentPath = realpath(PATH_site . $file);
			$newPath = $this->styleOptimizer->optimizeFile($currentPath);

			# add new file
			if(file_exists($newPath)) {
				$newGroups[$newPath] = $options;
			}
		}

		# replace all files
		$groups['jsFiles'] = $newGroups;
	}

	/**
	 *
	 */
	public function minifyStyles(&$groups, $renderer) {

		# skip if disabled
		if($this->settings['disable'] || $this->settings['style.']['disableMinification']) {
			return;
		}

		# compress inlined styles
		// @todo implement a cache for inlined scripts
		foreach($groups['cssInline'] as $identifier => $options) {

			# minify file contents
			$groups['cssInline'][$identifier]['code'] = $this->styleOptimizer->optimize($options['code']);
		}

		# compress files
		$newGroups = array();
		foreach($groups['cssFiles'] as $file => $options) {

			# skip if configured
			if(!$options['compress']) {
				continue;
			}

			# external files
			if(filter_var($file, FILTER_VALIDATE_URL) !== FALSE) {
				$newGroups[$file] = $options;
				$newGroups[$file]['external'] = 1;
				continue;
			}

			# set current path
			$currentPath = realpath(PATH_site . $file);
			$newPath = $this->styleOptimizer->optimizeFile($currentPath);

			# add new file
			if(file_exists($newPath)) {
				$newGroups[$newPath] = $options;
			}
		}

		# replace all files
		$groups['cssFiles'] = $newGroups;
	}

	/**
	 *
	 */
	public function concatenateFiles(&$groups, $renderer) {

		# concatenate scripts
		if(!$this->settings['disable'] && !$this->settings['script.']['disableConcatenation']) {
			$this->concatenateScripts(&$groups, $renderer);
		}

		# concatenate styles
		if(!$this->settings['disable'] && !$this->settings['style.']['disableConcatenation']) {
			$this->concatenateStyles(&$groups, $renderer);
		}
	}

	/**
	 *
	 */
	protected function concatenateScripts(&$groups, $renderer) {

		# get neccessary script files
		$files = array_merge($groups['jsLibs'], $groups['jsFiles']);

		# prepare cache
		$caches = array();
		$newGroups = array();
		foreach($files as $identifier => $options) {
			$path = $options['file'] ? $options['file'] : $identifier;

			# skip if external
			if(filter_var($path, FILTER_VALIDATE_URL) !== FALSE) {
				$newGroups[$path] = $options;
				$newGroups[$path]['external'] = 1;
				continue;

			# or if wrapped
			} else if($options['allWrap']) {
				$newGroups[$path] = $options;
			}

			$caches[$options['section']]['files'][$path] = $options;
			$caches[$options['section']]['hash'] .= $path;
		}

		# concatenate files
		foreach($caches as $section => $cache) {

			# determine new path
			$hash = md5($cache['hash']);
			$newPath = 'typo3temp/' . $this->settings['script.']['fileNamePrefix'] . $hash . '.js';

			# create compressed file
			if(!file_exists($newPath)) {

				# get and prepare file contents
				$data = NULL;
				foreach($cache['files'] as $file => $options) {
					$currentPath = realpath(PATH_site . $file);
					$data .= file_get_contents($currentPath);
				}

				# write new file
				if($data) {
					t3lib_div::writeFileToTypo3tempDir(PATH_site . $newPath, $data);
					unset($data);
				}
			}

			# add new file
			if(file_exists($newPath)) {
				$newGroups[$newPath] = array('section' => $section, 'type' => 'text/javascript');
			}
		}

		# set new files
		$groups['jsLibs'] = array();
		$groups['jsFiles'] = $newGroups;
	}

	/**
	 *
	 */
	protected function concatenateStyles(&$groups, $renderer) {

		# prepare cache
		$cache = array();
		$newGroup = array();
		foreach($groups['cssFiles'] as $file => $options) {

			# skip if external
			if(filter_var($file, FILTER_VALIDATE_URL) !== FALSE) {
				$newGroup[$file] = $options;
				$newGroup[$file]['external'] = 1;
				continue;

			# or if wrapped
			} else if($options['allWrap']) {
				$newGroup[$file] = $options;
			}

			$cache['files'][$file] = $options;
			$cache['hash'] .= $file . $options['media'];
		}

		# determine new path
		$hash = md5($cache['hash']);
		$newPath = 'typo3temp/' . $this->settings['style.']['fileNamePrefix'] . $hash . '.css';

		# create compressed file
		if(!file_exists($newPath)) {

			# get and prepare file contents
			// @todo beware of inlined media queries
			$data = NULL;
			foreach($cache['files'] as $file => $options) {
				$currentPath = realpath(PATH_site . $file);

				$contents = file_get_contents($currentPath);

				# sniff out font face declarations and stick 'em on top
				preg_match_all('~\@font-face.?\{.*\}~isU', $contents, $matches);

				if($matches[0]) {
					$data = implode(chr(10), $matches[0]) . $data;
				}

				$data .= '@media ' . $options['media'] . ' {' . file_get_contents($currentPath) . '}';
			}

			# write new file
			if($data) {
				t3lib_div::writeFileToTypo3tempDir(PATH_site . $newPath, $data);
				unset($data);
			}
		}

		# add new file
		if(file_exists($newPath)) {
			$groups['cssFiles'] = array($newPath => array('rel' => 'stylesheet', 'media' => 'all'));
		}
	}
}

?>