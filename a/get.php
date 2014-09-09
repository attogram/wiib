<?php
// WIIB Version 0.6.0
// Action - Get Image(s)

$class = '../wiib.php'; 
if(!file_exists($class)||!is_readable($class)){ print 'Site down for maintenance'; exit; } require_once($class);

//if( !isset($_GET['g']) ) { print "Error get"; exit; }	
if( !isset($_GET['port']) ) { $_GET['port'] = '0'; }
$wiib = new wiib( $debug = 0, $title = 'Get Image');

if( !$wiib->is_number($_GET['port']) ) { print "Error portfolio number"; exit; }	

$wiib->portfolio = $_GET['port']; 

switch( $_GET['g'] ) { 

	case 's': // search
		if( !isset($_GET['s']) || !$_GET['s'] ) { print "Error no search term"; exit; } 
		$z = $wiib->get_api_images_by_search($_GET['s'], 50);
		break;

	case 'r': // random
		$z = $wiib->get_random_images_from_api(10);
		break;

	case 'c': // category
		show_cat_form();
		exit;

	case 'cc': // do category
		if( !isset($_GET['c']) || !$_GET['c'] ) { print "Error category name"; exit; } 
		$z = $wiib->get_images_from_category( $_GET['c'] );
		break;

	default: // error
		break;
}

$url = $wiib->url('home');
if( isset($_GET['r']) && $_GET['r'] ) { 
	$url = $wiib->url($_GET['r']);
}

if( $wiib->debug ) { 
	$wiib->debug('END OF SCRIPT. REDIRECT: <a href="' . $url . '">'. $url . '</a>');
} else {
	header("Location: $url"); 
}
exit;



function show_cat_form() {
	global $wiib;
	print '
<form>
<input type="hidden" name="g" value="cc" />
<input type="hidden" name="port" value="' . $wiib->portfolio .  '" />
Category:<input id="c" name="c" type="text" size="50" value="Category:"/>
<input type="submit" value="   Get images from category    " />
</form>
	';
} // end show_cat_form
