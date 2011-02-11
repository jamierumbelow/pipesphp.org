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
	public function __construct() {
		parent::__construct();
		
		$this->load->model('pipe_model', 'pipe');
	}
	
	public function pipe_get() {
		// Setup the search
		$this->pipe->search($this->input->get('search'));
		
		// Get the pipes
		$pipes = $this->pipe->get_all() ?: array();
		
		// Return the pipes
		$this->response($pipes);
	}
}