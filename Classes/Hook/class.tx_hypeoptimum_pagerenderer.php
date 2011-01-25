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

require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/OptimizerInterface.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/AbstractOptimizer.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/ScriptOptimizer.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/StyleOptimizer.php'));

require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/Cache/CacheInterface.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/Cache/AbstractCache.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/Cache/FileCache.php'));

require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/Filter/FilterInterface.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/Filter/AbstractFilter.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/Filter/ScriptFilter/MinifyScriptFilter.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/Filter/StyleFilter/ImportStyleFilter.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/Filter/StyleFilter/CleanStyleFilter.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/Filter/StyleFilter/EmbedStyleFilter.php'));
require_once(t3lib_extMgm::extPath('hype_optimum', 'Classes/Utility/Optimizer/Filter/StyleFilter/MinifyStyleFilter.php'));

/**
*
* 1. Cleanup the header data and merge it into includeJS and includeCSS
* 2. Compress every single JS file ✓
* 3. Compress every single CSS file ✓
* 4. Merge all JS files by position (header|footer) ✓
* 5. Merge all CSS files by media definition
*
* TODO
* - Merge all CSS into 1 single file using @media rule: "@media screen { <css goes here> }" ✓
* - Recognize imported files inside CSS using @import rule: "@import url("screen.css") screen;" ✓
* - Get CSS and JS files from $headerData. (Important: headerData comes AFTER cssFiles and cssInline!)
* - Convert file-references (images/fonts) to data uris. ✓
* - Determine changes of referenced files in CSS files and recreate minified files. (1. url(), 2. @import)
* - Implement a procedure to import (@import) CSS files recursively including complete minification.
* - Introduce a ScriptOptimizer and a StyleOptimizer which holds global minification/compress functions.
* - Remove @charset definitions. ✓
* - Make everything configureable via TypoScript and Extension configuration. ✓
* - Find a solution for the CSS file generated by TYPO3 everytime, forcing us to recompress it everytime as well.
* - Allow external files to be concatenated.
*
* CLASSES
* - Tx_HypeOptimum_Utility_ScriptOptimizer
*   Optimizes a javascript file's content using processors.
*
* - Tx_HypeOptimum_Utility_Script_Processor_AbstractProcessor
*   Abstract processor class.
*
* - Tx_HypeOptimum_Utility_Script_Processor_MinifyProcessor
*   Removes unnecessary whitespace of all kind including comments.
*
* - Tx_HypeOptimum_Utility_StyleOptimizer
*   Optimizes a stylesheet's content using the following classes.
*
* - Tx_HypeOptimum_Utility_Style_Processor_AbstractProcessor
*   Abstract processor class.
*
* - Tx_HypeOptimum_Utility_Style_Processor_ImportProcessor
*   Recursively embeds imported files.
*
* - Tx_HypeOptimum_Utility_Style_Processor_NormalizeProcessor
*   Removes BOM, unifies EOL and performs encoding and charset magic.
*
* - Tx_HypeOptimum_Utility_Style_Processor_MinifyProcessor
*   Removes unnecessary whitespace of all kind including comments.
*
* - Tx_HypeOptimum_Utility_Style_Processor_EmbedProcessor
*   Embdes external resources like images and font files.
*
* CONSIDERATIONS
* - Also concatenating inline CSS?
* - ...
*/

/**
 *
 */
class tx_hypeoptimum_pagerenderer {

	/**
	 * @var array
	 */
	private $styleProcessors = array();

	/**
	 * @var array
	 */
	private $scriptProcessors = array();

	/**
	 *
	 */
	public function __construct() {

		# retrieve settings
		$this->settings = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hypeoptimum.'];

		# be sure to get realtime file information
		clearstatcache();

		$this->scriptOptimizer = new Tx_HypeOptimum_Utility_Optimizer_ScriptOptimizer;
		$this->scriptOptimizer->addFilter(new Tx_HypeOptimum_Utility_Optimizer_Filter_ScriptFilter_MinifyScriptFilter);

		$this->styleOptimizer = new Tx_HypeOptimum_Utility_Optimizer_StyleOptimizer;
		$this->styleOptimizer->setBasePath(realpath(PATH_site . 'typo3conf/ext/hype_project/Resources/Public/Media/style'));
		$this->styleOptimizer->addFilter(new Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_ImportStyleFilter);
		$this->styleOptimizer->addFilter(new Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_CleanStyleFilter);
		$this->styleOptimizer->addFilter(new Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_MinifyStyleFilter);
		$this->styleOptimizer->addFilter(new Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_EmbedStyleFilter);
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
		// @todo	Implement a cache for inlined scripts
		foreach($groups['jsInline'] as $identifier => $options) {
			$groups['jsInline'][$identifier]['code'] = $this->scriptOptimizer->optimize($options['code']);
		}

		# compress libraries
		foreach($groups['jsLibs'] as $identifier => $options) {

			# external files
			if(filter_var($options['file'], FILTER_VALIDATE_URL) !== FALSE) {
				$newGroups[$identifier] = $options;
				$newGroups[$identifier]['external'] = 1;
				continue;
			}

			# skip if configured
			if(!$options['compress']) {
				continue;
			}

			# set current path
			$currentPath = realpath(PATH_site . $options['file']);

			# determine new path
			$hash = md5($currentPath . filemtime($currentPath));
			$newPath = 'typo3temp/' . $this->settings['script.']['fileNamePrefix'] . $hash . '.js';

			# minify new file
			if(!file_exists($newPath)) {
				$data = $this->scriptOptimizer->optimize(file_get_contents($currentPath));

				if($data) {
					t3lib_div::writeFileToTypo3tempDir(PATH_site . $newPath, $data);
					unset($data);
				}
			}

			# replace file
			if(file_exists($newPath)) {
				$groups['jsLibs'][$identifier]['file'] = $newPath;
			}
		}

		# compress files
		$newGroups = array();
		foreach($groups['jsFiles'] as $file => $options) {

			# external files
			if(filter_var($file, FILTER_VALIDATE_URL) !== FALSE) {
				$newGroups[$file] = $options;
				$newGroups[$file]['external'] = 1;
				continue;
			}

			# skip if configured
			if(!$options['compress']) {
				continue;
			}

			# set current path
			$currentPath = realpath(PATH_site . $file);

			# determine new path
			$hash = md5($currentPath . filemtime($currentPath));
			$newPath = 'typo3temp/' . $this->settings['script.']['fileNamePrefix'] . $hash . '.js';

			# minify new file
			if(!file_exists($newPath)) {
				$data = $this->scriptOptimizer->optimize(file_get_contents($currentPath));

				if($data) {
					t3lib_div::writeFileToTypo3tempDir(PATH_site . $newPath, $data);
					unset($data);
				}
			}

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
		// @todo	Implement a cache for inlined scripts
		foreach($groups['cssInline'] as $identifier => $options) {

			# minify file contents
			$groups['cssInline'][$identifier]['code'] = $this->styleOptimizer->optimize($options['code']);
		}

		# compress files
		// @todo	Embed external styles (@import)
		$newGroups = array();
		foreach($groups['cssFiles'] as $file => $options) {

			# skip if configured
			if(!$options['compress']) {
				continue;
			}

			# external files
			if(filter_var($options['file'], FILTER_VALIDATE_URL) !== FALSE) {
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
			// @todo	Beware of inlined media queries and font-face declarations.
			$data = NULL;
			foreach($cache['files'] as $file => $options) {
				$currentPath = realpath(PATH_site . $file);
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

	/**
	 *
	 */
	public function embedStyles($match) {

		$path = realpath(PATH_site . $match[1]);

		if(file_exists($path)) {
			$data = file_get_contents($path);

			if($match[2]) {
				$data = '@media ' . $match[2] . ' {' . $data . '}';
			}
		} else {
			$data = $match[0];
		}

		return $data;
	}

	/**
	 *
	 */
	public function convertToBase64($match) {

		$path = realpath(PATH_site . $match[1]);

		if(file_exists($path) && filesize($path) < $this->settings['style.']['embedFileSizeLimit'] && !preg_match('~#~', $path)) {
			// $type = exif_imagetype($path);
			// $mime = image_type_to_mime_type($type);
			$mime = mime_content_type($path);

			$data = 'url(data:' . $mime . ';base64,' . base64_encode(file_get_contents($path)) . ')';
		} else {
			$data = $match[0];
		}

		return $data;
	}
}

?>