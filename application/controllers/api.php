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

// Load Pipes
require_once '/Users/jamierumbelow/Sites/Os/pipes/pipes.php';

class Api extends REST_Controller {
	public function __construct() {
		parent::__construct();
		
		$this->load->model('pipe_model', 'pipe');
		$this->load->model('version_model', 'version');
		$this->load->model('user_model', 'user');
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

	/**
	 * POST /api/pipes
	 *
	 * Submit a new pipe to the system
	 *
	 * @author Jamie Rumbelow
	 */
	public function pipes_post() {
		// Get the POST vars for this request
		$pipe = (isset($_FILES['pipe'])) ? $_FILES['pipe']['tmp_name'] : FALSE;
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		
		// First of all, authenticate!
		if (!$user = $this->user->authenticate($email, $password)) {
			$this->response(array('success' => FALSE, 'message' => 'A valid email and password are required to push pipes!'), 401);
			exit;
		}
		
		// Secondly, do we have a pipe at all?
		if (!$pipe) {
			$this->response(array('success' => FALSE, 'message' => 'A valid pipe package is required'), 401);
			exit;
		}
		
		// Decode the package
		$pipe = Pipes_Package::decode_from_file($pipe);
		
		// Make sure the pipe's name is valid
		if (preg_match("/[^\d\w\-\.]/", $pipe->name)) {
			$this->response(array('success' => FALSE, 'message' => 'Pipe names can only include letters, numbers, dashes, and underscores'), 400);
			exit;
		}
		
		// Do we have this pipe already?
		if (!$pipedb = $this->pipe->get_by('name', $pipe->name)) {
			// Create it in the DB
			$id = $this->pipe->insert(array(
				'user_id'		=> $user->id,
				'name' 			=> $pipe->name,
				'description'	=> $pipe->description
			));
		} else {
			// Make sure it's this user's
			if ($this->pipe->user($user->id)->get_by('name', $pipe->name)) {
				// The description could have been updated
				if ($pipedb->description !== $pipe->description) {
					$this->pipe->update($pipedb->id, array(
						'description' => $pipe->description
					));
				}
			} else {
				$this->response(array('success' => FALSE, 'message' => 'This pipe is owned by somebody else! Please use a different name.'), 401);
				exit;
			}
			
			$id = $pipedb->id;
		}
		
		// Do we have this version?
		if (!$this->version->get_by(array('version' => $pipe->version, 'pipe_id' => $id))) {
			// Insert this version
			$this->version->insert(array(
				'pipe_id' => $id,
				'version' => $pipe->version,
				'pipespec' => json_encode($pipe)
			));
		}
		
		// Return a boolean, brother
		$this->response(array('success' => TRUE, 'message' => 'Successfully released pipe! Try downloading it with `sudo pipes install '.$pipe->name.'`'), 201);
	}
}