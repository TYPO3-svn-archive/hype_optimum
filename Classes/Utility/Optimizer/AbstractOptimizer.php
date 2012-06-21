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
abstract class Tx_HypeOptimum_Utility_Optimizer_AbstractOptimizer
	implements Tx_HypeOptimum_Utility_Optimizer_OptimizerInterface, t3lib_singleton {

	/**
	 * Holds a stack of filters which gets applied to the styles.
	 */
	protected $filters = array();

	/**
	 * Holds already processed files.
	 */
	protected $processedFiles = array();

	/**
	 * Defines the initial base path for the files.
	 */
	protected $basePath;

	/**
	 *
	 */
	protected $cache;

	/**
	 *
	 *
	 */
	public function __construct() {

		t3lib_cache::initializeCachingFramework();

		try {
			$this->cache = $GLOBALS['typo3CacheManager']->getCache('tx_hypeoptimum');
		}catch(Exception $e) {
			$this->cache = $GLOBALS['typo3CacheFactory']->create(
				'tx_hypeoptimum',
				$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_hypeoptimum']['frontend'],
				$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_hypeoptimum']['backend'],
				$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_hypeoptimum']['options']
			);
		}
	}

	/**
	 *
	 */
	public function addFilter(Tx_HypeOptimum_Utility_Optimizer_Filter_FilterInterface $filter) {
		$filter->injectOptimizer($this);
		array_push($this->filters, $filter);
	}

	/**
	 *
	 */
	public function addProcessedFile($processedFile) {
		return array_push($this->processedFiles, $processedFile);
	}

	/**
	 *
	 */
	public function hasProcessedFile($processedFile) {
		return in_array($processedFile, $this->processedFiles);
	}

	/**
	 *
	 */
	public function setBasePath($basePath) {
		$this->basePath = (string)$basePath;
	}

	/**
	 *
	 */
	public function getBasePath() {
		return $this->basePath;
	}

	/**
	 *
	 */
	public function getContentCache() {
		return $this->contentCache;
	}

	/**
	 *
	 */
	public function normalizeFilePath($filePath) {
		return realpath($filePath);
	}

	/**
	 *
	 *
	 */
	abstract public function optimize($data);

	/**
	 *
	 *
	 */
	abstract public function optimizeFile($data);
}

?>