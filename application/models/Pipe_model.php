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
	public $before_create = array('_new_pipe');
	
	public function search($term) {
		$this->db->like('name', $term);
		$this->db->or_like('description', $term);
		
		return $this;
	}
	
	public function user($id) {
		$this->db->where('user_id', $id);
		
		return $this;
	}
	
	protected function _new_pipe($pipe) {
		$pipe['slug'] = url_title($pipe['name']);
		$pipe['downloads'] = 0;
		$pipe['created_at'] = $pipe['updated_at'] = date('Y-m-d H:i:s');
		
		return $pipe;
	}
}