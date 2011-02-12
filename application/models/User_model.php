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

class User_model extends MY_Model {
	
	/**
	 * Basic basic email/password authentication
	 *
	 * @return boolean/object
	 * @author Jamie Rumbelow
	 */
	public function authenticate($email, $password) {
		if ($user = $this->get_by('email', $email)) {
			if (sha1($password . $user->salt) == $user->password) {
				return $user;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
}