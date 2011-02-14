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
abstract class Tx_HypeOptimum_Utility_Optimizer_Filter_AbstractFilter
	implements Tx_HypeOptimum_Utility_Optimizer_Filter_FilterInterface {

	/**
	 * @var Tx_HypeOptimum_Utility_Optimizer_OptimizerInterface
	 */
	protected $optimizer;

	/**
	 * @var string
	 */
	protected $filePath;

	/**
	 * Injects the optimizer
	 *
	 * @param Tx_HypeOptimum_Utility_Optimizer_OptimizerInterface $optimizer
	 * @return void
	 */
	public function injectOptimizer(Tx_HypeOptimum_Utility_Optimizer_OptimizerInterface $optimizer) {
		$this->optimizer = $optimizer;
	}

	/**
	 * Returns the optimizer
	 *
	 * @return Tx_HypeOptimum_Utility_Optimizer_OptimizerInterface
	 */
	public function getOptimizer() {
		return $this->optimizer;
	}

	/**
	 * Sets the file path
	 *
	 * @param string $filePath
	 * @return void
	 */
	public function setFilePath($filePath) {
		$this->filePath = $filePath;
	}

	/**
	 * Gets the file path
	 *
	 * @return string
	 */
	public function getFilePath() {
		return $this->filePath;
	}
}

?>