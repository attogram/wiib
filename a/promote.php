<?php
// WIIB 
// Action - Promote Image 

$class = '../wiib.php'; 
if(!file_exists($class)||!is_readable($class)){ print 'Site down for maintenance'; exit; } require_once($class);
$wiib = new wiib( $debug = 0, $title = 'Promote Image');

$url = $wiib->url('home');
if( isset($_GET['p']) && $wiib->is_number($_GET['p']) ) {
        $v = $wiib->promote_image($_GET['p']);
        if( isset($_GET['r']) && $_GET['r'] ) { $url = $wiib->url($_GET['r']); }
}
header('Location: ' . $url);
exit;

