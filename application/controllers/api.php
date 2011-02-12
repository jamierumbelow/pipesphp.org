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
	
	/**
	 * GET /api/pipes
	 *
	 * Search through the list of pipes in the system
	 *
	 * Params:
	 * 		- search=some-search-term
	 *		- limit=5
	 *
	 * @author Jamie Rumbelow
	 */
	public function pipes_get() {
		// Setup the search
		$this->pipe->search($this->input->get('search'));
		
		// Limit?
		if ($this->input->get('limit')) {
			$this->db->limit((int)$this->input->get('limit'));
		}
		
		// Get the pipes
		$pipes = $this->pipe->get_all() ?: array();
		
		// Return the pipes
		$this->response($pipes);
	}
}