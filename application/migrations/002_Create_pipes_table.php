<?php

class Migration_create_pipes_table extends Migration {
	public function up() {
		$this->dbforge->add_field(array(
			'id' 			=> array('type' => 'INT', 'auto_increment' => TRUE, 'unsigned' => TRUE),
			'user_id'		=> array('type' => 'INT'),
			'name'			=> array('type' => 'VARCHAR', 'constraint' => 160),
			'slug'			=> array('type' => 'VARCHAR', 'constraint' => 160),
			'description'	=> array('type' => 'TEXT'),
			'downloads'		=> array('type' => 'INT'),
			'created_at'	=> array('type' => 'DATETIME'),
			'updated_at'	=> array('type' => 'DATETIME')
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('pipes');
		
		$this->dbforge->add_field(array(
			'pipe_id' 		=> array('type' => 'INT'),
			'version'		=> array('type' => 'VARCHAR', 'constraint' => 50),
			'pipespec'		=> array('type' => 'TEXT'),
			'downloads'		=> array('type' => 'INT'),
			'created_at'	=> array('type' => 'DATETIME')
		));
		$this->dbforge->create_table('versions');
	}
	
	public function down() {
		$this->dbforge->drop_table('pipes');
		$this->dbforge->drop_table('versions');
	}
}