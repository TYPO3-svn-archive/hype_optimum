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
class Tx_HypeOptimum_Utility_Optimizer_StyleOptimizer
	extends Tx_HypeOptimum_Utility_Optimizer_AbstractOptimizer {

	/**
	 * Defines the initial base path for the files.
	 */
	protected $basePath;

	/**
	 * Holds a stack of filters which gets applied to the styles.
	 */
	protected $filters = array();

	/**
	 * Holds already processed files.
	 */
	protected $processedFiles = array();

	/**
	 * Handles caching of processed files.
	 */
	protected $cache;

	/**
	 *
	 */
	public function __construct() {
		$this->cache = new Tx_HypeOptimum_Utility_Optimizer_Cache_FileCache;
		$this->cache->setStoragePath(PATH_site);
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
	public function getCache() {
		return $this->cache;
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
	public function addFilter(Tx_HypeOptimum_Utility_Optimizer_Filter_FilterInterface $filter) {
		$filter->injectOptimizer($this);
		//$filter->prepare();
		array_push($this->filters, $filter);
	}

	/**
	 *
	 */
	public function normalizeFilePath($filePath) {
		return realpath($filePath);
	}

	/**
	 *
	 */
	public function optimize($data, $filePath = NULL) {

		$filePath = $this->normalizeFilePath($filePath);

		foreach($this->filters as $filter) {
			$filter->setFilePath($filePath);
			$data = $filter->process($data);
		}

		return $data;
	}

	/**
	 *
	 */
	public function optimizeFile($filePath) {

		$filePath = $this->normalizeFilePath($filePath);

		if(($path = $this->cache->load($filePath)) === FALSE) {
			$data = $this->optimize(file_get_contents($filePath), $filePath);
			$path = $this->cache->save($filePath, $data);
		}

		$this->addProcessedFile($filePath);

		return $path;
	}
}

?>