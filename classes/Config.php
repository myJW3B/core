<?php
namespace JW3B\core;

class Config {
	public static $c = []; // sites configs
	public static $l = []; // language translations
	public static $r = []; // reserved words
	public static $t = []; // database table
	public static $w = []; // website settings... maybe.. idk yet..

	/*
	 *@ $path path to config file.
	 */
	public function __construct($directory = __DIR__ . '/../../../../lib/config') {
		self::$c = include ($directory . '/config.php');
		self::$r = include ($directory . '/reserved_usernames.php');
		self::$t = include ($directory . '/database_tables.php');
		self::$l = include ($directory . '/language.php');
	}

	public static function get($key, $w = 'c') {
		return self::$$w[$key] ?? false;
	}

	public static function set($key, $value, $w) {
		self::$$w[$key] = $value;
		return true;
	}
}
