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

/**
 * Embed Style Filter
 * Directly embeds certain files directly into a stylesheet via the data uri scheme.
 */
class Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_EmbedStyleFilter
	extends Tx_HypeOptimum_Utility_Optimizer_Filter_AbstractFilter {

	/**
	 * @var string Defines the base path of the processed files.
	 */
	protected $basePath;

	/**
	 * @var integer Defines the maximum filesize for a file to be embedded.
	 */
	protected $maximumFilesize = 8000;

	/**
	 * @var array Defines allowed file types for files to be embedded.
	 */
	protected $allowedFileExtensions = array('gif', 'png', 'jpg', 'jpeg', 'ttf', 'otf', 'woff');

	/**
	 * Sets the maximum file size for files to be embedded.
	 * @param integer $maximumFilesize
	 * @return void
	 */
	public function setMaximumFilesize($maximumFilesize) {
		$this->maximumFilesize = (integer)$maximumFilesize;
	}

	/**
	 * Gets the maximum file size for files to be embedded.
	 * @return integer
	 */
	public function getMaximumFilesize() {
		return $this->maximumFilesize;
	}

	/**
	 * Sets the allowed file extensions for files to be embedded.
	 * @param array $allowedFileExtensions
	 * @return void
	 */
	public function setAllowedFileExtensions(array $allowedFileExtensions) {
		$this->allowedFileExtensions = $allowedFileExtensions;
	}

	/**
	 * Gets the allowed file extensions for files to be embedded.
	 * @return array
	 */
	public function getAllowedFileExtensions() {
		return $this->allowedFileExtensions;
	}

	/**
	 * Adds an allowed file extensions for files to be embedded.
	 * @param string $allowedFileExtension
	 * @return void
	 */
	public function addAllowedFileExtension($allowedFileExtension) {
		if(!in_array($allowedFileExtension, $this->allowedFileExtensions)) {
			array_push($this->allowedFileExtensions, $allowedFileExtension);
		}
	}

	/**
	 * Adds an allowed file extensions for files to be embedded.
	 * @param string $allowedFileExtension
	 * @return void
	 */
	public function removeAllowedFileExtension($allowedFileExtension) {
		$this->addAllowedFileExtensions = array_values(array_diff($this->allowedFileExtensions, array($allowedFileExtension)));
	}

	/**
	 * Processes the embedding of allowed files.
	 * @param string $data The data to be processed.
	 * @return string The processed data.
	 */
	public function process($data) {
		return preg_replace_callback('~url\([\'|"]([^\)]+)[\'|"]\)~iU', array($this, 'embed'), $data);
	}

	/**
	 * Embeds files into the given stylesheet contents.
	 * @param string $match The url part of a stylesheet definition.
	 * @return string The processed url part of a stylesheet definition.
	 */
	protected function embed($match) {

		# get the real path of the file
		$filePath = realpath(PATH_site . $match[1]);
		$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

		# set data to the current definition
		$data = $match[0];

		# embed file and remove the url declaration
		if(file_exists($filePath) && filesize($filePath) < $this->getMaximumFilesize() && !preg_match('~#~', $filePath)) {

			# get mime type
			switch($fileExtension) {

				case 'jpeg':
				case 'jpg':
				case 'png':
				case 'mng':
				case 'gif':
				case 'bmp':
				case 'tiff':
				case 'tif':
					$mime = 'image';
					break;

				case 'ttf':
				case 'otf':
				case 'woff':
					$mime = 'font';
					break;

				default:
					return $data;
					break;
			}

			$mime .= '/' . strtolower($fileExtension);

			# encode file data
			$contents = base64_encode(file_get_contents($filePath));
			$data = 'url(data:' . $mime . ';base64,' . $contents . ')';

			# add file to cache
			//$this->cache->set($filePath, $contents);
		}

		return $data;
	}
}

?>