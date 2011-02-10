<?php

class Migration_create_users_table extends Migration {
	public function up() {
		$this->dbforge->add_field(array(
			'id' 		=> array('type' => 'INT', 'auto_increment' => TRUE, 'unsigned' => TRUE),
			'email'		=> array('type' => 'VARCHAR', 'constraint' => 160),
			'password'	=> array('type' => 'VARCHAR', 'constraint' => 160),
			'salt'		=> array('type' => 'VARCHAR', 'constraint' => 160),
			'name'		=> array('type' => 'VARCHAR', 'constraint' => 250)
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users');
	}
	
	public function down() {
		$this->dbforge->drop_table('users');
	}
}