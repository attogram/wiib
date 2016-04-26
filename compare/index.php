<?php
// WIIB
// COMPARE

$class = '../wiib.php'; 
if(!file_exists($class)||!is_readable($class)){ print 'Site down for maintenance'; exit; } require_once($class);

$wiib = new wiib(
	$debug = 0,
	$title = 'Compare Images'
);

$wiib->get_image_count();
$wiib->images = $wiib->get_random_images_from_db(2);

include('../header.php');

if( count($wiib->images) < 1 )  {
	print '<p class="head">no images in portfolio ' . $wiib->portfolio . '</p>';
	print '<p class="head"><a href="' . $wiib->url('import') . '">Import images now</a></p>';
	include('../footer.php');
	exit;
}

print '<table><tr>';
while( list(,$x) = each($wiib->images) ) {
	print '<td>' . $wiib->display_image($x) . '</td>';
}
print '</tr></table>';
include('../footer.php');
exit;

