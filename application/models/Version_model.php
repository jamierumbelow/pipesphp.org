<?php
/**
 * Pipes
 *
 * Sexy PHP package management
 *
 * @package pipes
 * @subpackage server
 * @author Jamie Rumbelow <http://jamierumbelow.net>
 * @copyright Copyright (c) 2010 Jamie Rumbelow
 * @license MIT License
 **/

class Version_model extends MY_Model {
	public $before_create = array('_new_version');
	
	protected function _new_version($version) {
		$version['downloads'] = 0;
		$version['created_at'] = date('Y-m-d H:i:s');
		
		return $version;
	}
}