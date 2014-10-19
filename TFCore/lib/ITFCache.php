<?php
/**
 * Description of ITFCache
 * @author Animax
 */
interface ITFCache {
	public function get($name, $default=FALSE);
	public function set($name, $value, $life_time=null);
	public function del($name);
	public function clear();
}