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

class Pipe_model extends MY_Model {
	public function search($term) {
		$this->db->like('name', $term);
		$this->db->or_like('description', $term);
		
		return $this;
	}
}