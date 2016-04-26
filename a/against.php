<?php
// WIIB 
// Action - Vote Against

$class = '../wiib.php';
if(!file_exists($class)||!is_readable($class)){ print 'Site down for maintenance'; exit; } require_once($class);
$wiib = new wiib( $debug = 0, $title = 'Vote Against');

$url = $wiib->url('home');
if( isset($_GET['d']) && $wiib->is_number($_GET['d']) ) {
	$v = $wiib->vote_against($_GET['d']);
        if( isset($_GET['r']) && $_GET['r'] ) { $url = $wiib->url($_GET['r']); }
}
header('Location: ' . $url);
exit;
