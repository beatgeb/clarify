<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

class Cache {

	private $cache = array();

	public function get($key) {
		if (isset($this->cache[$key])) {
			return $this->cache[$key];
		}
		return null;
	}

	public function set($key, $value) {
		$this->cache[$key] = $value;
	}

}