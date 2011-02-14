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
class Tx_HypeOptimum_Utility_Optimizer_Cache_HybridCache extends t3lib_cache_backend_FileBackend {

	/**
	 * @var t3lib_cache_backend_DbBackend
	 */
	protected $databaseCache;

	/**
	 *
	 * @param array $options
	 */
	public function __construct(array $options = array()) {

		# load file backend
		parent::__construct(array('cacheDirectory' => $options['cacheDirectory']));

		# load database backend
		$this->databaseCache = t3lib_div::makeInstance('t3lib_cache_backend_DbBackend', array('cacheTable' => $options['cacheTable'], 'tagsTable' => $options['tagsTable']));
		$this->databaseCache->setCache(t3lib_div::makeInstance('t3lib_cache_frontend_StringFrontend', 'test', $this->databaseCache));
	}

	/**
	 * Saves data in the cache.
	 *
	 * @param string An identifier for this specific cache entry
	 * @param string The data to be stored
	 * @param array Tags to associate with this cache entry
	 * @param integer Lifetime of this cache entry in seconds. If NULL is specified, the default lifetime is used. "0" means unlimited liftime.
	 * @return void
	 * @throws t3lib_cache_Exception if no cache frontend has been set.
	 * @throws InvalidArgumentException if the identifier is not valid
	 * @throws t3lib_cache_Exception_InvalidData if the data is not a string
	 */
	public function set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL) {

		if(!$this->cache instanceof t3lib_cache_frontend_Frontend) {
			throw new t3lib_cache_Exception(
				'No cache frontend has been set yet via setCache().',
				1204111375
			);
		}

		if(!is_string($data)) {
			throw new t3lib_cache_Exception_InvalidData(
				'The specified data is of type "' . gettype($data) . '" but a string is expected.',
				1204481674
			);
		}

		# get file modification time
		$modificationTime = filemtime($entryIdentifier);

		# get hashed filename
		$entryIdentifier = $this->getCachedFilename($entryIdentifier);

		# set database cache
		$this->databaseCache->set($entryIdentifier, (string)$modificationTime, $tags, $lifetime);

		if($this->databaseCache->has($entryIdentifier)) {

			$this->remove($entryIdentifier);

			$temporaryCacheEntryPathAndFilename = $this->root . $this->cacheDirectory . uniqid() . '.temp';
			if(strlen($temporaryCacheEntryPathAndFilename) > $this->maximumPathLength) {
				throw new t3lib_cache_Exception(
					'The length of the temporary cache file path "' . $temporaryCacheEntryPathAndFilename .
						'" is ' . strlen($temporaryCacheEntryPathAndFilename) . ' characters long and exceeds the maximum path length of ' .
						$this->maximumPathLength . '. Please consider setting the temporaryDirectoryBase option to a shorter path. ',
					1248710426
				);
			}

			$result = file_put_contents($temporaryCacheEntryPathAndFilename, $data);

			if($result === FALSE) {
				throw new t3lib_cache_exception(
					'The temporary cache file "' . $temporaryCacheEntryPathAndFilename . '" could not be written.',
					1204026251
				);
			}

			$i = 0;
			$cacheEntryPathAndFilename = $this->root . $this->cacheDirectory . $entryIdentifier;
				// @TODO: Figure out why the heck this is done and maybe find a smarter solution, report to FLOW3
			while (!rename($temporaryCacheEntryPathAndFilename, $cacheEntryPathAndFilename) && $i < 5) {
				$i++;
			}

			// @FIXME: At least the result of rename() should be handled here, report to FLOW3
			if($result === FALSE) {
				throw new t3lib_cache_exception(
					'The cache file "' . $cacheEntryPathAndFilename . '" could not be written.',
					1222361632
				);
			}
		}
	}

	/**
	 * Loads data from a cache file.
	 *
	 * @param string $entryIdentifier An identifier which describes the cache entry to load
	 * @return mixed The cache entry's content as a string or FALSE if the cache entry could not be loaded
	 * @api
	 */
	public function get($entryIdentifier) {

		if($this->has($entryIdentifier)) {
			$entryIdentifier = $this->getCachedFilename($entryIdentifier);
			return $this->cacheDirectory . $entryIdentifier;
		}

		return FALSE;
	}

	/**
	 * Checks if a cache entry with the specified identifier exists.
	 *
	 * @param string $entryIdentifier: An identifier specifying the cache entry
	 * @return boolean TRUE if such an entry exists, FALSE if not
	 */
	public function has($entryIdentifier) {

		$filePath = $entryIdentifier;
		$entryIdentifier = $this->getCachedFilename($entryIdentifier);

		if($this->databaseCache->has($entryIdentifier) && file_exists($this->root . $this->cacheDirectory . $entryIdentifier)) {
			$lastModificationTime = (int)$this->databaseCache->get($entryIdentifier);
			$currentModificationTime = filemtime($filePath);
			return ($currentModificationTime <= $lastModificationTime);
		}

		return FALSE;
	}

	/**
	 * Removes all cache entries matching the specified identifier.
	 * Usually this only affects one entry.
	 *
	 * @param string $entryIdentifier Specifies the cache entry to remove
	 * @return boolean TRUE if (at least) an entry could be removed or FALSE if no entry was found
	 * @api
	 */
	public function remove($entryIdentifier) {

		$entryIdentifier = $this->getCachedFilename($entryIdentifier);

		if($entryIdentifier !== basename($entryIdentifier)) {
			throw new InvalidArgumentException(
				'The specified entry identifier must not contain a path segment.',
				1282073035
			);
		}

		if($this->databaseCache->remove($entryIdentifier)) {
			$pathAndFilename = $this->root . $this->cacheDirectory . $entryIdentifier;
			if(!file_exists($pathAndFilename)) {
				return FALSE;
			}
			if(unlink($pathAndFilename) === FALSE) {
				return FALSE;
			}
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Removes all cache entries of this cache.
	 *
	 * @return void
	 * @api
	 */
	public function flush() {
		$this->databaseCache->flush();
		t3lib_div::rmdir($this->root . $this->cacheDirectory, TRUE);
	}

	/**
	 * Removes all cache entries of this cache which are tagged by the specified tag.
	 *
	 * @param string $tag The tag the entries must have
	 * @return void
	 */
	public function flushByTag($tag) {

		$entryIdentifiers = $this->databaseCache->findIdentifiersByTag($tag);

		foreach($entryIdentifiers as $entryIdentifier) {
			$this->remove($entryIdentifier);
		}

		$this->databaseCache->flushByTag($tag);
	}

	/**
	 * Removes all cache entries of this cache which are tagged by the specified tags.
	 *
	 * @param	array	The tags the entries must have
	 * @return void
	 */
	public function flushByTags(array $tags) {

		$entryIdentifiers = $this->databaseCache->findIdentifiersByTags($tags);

		foreach($entryIdentifiers as $entryIdentifier) {
			$this->remove($entryIdentifier);
		}

		$this->databaseCache->flushByTags($tag);
	}

	/**
	 * Finds and returns all cache entry identifiers which are tagged by the
	 * specified tag.
	 *
	 * @param string $tag The tag to search for
	 * @return array An array with identifiers of all matching entries. An empty array if no entries matched
	 */
	public function findIdentifiersByTag($tag) {
		return $this->databaseCache->findIdentifiersByTag($tag);
	}

	/**
	 * Finds and returns all cache entry identifiers which are tagged by the
	 * specified tags.
	 * The asterisk ("*") is allowed as a wildcard at the beginning and the end
	 * of a tag.
	 *
	 * @param array Array of tags to search for, the "*" wildcard is supported
	 * @return array An array with identifiers of all matching entries. An empty array if no entries matched
	 * @author	Ingo Renner <ingo@typo3.org>
	 */
	public function findIdentifiersByTags(array $tags) {
		return $this->databaseCache->findIdentifiersByTags($tags);
	}

	/**
	 * Does garbage collection
	 *
	 * @return void
	 */
	public function collectGarbage() {
		$this->databaseCache->collectGarbage();
		parent::collectGarbage();
	}

	/**
	 *
	 * @return string The cached filename
	 */
	public function getCachedFilename($path) {

		$metaData = pathinfo($path);
		$filename = implode('.', array($metaData['filename'], md5($path), $metaData['extension']));

		return $filename;
	}
}

?>