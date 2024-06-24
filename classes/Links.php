<?php

namespace JW3B\core;
use JW3B\core\Config;

class Links {
	public $Links;
	public $active_class;
	public $active_goes_on;

	/**
	 * __construct function
	 * @param array $links
	 * @param array $ary  = [
	 *		'active_class' => 'active',
	 *		'active_goes_on' => 'a'
	 *		'links' => ['/path' => 'Path']
	 *	]
	 *
	 */
	public function __construct($links=[], $ary=[]){
		$def = [
			'active_class' => 'active',
			'active_goes_on' => 'a'
		];
		if(!isset($links) || !is_array($links)){
			throw new \error('links is a required array');
		}
		$this->Links = $links;
		$this->active_class = isset($ary['active_class']) ? $ary['active_class'] : $def['active_class'];
		$this->active_goes_on = isset($ary['active_goes_on']) ? $ary['active_goes_on'] : $def['active_goes_on'];
	}

	/**
	 * display function
	 *
	 * @param string $wrap_in='li'
	 * @param array $ary = [
	 * 		'li' => [ // the value of $wrap_in if found
	 * 			'class' => '',
	 * 			'attr' => ' some="val" // strings
	 * 		],
	 * 		'a' => [
	 * 			'class' => '',
	 * 			'attr' => ' some="val"' // strings
	 * 		]
	 * ]
	 *
	 * @return string
	 */
	public function display($wrap_in='li', $ary=[]){
		// it just displays the links inside the ul
		$ret = '';
		if(isset($this->Links)){
			foreach($this->Links as $k => $v){
				$class = '';
				$attr = '';
				$after_link = '';
				$acls = '';
				$aattr = '';
				$_SERVER['REQUEST_URI'] = 'home';
				$add = $_SERVER['REQUEST_URI'] == str_replace('/', '', $k) ? $this->active_class : '';
				if($wrap_in != ''){
					if(isset($ary[$wrap_in])){
						$class .= $this->active_goes_on == $wrap_in ? $add : '';
						if(isset($ary[$wrap_in]['class'])){
							$class .= $class == '' ? $ary[$wrap_in]['class'] : ' '.$ary[$wrap_in]['class'];
						}
						if(isset($ary[$wrap_in]['attr'])){
							$attr .= ' '.$ary[$wrap_in]['attr'];
						}
					}
					$ret .= '<'.$wrap_in.' class="'.$class.'"'.$attr.'>';
					$after_link = '</'.$wrap_in.'>';
				}
				$acls .= $this->active_goes_on == 'a' ? $add : '';
				if(isset($ary['a'])){
					if(isset($ary['a']['class'])){
						$acls .= $acls == '' ? $ary['a']['class'] : ' '.$ary['a']['class'];
					}
					if(isset($ary['a']['attr'])){
						$aattr .= ' '.$ary['a']['attr'];
					}
				}
				$acls = $acls == '' ? '' : ' class="'.$acls.'"';
				$ret .= '<a href="'.$k.'"'.$acls.$aattr.'>'.$v.'</a>';
				$ret .= $after_link;
			}
		}
		return $ret;
	}
}