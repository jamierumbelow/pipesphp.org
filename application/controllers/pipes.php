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

class Pipes extends CI_Controller {
	public $data = array();
	
	public function index() {
		// Setup some vars
		$this->data['pipes'] = array();
		$this->data['search'] = $this->input->get('search');
		
		// Respond to both JSON and HTML
		if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] == 'application/json') {
			$this->output->set_header('Content-Type: application/json');
			$this->load->view('pipes/index.json.php', $this->data);
		} else {
			$this->load->view('pipes/index.html.php', $this->data);
		}
	}
}