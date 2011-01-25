<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Thomas "Thasmo" Deinhamer (thasmo@gmail.com)
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
 * TYPO3 pageRender class (new in TYPO3 4.3.0)
 * This class render the HTML of a webpage, usable for BE and FE
 *
 * @author	Thomas "Thasmo" Deinhamer <thasmo@gmail.com>
 */
class ux_t3lib_PageRenderer extends t3lib_PageRenderer {
	
	/**
	 * concatenate files into one file
	 * registered handler
	 *
	 * @return void
	 */
	protected function doConcatenate() {
		if(!$GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['concatenateHandler']) {
			parent::doConcatenate();
		}
	}
	
	/**
	 * compress inline code
	 *
	 * @return void
	 */
	protected function doCompress() {
		if($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['concatenateHandler']) {
			parent::doCompress();
			parent::doConcatenate();
		}
	}
}

?>