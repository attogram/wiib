<?php
// WIIB
// USER 

class wiib_user extends wiib_db {

	var $user_hits;

	//////////////////////////////////////////////////////////
	function __construct() {
		parent::__construct();

		//$this->debug('wiib_user:__construct()');
		$u = $this->user_hit();
	}

	//////////////////////////////////////////////////////////
	function user_hit() {
		$this->debug('wiib_user:user_hit()');


                $sql = 'SELECT hits FROM user WHERE ip = :ip';
                $x = $this->query_as_array( $sql, $bind = array(
                        ':ip' => @$_SERVER['REMOTE_ADDR']
                ) );
		if( !isset($x[0]['hits']) || !$this->is_positive_number($x[0]['hits']) ) {
			$this->user_hits = 1;
		} else {
			$this->user_hits = $x[0]['hits'] + 1;
		} 


		$sql = 'INSERT OR REPLACE INTO user ( ip, last_seen, last_url, last_ref_url, last_user_agent, hits
			) VALUES ( :ip, DATETIME("now"), :last_url, :last_ref_url, :last_user_agent, :hits )';
		$r = $this->query_as_bool( $sql, $bind = array( 
			':ip' => @$_SERVER['REMOTE_ADDR'],
			':last_url' => @$_SERVER['REQUEST_URI'],
			':last_ref_url' => @$_SERVER['HTTP_REFERER'],
			':last_user_agent' => @$_SERVER['HTTP_USER_AGENT'],
			':hits' => $this->user_hits
		) );
		if( !$r ) { 
			$this->error('wiib_user:user_hit: ERROR updating user');
			return FALSE;
		} 	
		

		return TRUE;
	}

} // END class wiib_user

