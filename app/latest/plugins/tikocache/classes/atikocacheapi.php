<?php

	interface TikoCacheAPI {

		public function getUrl();

		public function get($key);

		public function set($key, $value);

		public function resolve($item);

		public function remove($key);

	}

?>