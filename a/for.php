<?php
// WIIB
// Action - Vote For

$class = '../wiib.php'; 
if(!file_exists($class)||!is_readable($class)){ print 'Site down for maintenance'; exit; } require_once($class);
$wiib = new wiib( $debug = 0, $title = 'Vote For');

$url = $wiib->url('home');
if( isset($_GET['b']) && $wiib->is_number($_GET['b']) ) {
	$v = $wiib->vote_for($_GET['b']);
	if( isset($_GET['r']) && $_GET['r'] ) { $url = $wiib->url($_GET['r']); } 
}
header('Location: ' . $url);
exit;

