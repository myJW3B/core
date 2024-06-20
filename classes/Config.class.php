<?php
namespace JW3B\core;

class Config {
	public static $c = []; // sites configs

	/*
		@ $path path to config file.
	*/
	public function __construct($directory='configs'){
		self::$c = include(__DIR__.'/../../../../'.$directory.'/config.php');
	}

	public static function get($key){
		return self::$c[$key] ? self::$c[$key] : false;
	}

	public static function set($key, $value){
		self::$c[$key] = $value;
		return true;
	}
}
