<?php
/*
	Look, I want to put everything into plugins..
		to do this I need to activate and deactivate the plugins..
	I may need to do more research on this..
	$plugins = array(	'Events', 'News', 'Pictures');
	include('classes/Plugable.php');
	$Plugable = new Plugable;
	$Plugable->loadPlugins($Sets['website']['plugins'], $_SERVER['DOCUMENT_ROOT'].'/pluggable/plugins/');

	echo '<pre>';
	print_r($Plugable->events);
	echo '</pre>';
	$Plugable->doHook('startup');
*/

namespace JW3B\core;

use JW3B\core\Config;

class Plugable {
	public static $events;

	public static $filter;
	public static $plugin;

	public static function loadPlugins($plugins) {
		$dir = Config::get('PluginDir');
		if (isset($plugins) && is_array($plugins)) {
			foreach ($plugins as $v) {
				if (is_file($dir . $v . '/config.json')) {
					self::$plugin[$v] = json_decode(file_get_contents($dir . $v . '/config.json'), 1);
				}
			}
		}
		return isset(self::$plugin) ? self::$plugin : '';
	}

	// this activates the hook..
	public static function addHook($event, $func) {
		self::$events[$event][] = $func;
	}

	public static function addHooks($hooks) {
		if (is_array($hooks)) {
			foreach ($hooks as $ev => $func) {
				self::addHook($ev, $func);
			}
		}
	}

	public static function removeHook($event, $func) {
		if (isset(self::$events[$event])) {
			if (in_array($func, self::$events[$event])) {
				$key = array_search($func, self::$events[$event]);
				if ($key !== false) {
					unset(self::$events[$event][$key]);
				}
			}
		}
	}

	public static function doHook() {
		//$num_args = func_num_args();
		$args = func_get_args();
		//if($num_args < 1)
		//	trigger_error("Insufficient arguments", E_USER_ERROR);
		//Hook name should always be first argument
		$hook_name = array_shift($args);
		// this does whatever hooks are attached to the $event
		if (isset(self::$events[$hook_name])) {
			foreach (self::$events[$hook_name] as $k => $v) {
				if (function_exists($v)) {
					//call_user_func($v);
					return $v($args);
				} else {
					throw new \Exception($v . ' function does not exist in plugable class for ' . $hook_name, 'plugable');
					//Error::e($v.' function does not exist in plugable class for '.$hook_name, 'plugable', true);
				}
			}
		} else {
			return;
		}
	}

	// this activates the hook..
	public static function addFilter($event, $array) {
		self::$filter[$event][] = $array;
	}

	public static function doFilter($event, $func) {
		if (isset(self::$filter[$event]) && function_exists($func)) {
			return $func(self::$filter[$event]);
		} else {
			return '';
		}
	}
}