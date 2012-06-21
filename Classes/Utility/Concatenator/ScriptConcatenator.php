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
 *
 */
class Tx_HypeOptimum_Utility_Concatenator_ScriptConcatenator
	extends Tx_HypeOptimum_Utility_Concatenator_AbstractConcatenator {

	/**
	 *
	 */
	public function addFile($path, $options) {
		$this->files[$path] = $options;
	}

	/**
	 *
	 */
	public function removeFile($path) {
		unset($this->files[$path]);
	}

	/**
	 *
	 */
	public function concatenate() {

		# skip if no files were added
		if(count($this->files) < 1) {
			return FALSE;
		}

		# generate unique files hash
		$hash = md5(implode('', array_keys($this->files)));

		# determine new path
		$newPath = 'typo3temp/' . $hash . '.js';

		# create compressed file
		if(!file_exists(PATH_site . $newPath)) {

			# get and prepare file contents
			$data = '';
			foreach($this->files as $file => $options) {
				$data .= file_get_contents(realpath(PATH_site . $file));
			}

			# write new file
			if($data) {
				t3lib_div::writeFileToTypo3tempDir(PATH_site . $newPath, $data);
				unset($data);
			}
		}

		return $newPath;
	}
}

?>