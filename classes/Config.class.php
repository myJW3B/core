<?php
namespace JW3B\core\classes\Config;

class Config {
	public static $c = [];

	/*
		@ $path path to config file.
	*/
	public function __construct($path='config.php'){
		self::$c = include(__DIR__.'/../../../../'.$path);
	}

	public static function get($key){
		return self::$c[$key] ? self::$c[$key] : false;
	}
}
