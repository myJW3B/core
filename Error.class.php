<?php
/**
 * Requires Config::error_dir to be set
 * 	This is to path to the directory where the error logs will be saved.
 */
namespace JW3B\core\Error;

class Error {

	public static function errors($notes, $type='basic', $die=false){
		$dir = Config::$error_dir;
		if(!is_dir($dir)) mkdir($dir, 0777);
		$file = $dir.$type.'.dat';
		$GET = print_r($_GET, true);
		$POST = print_r($_POST, true);
		$SES = ''; //print_r($_SESSION, true);
		$FILES = print_r($_FILES, true);
		//	$COK = isset($_COOKIE['Yohns']) ? 'member' : print_r($_COOKIE, true); //print_r($_COOKIE, true);
		$debug = print_r(debug_backtrace(), true);
		$lastError = print_r(error_get_last(), true);
		$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct input';
		$gg = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
		$rs = isset($_SERVER['REDIRECT_STATUS']) ? $_SERVER['REDIRECT_STATUS'] : '';
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$putIn = '['.date('F j Y @ g:i:s A', time()).']
		'.$notes.'

		REQUEST_URI = '.$_SERVER['REQUEST_URI'].'
		SCRIPT_NAME = '.$_SERVER['SCRIPT_NAME'].'
		PHP_SELF = '.$_SERVER['PHP_SELF'].'
		REDIRECT_STATUS = '.$rs.'
		IP = '.$ip.'
		HTTP_USER_AGENT = '.$_SERVER['HTTP_USER_AGENT'].'
		HTTP_REFERER = '.$ref.'
		QUERY_STRING = '.$gg.'
		Last Error = '.$lastError.'

		GET = '.$GET.'
		POST = '.$POST.'
		SESSION = '.$SES.'
		FILES = '.$FILES.'

		debug_backtrace = '.$debug.'
		';
		//COK = '.$COK.'
		//';

		$gotten = is_file($file) ? file_get_contents($file) : '';
		//	$fp = fopen($file, 'w');
		$fp = fopen($file, 'a');
			flock($fp, LOCK_EX);
			fwrite($fp, urlencode($putIn)."\n".$gotten);
			flock($fp, LOCK_UN);
			fclose($fp);
		return $die == true ? die('<pre>'.$putIn.'</pre>') : '';
	}
}
