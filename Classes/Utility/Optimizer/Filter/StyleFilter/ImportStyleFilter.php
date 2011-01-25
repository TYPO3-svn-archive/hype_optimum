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
class Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_ImportStyleFilter
	extends Tx_HypeOptimum_Utility_Optimizer_Filter_AbstractFilter {

	/**
	 * Holds path history of imported files.
	 */
	protected $pathStack = array();

	/**
	 * Defines the maximum recursion depth for importing files.
	 */
	protected $recursionDepth = 0;

	/**
	 *
	 */
	public function setRecursionDepth($recursionDepth) {
		$this->recursionDepth = $recursionDepth;
	}

	/**
	 *
	 */
	public function getRecursionDepth() {
		return $this->recursionDepth;
	}

	/**
	 *
	 */
	public function process($data) {
		return preg_replace_callback('~@import[ ]url\([\'|"]([^\)]+)[\'|"]\)(.*)\;~iU', array($this, 'import'), $data);
	}

	/**
	 *
	 */
	protected function import($match) {

		# determine file path
		if(preg_match('~//|/\~', $match[1])) {
			$path = $this->getOptimizer()->getBasePath() . DIRECTORY_SEPARATOR . $match[1];
		} else {
			$path = pathinfo($this->getFilePath(), PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $match[1];
		}

		$filePath = $this->getOptimizer()->normalizeFilePath($path);

		if(file_exists($filePath)) {
			if($this->getOptimizer()->hasProcessedFile($filePath)) {

				# @todo Log files which gets called multiple times.
				$data = '';

			} else {

				# optimize file
				$newFilePath = $this->getOptimizer()->optimizeFile($filePath);

				try {
					$data = file_get_contents($newFilePath);
				} catch(Exception $e) {

				}


				if($match[2]) {
					$data = '@media ' . $match[2] . ' {' . $data . '}';
				}
			}

		} else {

			# @todo Log missing files.
			$data = $match[0];
		}

		return $data;
	}
}

?>