<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Thomas "Thasmo" Deinhamer <thasmo@gmail.com>
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
 * Cdn Style Filter
 * Adds CDN addresses to certain external referenced files based on their filetype.
 */
class Tx_HypeOptimum_Utility_Optimizer_Filter_StyleFilter_CdnStyleFilter
	extends Tx_HypeOptimum_Utility_Optimizer_Filter_AbstractFilter {

	/**
	 * @var array The defined cdn server hosts.
	 */
	protected $hosts = array();

	/**
	 * Adds a cdn host address with associated file extensions.
	 * @param string $hostAddress An cdn host address.
	 * @param array $fileExtensions The associated file extensions for this host.
	 * @return void
	 */
	public function addHost($hostAddress, $fileExtensions = array()) {
		$this->hosts[$hostAddress] = $fileExtensions;
	}

	/**
	 * Removes a cdn host address.
	 * @param string $hostAddress The host address to remove.
	 * @return void
	 */
	public function removeHost($hostAddress) {
		unset($this->hosts[$hostAddress]);
	}

	/**
	 * Processes the adding of cdn host addressed to referenced files.
	 * @param string $data The data to be processed.
	 * @return string The processed data.
	 */
	public function process($data) {
		return preg_replace_callback('~url\([\'|"]([^\)]+)[\'|"]\)~iU', array($this, 'reference'), $data);
	}

	/**
	 * Adds the defined cdn host addresses to referenced, specific filetypes.
	 * @param string $data The url definition.
	 * @return string The new url definition.
	 */
	protected function reference($match) {

		# skip urls
		if(filter_var($match[1], FILTER_VALIDATE_URL) !== FALSE) {
			return $match[0];
		}

		# get real path
		$filePath = realpath(PATH_site . $match[1]);
		$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

		# set data to the current definition
		$data = $match[0];

		# add the cdn host address
		if(file_exists($filePath)) {

			# find a fitting cdn host
			foreach($this->hosts as $hostAddress => $fileExtensions) {
				if(in_array($fileExtension, $fileExtensions) || empty($fileExtensions)) {
					$data = 'url(\'' . $hostAddress . $match[1] . '\')';
					break;
				}
			}
		}

		return $data;
	}
}

?>