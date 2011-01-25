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

/**
*
* 1. Cleanup the header data and merge it into includeJS and includeCSS
* 2. Compress every single JS file
* 3. Compress every single CSS file
* 4. Merge all JS files
* 5. Merge all CSS files BY MEDIA TYPE!!
*
* TODO:
* # Merge all CSS into 1 single file using @media rule: "@media screen { <css goes here> }"
*   Important thing here is to consider media defintions like 'screen,projection' and 'projection,screen'
* - Recognize imported files inside CSS using @import rule: "@import url("screen.css") screen;"
*   Again it's important to consider the media definitions in correct order (e.g. alphabetic order)
* - Get CSS and JS files from $headerData.
*   Important: headerData comes AFTER cssFiles and cssInline in the template!!
* # Concatenate 'jsFooterFiles'
* - Convert file-references (images) to data uris
* - Some more ...
*
* CONSIDERATIONS:
* - Also concatenating inline CSS?
* - ...
*/
class Tx_HypeOptimum_Utility_ParserUtility {
	public static function base64EncodeFile($path) {

	}
}

class tx_hypeoptimum_pagerenderer {
	static $converted = FALSE;

	public function __construct() {

		# be sure to get realtime file information
		clearstatcache();

		# retrieve settings
		$this->settings = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hypeoptimum.'];
	}

	public function compressJS(&$files, $renderer) {

		# skip if disabled
		if($this->settings['disable'] || !$this->settings['compress_js']) {
			return;
		}

		# convert headerdata to JS and CSS files
		//$this->convertHeaderData($renderer);

		# loop thru all file groups
		foreach($files as $group => $items) {

			switch($group) {

				# compress inline code
				case 'jsInline':
				case 'jsFooterInline':

					# skip inline code if configured
					if($items['_scriptCode']['compress'] || $this->settings['compress_js_force']) {
						$items['_scriptCode']['code'] = t3lib_div::minifyJavaScript($items['_scriptCode']['code']);
					}

					# add compressed code
					if($items['_scriptCode']['code']) {
						$files[$group] = $items;
					}

					# todo: hack?!
					if(!t3lib_div::minifyJavaScript($items['TS_inlineJSint'])) {
						unset($files[$group]['TS_inlineJSint']);
					}

				break;

				# compress external libraries
				case 'jsLibs':

					# loop thru all files
					foreach($items as $name => $options) {

						# compress if configured
						if($options['compress'] || $this->settings['compress_js_force']) {

							# get full path, file hash and new file path
							$full_path = realpath(PATH_site . $options['file']);
							$hash = md5($full_path . filemtime($full_path) . filesize($full_path));
							$file = PATH_site . 'typo3temp/javascript_' . $hash . '.js';

							# write a new file if neccessary
							if(!file_exists($file)) {
								$data = file_get_contents($full_path);
								file_put_contents($file, t3lib_div::minifyJavaScript($data));
							}

							# set new path
							$options['file'] = '/typo3temp/javascript_' . $hash . '.js';
						}

						# add compressed file
						$files[$group][$name] = $options;
					}

				break;

				# compress external files
				case 'jsFiles':
				case 'jsFooterFiles':

					# loop thru all stylesheet files
					foreach($items as $path => $options) {

						# break if we're finished
						if($options['finished']) {
							break;
						}

						# cache the current path
						$currentPath = $path;

						# compress if configured
						if($options['compress'] || $this->settings['compress_js_force']) {

							# get full path, file hash and new file path
							$full_path = realpath(PATH_site . $path);
							$hash = md5($full_path . filemtime($full_path) . filesize($full_path));
							$file = PATH_site . 'typo3temp/javascript_' . $hash . '.js';

							# write a new file if neccessary
							if(!file_exists($file)) {
								$data = file_get_contents($full_path);
								file_put_contents($file, t3lib_div::minifyJavaScript($data));
							}

							# set new path
							$path = '/typo3temp/javascript_' . $hash . '.js';
						}

						# set flag for the finished item
						$options['finished'] = TRUE;

						# add compressed file
						unset($files[$group][$currentPath]);
						$files[$group][$path] = $options;
					}

				break;
			}
		}
	}

	public function compressCSS(&$files, $renderer) {

		# skip if disabled
		if($this->settings['disable'] || !$this->settings['compress_css']) {
			return;
		}

		# convert headerdata to JS and CSS files
		$this->convertHeaderData($renderer);

		foreach($files as $group => $items) {

			switch($group) {

				# compress inline styles
				case 'cssInline':

					# skip inline code if configured
					if($items['TSFEinlineStyle']['compress'] || $this->settings['compress_css_force']) {
						$items['TSFEinlineStyle']['code'] = Minify_CSS_Compressor::process($items['TSFEinlineStyle']['code']);
					}

					//$items['TSFEinlineStyle']['code'] = $this->importCssMediaFiles($items['TSFEinlineStyle']['code']);

					# add compressed styles
					if($items['TSFEinlineStyle']['code']) {
						$files[$group] = $items;
					}

				break;

				case 'cssFiles':

					# loop thru all stylesheet files
					foreach($items as $path => $options) {

						# break if we're finished
						if($options['finished']) {
							break;
						}

						# cache the current path
						$currentPath = $path;

						# skip single file if configured
						if($options['compress'] || $this->settings['compress_css_force']) {

							# get full path, file hash and new file path
							$full_path = realpath(PATH_site . $path);
							$hash = md5($full_path . filemtime($full_path) . filesize($full_path));
							$file = PATH_site . '/typo3temp/stylesheet_' . $hash . '.css';

							# write a new file if neccessary
							if(!file_exists($file)) {
								$data = file_get_contents($full_path);

								# embed external styles
								if(FALSE) {
									$data = $this->importExternalFiles($data);
								}

								# base 64 encode images and fonts
								if(FALSE) {
									$data = $this->encodeExternalFiles($data);
								}

								# replace relative paths
								if(FALSE) {
									$data = $this->replaceRelativePaths($data);
								}

								# compress styles
								$data = Minify_CSS_Compressor::process($data);

								# save cache file
								file_put_contents($file, $data);
							}

							# set new path
							$path = '/typo3temp/stylesheet_' . $hash . '.css';
						}

						# set flag for the finished item
						$options['finished'] = TRUE;

						# add compressed file
						unset($files[$group][$currentPath]);
						$files[$group][$path] = $options;
					}

				break;
			}
		}
	}

	protected function importExternalFiles($data) {

		if(preg_match_all('~\@import.?\"(.*)\"(.*);~isU', $data, $matches)) {
			//print_r($matches);
		}

		return $data;
	}

	protected function encodeExternalFiles($data) {

		if (preg_match_all('~url\(([^\)]+)\)~i', $data, $matches)) {

			foreach($matches[1] as $path) {

				# get rid of quotes
				$path = trim($path, '"\'');

				if(!preg_match('~\.(gif|jpg|png)$~', $path, $extension)) {
					continue;
				}

				$images[$path] = $extension[1];
			}

			foreach($images as $path => $extension) {
				//$up = substr_count($relative_img, '../');
				//$absolute_img = $root_dir.preg_replace('#([^/]+/){'.$up.'}(\.\./){'.$up.'}#', '', $requested_dir.'/'.$relative_img);

				if (file_exists('.' . $path)) {
					$source = file_get_contents('.' . $path);
					$img_data = 'data:image/' . $extension . ';base64,' . base64_encode($source);
					$data = preg_replace('~url\(.*\)~iU', "url($img_data)", $data);
				}
			}
		}

		return $data;
	}

	public function concatenate(&$files, $renderer) {

		# skip if disabled
		if($this->settings['disable'] || (!$this->settings['concatenate_js'] && !$this->settings['concatenate_css'])) {
			return;
		}

		# convert headerdata to JS and CSS files
		//$this->convertHeaderData($renderer);

		# concatenate javascript files
		if($this->settings['concatenate_js']) {
			$this->concatenateJS($files, $renderer);
		}

		#concatenate styles
		if($this->settings['concatenate_css']) {
			$this->concatenateCSS($files, $renderer);
		}
	}

	protected function concatenateJS(&$files, $renderer) {

		# get neccessary javascript files
		$items = array_merge($files['jsLibs'], $files['jsFiles']);

		# unset the old files
		$files['jsLibs'] = array();
		$files['jsFiles'] = array();

		# glue them together
		$cache = array();
		foreach($items as $path => $options) {

			# get full path, file hash and new file path
			$path = ($options['file']) ? realpath(PATH_site . $options['file']) : realpath(PATH_site . $path);
			$cache[$options['section']]['hash'] .= $path;
			$cache[$options['section']]['data'] .= file_get_contents($path) . chr(10);
		}

		# process all cache items
		foreach($cache as $section => $item) {

			# calculate new file hash
			$hash = md5($item['hash']);

			$file = realpath(PATH_site . '/typo3temp') . '/javascript_' . $hash . '.js';

			# write a new file if neccessary
			if(!file_exists($file)) {
				file_put_contents($file, $item['data']);
			}

			# assign the new file
			array_push($files['jsLibs'], array(
				'file' => '/typo3temp/javascript_' . $hash . '.js',
				'section' => $section,
				'compress' => 1
			));
		}
	}

	protected function concatenateCSS(&$files, $renderer) {

		# get neccessary stylesheet files
		$items = $files['cssFiles'];

		# glue them together by media type
		$hash = array();
		$data = array();
		$data_final = '';
		$hash_final = '';
		foreach($items as $path => $options) {

			# get full path
			$path = realpath(PATH_site . $path);

			# calculate media string
			$media = explode(',', $options['media']);
			sort($media);
			$media = implode(',', $media);

			# set media hash
			$hash[$media] .= $path;

			# get media string
			$source = file_get_contents($path);

			# sniff out font face declarations and stick 'em on top
			preg_match_all('~\@font-face.?\{.*\}~isU', $source, $matches);

			if($matches[0]) {
				$data_final .= implode(chr(10), $matches[0]);
			}

			# build media string
			$data[$media] .= $source;
		}

		# build final string
		foreach($data as $media => $source) {
			$data_final .= '@media ' . $media . '{' . $source . '}';
			$hash_final .= $hash[$media];
		}

		# get file hash and new file path
		$hash_final = md5($hash_final);
		$file = realpath(PATH_site . '/typo3temp') . '/stylesheet_' . $hash_final . '.css';

		# write a new file if neccessary
		if(!file_exists($file)) {
			file_put_contents($file, $data_final);
		}

		# remove old files
		$files['cssFiles'] = array();

		# assign the new file
		$renderer->addCssFile('/typo3temp/stylesheet_' . $hash_final . '.css', 'stylesheet', 'all');
	}

	public function convertHeaderData($renderer) {

		# skip if already done
		if(self::$converted)
			return;

		# set new state
		self::$converted = TRUE;
	}
}

?>