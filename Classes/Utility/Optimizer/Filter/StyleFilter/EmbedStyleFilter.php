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
 *
 */
class Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_EmbedStyleFilter
	extends Tx_HypeOptimum_Utility_Optimizer_Filter_AbstractFilter {

	/**
	 * Defines the base path of the files.
	 */
	protected $basePath;

	/**
	 * Defines the maximum filesize for a file to be embedded.
	 */
	protected $maximumFilesize = 30000;

	/**
	 * Defines all allowed file types which should get embedded.
	 */
	protected $allowedFileExtensions = array('gif', 'png', 'jpg', 'jpeg', 'ttf', 'otf', 'woff');

	/**
	 *
	 */
	public function setMaximumFilesize($maximumFilesize) {
		$this->maximumFilesize = (int)$maximumFilesize;
	}

	/**
	 *
	 */
	public function getMaximumFilesize() {
		return $this->maximumFilesize;
	}

	/**
	 *
	 */
	public function setAllowedFileExtensions(array $allowedFileExtensions) {
		$this->allowedFileExtensions = $allowedFileExtensions;
	}

	/**
	 *
	 */
	public function getAllowedFileExtensions() {
		return $this->allowedFileExtensions;
	}

	/**
	 *
	 */
	public function process($data) {
		return preg_replace_callback('~url\([\'|"]([^\)]+)[\'|"]\)~iU', array($this, 'embed'), $data);
	}

	/**
	 *
	 */
	protected function embed($match) {
		$path = realpath(PATH_site . $match[1]);

		if(file_exists($path) && filesize($path) < $this->getMaximumFilesize() && !preg_match('~#~', $path)) {
			$data = 'url(data:' . $mime . ';base64,' . base64_encode(file_get_contents($path)) . ')';
		} else {
			$data = $match[0];
		}

		return $data;
	}
}

?>