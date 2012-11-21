<?php

	class TikoCacheController {
		
		private $cacheAPI;
		private $URL;
		private $requiredCacheParameters = Array();
		private $requiredCacheParametersWereLoadedFromCache = false;
		private $filledCacheParameters = Array();
		private $cacheStringForURL;

		public function __construct($cacheAPI) {
			$this->cacheAPI = $cacheAPI;
			// Get URL
			$this->URL = $this->cacheAPI->getURL();
			// Try to load the requiredCacheParameters for this URL
			$requiredCacheParameters = $this->cacheAPI->get("tiko_requiredCacheParameters_" . $this->URL);
			if ($requiredCacheParameters !== null) {
				$this->requiredCacheParameters = json_decode($requiredCacheParameters);
				$this->requiredCacheParametersWereLoadedFromCache = true;
				// Check if all requiredparameters are already filled
				$this->getFromCacheIfAllRequiredParametersAreFilled();
			}
		}

		public function fillCacheParameter($parameterName, $parameterValue) {
			$this->filledCacheParameters[$parameterName] = $parameterValue;
			// If all requiredCacheParameters are filled, try to load the item from cache
			$this->getFromCacheIfAllRequiredParametersAreFilled();
		}

		public function requireCacheParameter($parameterName) {
			// Only store cache-parameters if they were not already loaded from cache
			if ($this->requiredCacheParametersWereLoadedFromCache) {
				return;
			}
			// Store cache parameter
			$this->requiredCacheParameters[$parameterName] = true;
		}

		public function cache($item) {
			// If all required praameters are not filled, we cannot cache
			$this->cacheStringForURL = $this->createCacheStringForURL();
			if ($this->cacheStringForURL === false) {
				return false;
			}
			// Cache requiredCacheParameters
			$this->cacheAPI->set("tiko_requiredCacheParameters_" . $this->URL, json_encode($this->requiredCacheParameters));
			// Cache item
			$this->cacheAPI->set("tiko_filledCacheParameters_" . $this->URL . "_" . $this->cacheStringForURL, $item);
		}

		public function removeCacheForURL($url=null) {
			if ($url === null) {
				$url = $this->URL;
				$this->requiredCacheParameters = Array();
			}
			$this->cacheAPI->remove("tiko_requiredCacheParameters_" . $url);
		}

		public function createCacheStringForURL() {
			// Build cacheString first
			$cacheArray = Array();
			foreach($this->requiredCacheParameters as $parameterName => $someValue) {
				if (!isset($this->filledCacheParameters[$parameterName])) {
					return false;
				}
				$cacheArray[$parameterName] = $this->filledCacheParameters[$parameterName];
			}
			// Sort alphabetically on key
			ksort($cacheArray);
			return json_encode($cacheArray);
		}

		private function getFromCacheIfAllRequiredParametersAreFilled() {
			$this->cacheStringForURL = $this->createCacheStringForURL();
			if ($this->cacheStringForURL === false) {
				return false;
			}
			// Lookup in cache and return if it is found
			$cachedItem = $this->cacheAPI->get("tiko_filledCacheParameters_" . $this->URL . "_" . $this->cacheStringForURL);
			if ($cachedItem !== null) {
				$this->cacheAPI->resolve($cachedItem);
			}
		}
	}

?>