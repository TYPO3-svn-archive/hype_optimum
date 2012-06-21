<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Thomas "Thasmo" Deinhamer <thasmo@gmail.com>
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
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class tx_hypeoptimum_tslib_content implements tslib_cObj_getImgResourceHook {

	/**
	 *
	 */
	public function __construct() {
		$this->graphics = $GLOBALS['TYPO3_CONF_VARS']['GFX'];
	}

	/**
	 * Hook for post-processing image resources
	 *
	 * @param	string		$file: Original image file
	 * @param	array		$configuration: TypoScript getImgResource properties
	 * @param	array		$imageResource: Information of the created/converted image resource
	 * @param	tslib_cObj	$parent: Parent content object
	 * @return	array		Modified image resource information
	 */
	public function getImgResourcePostProcess($file, array $configuration, array $imageResource, tslib_cObj $content) {

		# get image path
		$path = $imageResource[3];
		$fullPath = t3lib_div::fixWindowsFilePath(realpath($path));

		# get new path
		$meta = t3lib_div::split_fileref($path);
		$newPath = $meta['path'] . $meta['filebody'] . '-opt' . '.' . $meta['realFileext'];
		$newFullPath = t3lib_div::fixWindowsFilePath(realpath($meta['path']) . '/' . $meta['filebody'] . '-opt' . '.' . $meta['realFileext']);

		# skip if optimized file exists
		if(file_exists($newFullPath)) {
			return $imageResource;
		}

		# get current size
		$size = filesize($fullPath);

		# switch on filetype
		switch(TRUE) {

			# JPEG
			case in_array($meta['fileext'], array('jpg', 'jpeg')):

				# build command
				$parameters = '-copy none -optimize -progressive -perfect ' . $fullPath . ' ' . $newFullPath;

				if(TYPO3_OS == 'WIN') {
					$command = t3lib_div::fixWindowsFilePath("C:\Users\Thasmo\Dropbox\Development\software\jpegtran\jpegtran.exe") . ' ' . $parameters;
				} else {
					$command = 'jpegtran ' . $parameters;
				}

				break;

			# PNG
			case in_array($meta['fileext'], array('png')):

				# build command
				$parameters = '-rem alla -rem text -reduce ' . $fullPath . ' ' . $newFullPath;

				if(TYPO3_OS == 'WIN') {
					$command = t3lib_div::fixWindowsFilePath("C:\Users\Thasmo\Dropbox\Development\software\pngcrush\pngcrush.exe") . ' ' . $parameters;
				} else {
					$command = 'pngcrush ' . $parameters;
				}

				break;

			# GIF
			case in_array($meta['fileext'], array('gif')):

				/*
				if($this->graphics['im']) {
					$cmd = t3lib_div::imageMagickCommand('identify', '-format \'%n\' ' . $fullPath);
					var_dump($cmd);

					$frames = t3lib_utility_Command::exec($cmd, $one, $two);

					var_dump($frames, $one, $two);
				}
				*/

				break;
		}

		# perform optimization
		if($command) {
			t3lib_utility_Command::exec($command, $output, $return);
			t3lib_div::fixPermissions($newFullPath);

			# update resource path
			if(!$return && filesize($newFullPath) < $size) {
				$imageResource[3] = $newPath;

			# delete file if bigger than original
			} else if(file_exists($newFullPath)) {
				@unlink($newFullPath);
			}
		}

		# return resource array
		return $imageResource;
	}
}
?>