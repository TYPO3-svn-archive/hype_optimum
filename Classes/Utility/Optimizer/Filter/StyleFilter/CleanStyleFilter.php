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
 * Clean Style Filter
 * Cleans the stylesheet removing or manipulating various bits.
 */
class Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_CleanStyleFilter
	extends Tx_HypeOptimum_Utility_Optimizer_Filter_AbstractFilter {

	/**
	 * @var boolean Defines if charset declarations should get removed.
	 */
	protected $removeCharsetDeclarations = TRUE;

	/**
	 * Sets whether charset declarations should get removed or not.
	 * @param boolean $removeCharsetDeclarations
	 * @return void
	 */
	public function setRemoveCharsetDeclarations($removeCharsetDeclarations) {
		$this->removeCharsetDeclarations = (boolean)$removeCharsetDeclarations;
	}

	/**
	 * Gets whether charset declarations should get removed or not.
	 * @return boolean
	 */
	public function getRemoveCharsetDeclarations() {
		return $this->removeCharsetDeclarations;
	}

	/**
	 * Processes the cleaning of the stylesheet.
	 * @param string $data The data to be processed.
	 * @return string The processed data.
	 */
	public function process($data) {

		if($this->getRemoveCharsetDeclarations() === TRUE) {
			$data = $this->removeCharsetDeclarations($data);
		}

		return $data;
	}

	/**
	 * Removes charset declarations of stylesheets.
	 * @param string The stylesheet contents of which the charset declarations will be removed.
	 * @return string The stylesheet contents without charset declarations.
	 */
	protected function removeCharsetDeclarations($data) {
		return preg_replace('~@charset[ ][\'|"].*[\'|"];~iU', '', $data);
	}
}

?>