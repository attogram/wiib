<?php
// WIIB 
// Version 0.6.1

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('__WIIB__',1);

$d = dirname(__FILE__);
require_once($d . '/wiib-init.php' );	// class wiib_init
require_once($d . '/wiib-web.php' );	// class wiib_web 	extends wiib_init
require_once($d . '/wiib-db-init.php' );// class wiib_db_init 	extends wiib_web
require_once($d . '/wiib-db.php' );	// class wiib_db 	extends wiib_db_init
require_once($d . '/wiib-user.php' );	// class wiib_user	extends wiib_db
require_once($d . '/wiib-api.php' );	// class wiib_api 	extends wiib_user

class wiib extends wiib_api {

	var $images;
	
	//////////////////////////////////////////////////////////
	function __construct( $debug, $title='' ) {
		$this->debug = $debug;
		$this->title = $title;
		parent::__construct();
		$this->debug("wiib:__construct() - title: " . $this->title);
	}

	//////////////////////////////////////////////////////////
	function display_image_mini($image='', $size=100) {
		if( !$image || !is_array($image) ) { 
			$this->error('wiib:display_image_mini: ERROR: no image array');
			return FALSE;
		} 
		if( !isset($image['width']) || !$image['width'] ) { 
			$this->error('wiib:display_image_mini: ERROR: no image width');
		} 
		if( !isset($image['height']) || !$image['height'] ) { 
			$this->error('wiib:display_image_mini: ERROR: no image height');
		} 
		if( !isset($image['url']) || !$image['url'] ) { 
			$this->error('wiib:display_image_mini: ERROR: no image url');
		} 


		if( $image['width'] < 100 ) { 
			$w = $image['width'];
			$h = $image['height'];
			$mini_url = $image['url'];
		} else {
			$w = $size;
			$h = $this->get_resized_height( $image['width'], $image['height'], $size);
        		$x = explode('/', $image['thumburl']);
			$split = 'px-';
			//if( $image['mime'] == 'image/tiff' ) { $pre- split = 'lossy-page1-'; }  
				// lossy-page1-300px'- //
			$xx = explode($split, $x[count($x)-1]);
			$xx[0] = $size;
			$x[count($x)-1] = implode($split, $xx);
			$mini_url = implode('/', $x);
		}  

		return ''
		. '<div style="display:inline-block;vertical-align:top;border:0px solid black;text-align:center;margin:10px;font-size:14pt;">'
		//. '<a href="" style="color:#bbb;background-color:#333;text-decoration:none;"> +<sup>' 
		//. $image['votes_for'] . '</sup></a>'
		//. '&nbsp;&nbsp;&nbsp;'
		//. '<a href="" style="color:#bbb;background-color:#333;text-decoration:none;"> -<sup>' 
		//. $image['votes_against'] . '</sup></a>'
		//. '<br />'
		. '<a href="/image/info/?i=' . $image['pageid'] . '">'
		. '<img src="' . $mini_url . '" width="' . $w . '" height="' . $h. '"' 
			. ' style="border:0px;"'
			. ' alt="' . htmlspecialchars($this->pretty_title($image['title'])) . '"' 
			. ' title="'
			. 'title: ' . htmlspecialchars($this->pretty_title($image['title'])) . "\n"
			. 'pageid: ' . $image['pageid'] . "\n"
			. 'timestamp: ' . $image['timestamp'] . "\n"
			. 'last updated: ' . $image['last_seen'] . "\n"
			. 'mime: '. $image['mime'] . "\n"
			. 'vote: +' . $image['votes_for'] .  ' -' . $image['votes_against'] 
			. ' as of ' . $image['last_seen'] . "\n"
			. "\n"
			. '"'
		. '/>'
		. '</a>'
		. '</div>'
		;
	}

	//////////////////////////////////////////////////////////
	function display_image($image='', $return='compare' ) {
		if( !$image || !is_array($image) ) { 
			$this->error('display_image: ERROR: no image array');
			return FALSE;
		}		

		$title = htmlspecialchars($this->pretty_title($image['title']));
        	$height = $image['thumbheight'];
        	$width = $image['thumbwidth'];
        	$url = $image['thumburl'];

		return  ''
		. '<div class="pm">' 
		. $this->for_button($image, $return) . '&nbsp;' . '&nbsp;' . $this->against_button($image, $return)
		. '</div>'
		. '<div style="height:100%;width:100%;" class="pick">'
		. '<a href="/image/info/?i=' . $image['pageid'] . '">'
		. '<img src="'. $url .'" height="'. $height .'" width="'. $width 
		. '" alt="" title="'. $title .'">'
		. '</a>'
		. ( $this->admin 
			? '<br /><div class="admin">ADMIN: <a href="/image/a/delete.php?x=' . $image['pageid'] 
				. '&r=compare&port=' . $this->portfolio
				. '">Delete Image ' 
				. $image['pageid'] . '</a></div>'
			: '' ) 
		. '</div>'
		;
	}

	//////////////////////////////////////////////////////////
	function for_button($image, $return='compare') {
		if( !is_array($image) ) { return '?'; } 
                if( ($image['portfolio'] < 0)
 		|| ( ($image['portfolio'] == 0) && ($image['votes_for'] >= 10) )
                || ( ($image['portfolio'] == 1) && ($image['votes_for'] >= 20) )
                || ( ($image['portfolio'] == 2) && ($image['votes_for'] >= 30) )
                || ( ($image['portfolio'] == 3) && ($image['votes_for'] >= 40) )
                || ( ($image['portfolio'] >= 4) && ($image['votes_for'] >= 100) )
                ) {
			$link = '/image/a/promote.php?p=' . $image['pageid'] . $this->portfolio_urlvar(1);
			$link .= '&r=' . $return;
			$button_text = '^'; $color = 'lightgreen';
		} else {
			$link = '/image/a/for.php?b=' . $image['pageid'] . $this->portfolio_urlvar(1);
			$link .= '&r=' . $return;
			$button_text = '+'; $color = 'lightgreen';
		}

		return '<a href="' . $link . '" style="color:' . $color . '">&nbsp;' . $button_text 
		. '<span style="font-size:15px;vertical-align:super;">' . $image['votes_for'] . '</span>'
		. '&nbsp;</a>' 
		;
	}

	//////////////////////////////////////////////////////////
	function against_button($image, $return='compare') {
		if( !is_array($image) ) { return '?'; } 
		if( ($image['portfolio'] < 0) 
		  || ( ($image['portfolio'] == 0) && ($image['votes_against'] >= 10) )
                  || ( ($image['portfolio'] == 1) && ($image['votes_against'] >= 10) )
                  || ( ($image['portfolio'] == 2) && ($image['votes_against'] >= 10) )
                  || ( ($image['portfolio'] == 3) && ($image['votes_against'] >= 10) )
                  || ( ($image['portfolio'] >= 4) && ($image['votes_against'] >= 100 ) )
                ) {
			$link = '/image/a/delete.php?x='. $image['pageid'] . $this->portfolio_urlvar(1);
			$link .= '&r=' . $return;
			$button_text = 'x'; $color = 'salmon';
		} else {
			$link = '/image/a/against.php?d='. $image['pageid'] . $this->portfolio_urlvar(1);
			$link .= '&r=' . $return;
			$button_text = '-'; $color = 'salmon';
		}

                return '<a href="' . $link . '" style="color:' . $color . '">&nbsp;' . $button_text
                . '<span style="font-size:15px;vertical-align:super;">' . $image['votes_against'] . '</span>'
                . '&nbsp;</a>'
                ;
	}

	//////////////////////////////////////////////////////////
	function portfolio_select() {

		$pl = $this->get_portfolio_list();
		if( !$pl ) { $this->portfolio_list = array(); } 
		reset($this->portfolio_list);

		$opts = '';
		$here = array();

		while( $x = each($this->portfolio_list)) {
			$port = $x['value']['portfolio'];
			$count  = $x['value']['count'];
			$votes_for = $x['value']['votes_for'];
			$votes_against = $x['value']['votes_against'];
			$selected = ''; if( $this->portfolio == $port ) { $selected = ' selected="selected" '; } 
			$here[$port] = true;
			$opts .= '<option value="' . $wiib->url('portfolio')  . $port . '"' . $selected . '>';
			switch( $port ) { 
				case '-1': $opts_b = "Trash ($count images)"; break;
				default: 
					$opts_b = "Portfolio $port ($count images)"
						. " +$votes_for -$votes_against"; 
					break;
			} 
			$opts .= $opts_b . '</option>';
		} 
		if( !isset($here[-1]) ) { $opts = '<option value="' . $this->url('portfolio') . '-1"'
			. $this->selected($this->portfolio,'-1') . '>Trash (0 images)</option>' . $opts; }
		if( !isset($here[0]) ) { $opts = '<option value="' . $this->url('portfolio') . '0"' 
			. $this->selected($this->portfolio,'0') . '>Portfolio 0 (0 images)</option>' . $opts; }
		if( !isset($here[1]) ) { $opts .= '<option value="' . $this->url('portfolio') . '1"' 
			. $this->selected($this->portfolio,'1') . '>Portfolio 1 (0 images)</option>'; }
		if( !isset($here[2]) ) { $opts .= '<option value="' . $this->url('portfolio') . '2"' 
			. $this->selected($this->portfolio,'2') . '>Portfolio 2 (0 images)</option>'; }
		if( !isset($here[3]) ) { $opts .= '<option value="' . $this->url('portfolio') . '3"'   
			. $this->selected($this->portfolio,'3') . '>Portfolio 3 (0 images)</option>'; }
		return '' 
		. '<select onchange="if (this.value) window.location.href=this.value;">' 
		. '<option value="' . $this->url('portfolio') . '">Select a Portfolio</option>'
		. $opts . '</select>'
		;
	} 

} // END class wiib 

