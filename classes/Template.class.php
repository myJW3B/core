<?php
namespace JW3B\core\classes\Template;
use JW3B\core\classes\Config\Config;
use JW3B\core\classes\Error\Error;

class Template {
	public $dir;
	public $Sets;
	public $template;
	public $tempDefault;
	public $find;
	public $rep;

	public function __construct(){
		$this->Sets = Config::$c;
		$this->dir = $this->Sets['root'].'/templates/';
		$this->tempDefault = $this->dir.'default/';
		$this->template = $this->tempDefault;
		if(isset($this->Sets['template'])){
			if(is_dir($this->dir.$this->Sets['template'].'/')){
				$this->template = $this->dir.$this->Sets['template'].'/';
			}
		}
	}

	// $file = the template file we're loading..
	public function loadTemp($file){
		if(is_file($this->template.$file.'.php')){
			return $this->template.$file.'.php';
		} else if(is_file($this->tempDefault.$file.'.php')) {
			return $this->tempDefault.$file.'.php';
		} else {
			return 'error';
		}
	}

	public function addVal($k, $v){
		$this->find[] = '{{'.$k.'}}';
		$this->rep[] = $v;
		return $this;
	}

	public function addValues($arr){
		if(is_array($arr)){
			foreach($arr as $k => $v){
				$this->addVal($k, $v);
			}
			return $this;
		} else {
			Error::e('Using addValues need to have an array.', 'fatal', true);
		}
	}

	// this way does not render php within the file.
	public function strFile($file){
		$contents = file_get_contents($this->loadTemp($file));
		if(isset($this->find) && is_array($this->find)){
			$contents = str_replace($this->find, $this->rep, $contents);
		}
		return $contents;
	}
}