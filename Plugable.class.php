<?php
/*
	Look, I want to put everything into plugins..
		to do this I need to activate and deactivate the plugins..
	I may need to do more research on this..
	$Sets['website']['plugins'] = array(	'Events', 'News', 'Pictures');
	include('classes/Plugable.php');
	$Sets['core']['Plugable'] = new Plugable;
	$Sets['core']['Plugable']->loadPlugins($Sets['website']['plugins'], $_SERVER['DOCUMENT_ROOT'].'/pluggable/plugins/');

	echo '<pre>';
	print_r($Sets['core']['Plugable']->events);
	echo '</pre>';
	$Sets['core']['Plugable']->doHook('startup');
*/

namespace JW3B\core\Plugable;

class Plugable {
	public $events = Plugable::events;

	public $filter = Plugable::filter;

	public static function loadPlugins($plugins, $dir){
		if(isset($plugins) && is_array($plugins)){
			foreach($plugins as $v){
				if(is_file($dir.$v.'/config.json')){
					$plugin[$v] = json_decode(file_get_contents($dir.$v.'/config.json'), 1);
				}
			}
		}
		return isset($plugin) ? $plugin : '';
	}

	// this activates the hook..
	public static function addHook($event, $func){
		self::$events[$event][] = $func;
	}

	public static function addHooks($hooks){
		if(is_array($hooks)){
			foreach($hooks as $ev => $func){
				self::addHook($ev, $func);
			}
		}
	}

	public static function removeHook($event, $func){
		if(isset(Plugable::$events[$event])){
			if(in_array($func, Plugable::$events[$event])){
				$key = array_search($func,Plugable::$events[$event]);
				if($key!==false){
					unset(Plugable::$events[$event][$key]);
				}
			}
		}
	}

	public static function doHook(){
		//$num_args = func_num_args();
		$args = func_get_args();
		//if($num_args < 1)
		//	trigger_error("Insufficient arguments", E_USER_ERROR);
		//Hook name should always be first argument
		$hook_name = array_shift($args);
		// this does whatever hooks are attached to the $event
		if(isset(Plugable::$events[$hook_name])){
			foreach(Plugable::$events[$hook_name] as $k => $v){
				if(function_exists($v)){
					//call_user_func($v);
					return $v($args);
				} else {
					die($v.' function does not exist');
				}
			}
		} else {
			return;
		}
	}

	// this activates the hook..
	public static function addFilter($event, $array){
		Plugable::$filter[$event][] = $array;
	}

	public static function doFilter($event, $func){
		if(isset(Plugable::$filter[$event]) && function_exists($func)){
			return $func(Plugable::$filter[$event]);
		} else {
			return '';
		}
	}
}
