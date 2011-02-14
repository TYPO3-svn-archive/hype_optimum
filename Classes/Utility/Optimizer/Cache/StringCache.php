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
class Tx_HypeOptimum_Utility_Optimizer_Cache_StringCache extends t3lib_cache_frontend_StringFrontend {

	/**
	 * Checks the validity of an entry identifier. Returns true if it's valid.
	 *
	 * @param string $identifier An identifier to be checked for validity
	 * @return boolean
	 * @author Christian Jul Jensen <julle@typo3.org>
	 */
	public function isValidEntryIdentifier($identifier) {
		return file_exists($identifier);
	}
}

?>