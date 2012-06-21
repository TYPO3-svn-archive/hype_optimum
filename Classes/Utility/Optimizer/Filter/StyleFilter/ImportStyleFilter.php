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
 * Import Style Filter
 * Directly imports external referenced stylsheets into the stylesheet.
 */
class Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_ImportStyleFilter
	extends Tx_HypeOptimum_Utility_Optimizer_Filter_AbstractFilter {

	/**
	 * @var array Holds path history of imported files.
	 */
	protected $pathStack = array();

	/**
	 * @var integer Defines the maximum recursion depth for importing files.
	 */
	protected $recursionDepth = 0;

	/**
	 * @var integer Defines the maximum filesize for a file to be imported.
	 */
	protected $maximumFilesize = 102400;

	/**
	 * Sets the maximum recursion depth for importing files.
	 * @param integer The maximum recursion depth.
	 * @return void
	 */
	public function setRecursionDepth($recursionDepth) {
		$this->recursionDepth = (integer)$recursionDepth;
	}

	/**
	 * Gets the maximum recursion depth for importing files.
	 * @return integer The maximum recursion depth.
	 */
	public function getRecursionDepth() {
		return $this->recursionDepth;
	}

	/**
	 * Sets the maximum file size for files to be imported.
	 * @param integer $maximumFilesize
	 * @return void
	 */
	public function setMaximumFilesize($maximumFilesize) {
		$this->maximumFilesize = (integer)$maximumFilesize;
	}

	/**
	 * Gets the maximum file size for files to be imported.
	 * @return integer
	 */
	public function getMaximumFilesize() {
		return $this->maximumFilesize;
	}

	/**
	 * Processes the importing of stylesheets.
	 * @param string $data The data to be processed.
	 * @return string The processed data.
	 */
	public function process($data) {
		return preg_replace_callback('~@import[ ]url\([\'|"]([^\)]+)[\'|"]\)(.*)\;~iU', array($this, 'import'), $data);
	}

	/**
	 * Embeds files into the given stylesheet contents.
	 * @param string $match The import definition.
	 * @return string The processed import definition.
	 */
	protected function import($match) {

		# determine file path
		if(preg_match('~//|/\~', $match[1])) {
			$path = $this->getOptimizer()->getBasePath() . DIRECTORY_SEPARATOR . $match[1];
		} else {
			$path = pathinfo($this->getFilePath(), PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $match[1];
		}

		# get the real path of the file
		$filePath = $this->getOptimizer()->normalizeFilePath($path);

		# set data to the current definition
		$data = $match[0];

		if(file_exists($filePath) && filesize($filePath) < $this->getMaximumFilesize()) {
			if(!$this->getOptimizer()->hasProcessedFile($filePath)){

				# optimize file
				$newFilePath = $this->getOptimizer()->optimizeFile($filePath);

				# get new file data
				$fileData = @file_get_contents($newFilePath);

				if($fileData) {
					$data = $fileData;
					unset($fileData);
				}

				# set media type if found
				if($match[2]) {
					$data = '@media ' . $match[2] . ' {' . $data . '}';
				}
			}
		}

		return $data;
	}
}

?>