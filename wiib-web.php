<?php
// WIIB
// WEB 

class wiib_web extends wiib_init {

	var $host;
	var $links;
	var $portfolio;
	
	//////////////////////////////////////////////////////////
	function __construct() {
		parent::__construct();
		//$this->debug('wiib_web:__construct()');

		$this->host = 'http://localhost';  // without trailing slash

		$h = $this->host . '/wiib/';
		
		$this->links = array(
			'css'     => $h . 'css.css',
			'home'    => $h . '',
			'compare' => $h . 'compare/',
			'list'    => $h . 'list/',
			'tools'   => $h . 'tools/', 
			'import'  => $h . 'import/',
			'export'  => $h . 'export/',
			'info'    => $h . 'info/',
			'for'     => $h . 'a/for.php',
			'promote' => $h . 'a/promote.php',
			'against' => $h . 'a/against.php',
			'delete'  => $h . 'a/delete.php',
			'get'     => $h . 'a/get.php',
			'admin'   => $h . 'admin',
		);

                $this->portfolio = 0;
                if( isset($_GET['port']) && $this->is_number($_GET['port']) ) {
                        $this->portfolio = $_GET['port'];
                        $this->debug('wiib_web:__construct(): portfolio:'. $this->portfolio);
                } else if( isset($_GET['port']) && !$this->is_number($_GET['port']) ) {
			$this->fail('Bad portfolio ID');
		}


	}

	//////////////////////////////////////////////////////////
	function url( $link='home' ) {
		if( !isset($this->links[$link]) ) { 
			$this->error("wiib_web:url: ERROR: Bad link: $link");
			return $this->links['home'];
		} 
		return $this->links[$link] . $this->portfolio_urlvar(0);
	} 

	//////////////////////////////////////////////////////////
	function portfolio_urlvar($multi=TRUE) {
		if( !$this->portfolio || $this->portfolio == '0' ) { return; }
		if( $multi ) { $r = '&'; } else { $r = '?'; } 
		return $r . 'port=' . $this->portfolio;
	}

	//////////////////////////////////////////////////////////
	function selected($a,$b) {
		if( $a != $b ) { return; } 
		return ' selected="selected" ';
	}


} // END class wiib_web

