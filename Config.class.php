<?php
namespace JW3B\core\Config;

class Config {
	public static $config = [];
	public function __construct(){
		self::$config = file_get_contents(__DIR__.'../../config.php');
	}
}
