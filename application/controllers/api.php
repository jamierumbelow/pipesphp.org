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

class Api extends REST_Controller {
	public function pipe_get() {
		// Setup some variables
		$this->data['pipes'] = array();
		$this->data['search'] = $this->input->get('search');
	}
}