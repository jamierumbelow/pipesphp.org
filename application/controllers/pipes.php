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

class Pipes extends MY_Controller {
	public $data = array();
	
	public function index() {
		// Setup some vars
		$this->data['pipes'] = array();
		$this->data['search'] = $this->input->get('search');
	}
}