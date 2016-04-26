<?php
// WIIB
// Action - Delete Image

$class = '../wiib.php'; 
if(!file_exists($class)||!is_readable($class)){ print 'Site down for maintenance'; exit; } require_once($class);
$wiib = new wiib( $debug = 0, $title = 'Delete Image');

$url = $wiib->url('home'); 
if( isset($_GET['r']) && $_GET['r'] ) { $url = $wiib->url($_GET['r']); } 

if( !isset($_GET['x']) || !$wiib->is_number($_GET['x']) ) {
	//print "Error id"; exit;
	header("Location: $url"); exit;
}	

$i = $wiib->get_image_from_db($_GET['x']);
$image = @$i[0];

if( !$image ) { 
	//print "Error image"; exit;
	header("Location: $url"); exit;
}

if( $image['portfolio'] < 0 ) { 
	$d = $wiib->delete_image($_GET['x'], $fulldelete=true);
	if( !$d ) { print 'Delete error'; exit; } 
	header("Location: $url"); exit;
}

$d = $wiib->delete_image($_GET['x'], $fulldelete=false);

if( !$d ) { 
	//print 'Delete error'; exit; 
} 

header("Location: $url"); exit;


