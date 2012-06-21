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
 * Style Optimizer
 * Optimizes stylesheets with the registered style filters.
 */
class Tx_HypeOptimum_Utility_Optimizer_StyleOptimizer extends Tx_HypeOptimum_Utility_Optimizer_AbstractOptimizer {

	/**
	 * Optimized the given stylesheet data.
	 * @param string $data
	 * @param string $filePath
	 * @return string The optimized stylesheet data.
	 */
	public function optimize($data, $filePath = NULL) {

		# normalize the file path
		$filePath = $this->normalizeFilePath($filePath);

		# apply all filters
		foreach($this->filters as $filter) {
			$filter->setFilePath($filePath);
			$data = $filter->process($data);
		}

		# versioning of referenced resources
		$data = preg_replace_callback('~url\([\'|"]([^\)]+)[\'|"]\)~iU', array($this, 'versioning'), $data);

		# return the optimized source
		return $data;
	}

	/**
	 *
	 */
	public function optimizeFile($filePath) {

		$filePath = $this->normalizeFilePath($this->getBasePath() . $filePath);

		if(!$this->cache->has($filePath)) {
			$optimizedFilePath = $this->optimize(file_get_contents($filePath), $filePath);
			$this->cache->set($filePath, $optimizedFilePath);
		}

		$this->addProcessedFile($filePath);

		return $this->cache->get($filePath);
	}

	/**
	 * Adds versioning information into the file path of referenced resources.
	 * @param string $match
	 * @return string
	 */
	protected function versioning($match) {

		# determine url and path parts
		if(filter_var($match[1], FILTER_VALIDATE_URL) !== FALSE) {
			$url = parse_url($match[1]);
			$path = pathinfo($url['path']);
		} else {
			$url = array();
			$path = pathinfo($match[1]);
		}

		# get versionized file path
		$versionizedFilePath = t3lib_div::createVersionNumberedFilename(implode('/', array($path['dirname'], $path['basename'])));

		# rebuild resource path
		$definition = (empty($url))
			? 'url(\'' . $versionizedFilePath . '\')'
			: 'url(\'' .$url['scheme'] . '://' . $url['host'] . $versionizedFilePath . '\')';

		# return the new definition
		return $definition;
	}
}

?>