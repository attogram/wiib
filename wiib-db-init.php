<?php
// WIIB - Version 0.6.0
// DATABASE INIT

$d = dirname(__FILE__);
$database_name = $d . '/admin/imgdb/images.sqlite';

class wiib_db_init extends wiib_web {

	var $db;
	var $database_name;

        //////////////////////////////////////////////////////////
	function __construct() {
                parent::__construct();
		//$this->debug('wiib_db_init:__construct()');

		global $database_name;
		$this->database_name = $database_name;
		$this->debug('wiib_db_init:__construct: db: ' . $this->database_name);
		$this->db = $this->init_database();

		if( !$this->db ) { $this->fail('error opening database'); exit; }	
	}
	
        //////////////////////////////////////////////////////////
	function query_as_array( $sql, $bind=array() ) {
		$this->debug("wiib_db_init:query_as_array() sql: $sql #bind:" . sizeof($bind));
		if( !$this->db ) { $this->error('wiib_db_init:query_as_array(): ERROR: no db'); return array(); }
		$statement = $this->db->prepare($sql);
                if( !$statement ) {
                        $this->error('wiib_db_init:query_as_array(): ERROR PREPARE: '.print_r($this->db->errorInfo(),1));
                        return array();
                }
		while( $x = each($bind) ) {
			$this->debug('wiib_db_init:query_as_array(): bindParam '. $x[0] .' = ' . $x[1]);
			$statement->bindParam( $x[0], $x[1]);
		}	
                if( !$statement->execute() ) {
                        $this->error('wiib_db_init:query_as_array(): ERROR EXECUTE: '. $sql . ' == '.print_r($this->db->errorInfo(),1));
                        return array();
                }
                $r = $statement->fetchAll(PDO::FETCH_ASSOC);
		if( !$r && $this->db->errorCode() != '00000') { 
                        $this->error('wiib_db_init:query_as_array(): ERROR FETCH: '.print_r($this->db->errorInfo(),1));
                        $r = array();
		}
		$this->debug('wiib_db_init:query_as_array(): OK ' . count($r) );
		return $r;
	}

        //////////////////////////////////////////////////////////
	function query_as_bool( $sql, $bind=array() ) {
		$this->debug("wiib_db_init:query_as_bool() sql: $sql #bind:" . sizeof($bind));
		if( !$this->db ) { $this->error('wiib_db_init:query_as_bool(): ERROR: no db'); return FALSE; }
		$statement = $this->db->prepare($sql);
                if( !$statement ) {
                        $this->error('wiib_db_init:query_as_bool(): ERROR PREPARE: '. print_r($this->db->errorInfo(),1));
                        return FALSE;
                }
		while( $x = each($bind) ) {
			$this->debug('wiib_db_init:query_as_bool(): bindParam '. $x[0] .' = ' . $x[1]);
			$statement->bindParam( $x[0], $x[1]);
		}	
                if( !$statement->execute() ) {
                        $this->error('wiib_db_init:query_as_array(): ERROR EXECUTE: '. $sql . ' == '.print_r($this->db->errorInfo(),1));
                        return FALSE;
                }
		$this->debug('wiib_db_init:query_as_bool(): OK');
		return TRUE;
	}

        //////////////////////////////////////////////////////////
	function init_database() {
		$this->debug('wiib_db_init:init_database()');
		if( !in_array('sqlite', PDO::getAvailableDrivers() ) ) {
			$this->error('wiib_db_init:init_database: ERROR: no sqlite Driver');
			return FALSE;
		}
		try {
			return new PDO('sqlite:'. $this->database_name);
		} catch(PDOException $e) {
			$this->error('wiib_db_init:init_database: ERROR: '. $e->getMessage());
			return FALSE;
		}
	}

	//////////////////////////////////////////////////////////
	function create_tables() {
		$this->debug('wiib_db_init:create_database()');
		if( !file_exists($this->database_name) ) { 
			if( !@touch($this->database_name) ) { 
				$this->error('wiib_db_init:create_database(): ERROR: can not touch database name: '
					.$this->database_name);
				return FALSE; 
			} 
		} 
		$r = false;

		$sql = "CREATE TABLE IF NOT EXISTS 'images' (
		'pageid' INTEGER NOT NULL, 
		'votes_for' INTEGER NOT NULL DEFAULT 0, 
		'votes_against' INTEGER NOT NULL DEFAULT 0, 
		'last_seen' TEXT,
		'portfolio' INTEGER NOT NULL DEFAULT 0,
		'sha1' TEXT,
		'timestamp' TEXT,
		'title' TEXT, 'user' TEXT, 'descriptionurl' TEXT, 'mime' TEXT,
		'thumburl' TEXT, 'thumbwidth' INTEGER, 'thumbheight' INTEGER, 
		'url' TEXT, 'height' INTEGER, 'width' INTEGER,
		PRIMARY KEY (pageid)
		)";
		$r = $this->query_as_bool($sql);

		$sql = "CREATE TABLE IF NOT EXISTS 'categories' ( 
		'pageid' INTEGER NOT NULL DEFAULT 0, 
		'name' TEXT NOT NULL,  
		PRIMARY KEY (pageid,name) 
		)";
		$r .= $this->query_as_bool($sql);

		$sql = "CREATE TABLE IF NOT EXISTS 'img2cat' ( 
		'img' INTEGER NOT NULL DEFAULT 0, 
		'cat' TEXT NOT NULL, 
		PRIMARY KEY (img,cat) 
		)";
		$r .= $this->query_as_bool($sql);

		$sql = "CREATE TABLE IF NOT EXISTS 'user' ( 
		'ip' TEXT NOT NULL,
		'last_seen' TEXT,
		'hits' INTEGER,
		'last_url' TEXT,
		'last_ref_url' TEXT, 
		'last_user_agent' TEXT,
		PRIMARY KEY (ip)
		)";
		$r .= $this->query_as_bool($sql);


		return $r;
	}

	//////////////////////////////////////////////////////////
	function drop_tables() { 
		$this->debug("drop_images()");
		$r  = $this->query_as_bool('DROP TABLE images');
		$r .= $this->query_as_bool('DROP TABLE categories');
		$r .= $this->query_as_bool('DROP TABLE img2cat');
		$r .= $this->query_as_bool('DROP TABLE user');
		return $r;
	}


} // END class wiib_db_init

