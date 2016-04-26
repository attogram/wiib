<?php
// WIIB 
// index

$class = 'wiib.php'; 
if(!file_exists($class)||!is_readable($class)){ print 'Site down for maintenance'; exit; } require_once($class);

$wiib = new wiib(
	$debug = 0,
	$title = 'Which image is better ... or worse?'
);

$wiib->get_image_count();

include('header.php');
?>
<div style="text-align:center;background-color:#333;color:#9c9;">
<h1>WiiB</h1>
<br/><br />
<h2>an online shared light table</h2>
<br/><br />
<h3>with free license photos, images, and illustrations from Wikimedia Commons</h3>
<br/><br />
<h4>WiiB - Which image is Better ... or worse?</h4>
<br/><br />
</div>
<?php

include('footer.php');

