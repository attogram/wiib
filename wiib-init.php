<?php
// WIIB
// INIT

class wiib_init {

	var $debug;
	var $admin;
	
	//////////////////////////////////////////////////////////
	function __construct() {
		if(isset($_GET['debug'])) { $this->debug = true; } 
		//$this->debug('wiib_init:__construct()');
		set_time_limit(2); // 2 second timeout

                $this->admin = false;
                $god_array = array(
                        '127.0.0.1',
						'::1',
                );
                if( in_array( $_SERVER['REMOTE_ADDR'], $god_array ) ) {
                        $this->admin = true;
                        $this->debug('wiib_init:__construct: ADMIN ' . $_SERVER['REMOTE_ADDR']);
                }
	}

	//////////////////////////////////////////////////////////
	function debug($m='') {
		if( !$this->debug ) { return; }
		print '<pre style="margin:0px;padding:0px;" class="debug">DEBUG: '. print_r($m,1) .'</pre>';
	}

	//////////////////////////////////////////////////////////
	function error($m='') {
		//if( !$this->debug ) { return; }
		print '<pre style="margin:0px;padding:0px;background-color:yellow;color:black;" class="debug">ERROR: '
			. print_r($m,1) .'</pre>';
	}
	//////////////////////////////////////////////////////////
	function is_number($n='') { 
                if ( preg_match('/^-?[0-9]*$/', $n )) { return TRUE; }
		return FALSE;
	}

	//////////////////////////////////////////////////////////
	function is_positive_number($n='') { 
                if ( preg_match('/^[0-9]*$/', $n )) { return TRUE; }
		return FALSE;
	}

	//////////////////////////////////////////////////////////
	function pretty_title($title='') { 
		if( !$title ) { return FALSE; }
		$title = str_replace('File:','',$title);
		$x = explode('.',$title);
		$end = array_pop($x);
		$title = implode('.',$x);
		return $title;
	}

	//////////////////////////////////////////////////////////
	function fail($m='') {
		print '<PRE><a href="' .  $this->url('home') . '">Which image is better .. or worse?</a><BR />';
		print '<BR />SYSTEM FAILURE @ ' . date('r') . ' GMT:<BR /><BR />';
		if( $m ) { print_r($m); } else { print 'guru meditation error'; } 
		exit;
	}

	//////////////////////////////////////////////////////////
	function get_resized_height( $old_width, $old_height, $new_width ) {
		$this->debug("wiib_init:get_resized_height($old_width, $old_height, $new_width )");
		if( !$old_width ) { $this->error('wiib_init:get_resized_height: ERROR: old_width'); return FALSE; } 
		if( !$old_height) { $this->error('wiib_init:get_resized_height: ERROR: old_height'); return FALSE; } 
		if( !$new_width ) { $this->error('wiib_init:get_resized_height: ERROR: new_width'); return FALSE; } 
		if( $old_height <= 0 ) { $this->error('wiib_init:get_resized_height: ERROR: divide by 0');return FALSE;} 
		$ratio = $old_width / $old_height;
		return round($new_width / $ratio);
	}

} // END class wiib_init

