<?php
// WIIB 
// Image info

$class = '../wiib.php'; 
if( !file_exists($class) || !is_readable($class) ) { print 'Site down for maintenance'; exit; } require_once($class);

$wiib = new wiib(
	$debug = 0,
	$database_name = '../admin/imgdb/images.sqlite',
	$title = 'Image Info'
);

if( !isset($_GET['i']) || !$wiib->is_number($_GET['i']) ) { 
	$wiib->fail('ERROR id');
}

$wiib->images = $wiib->get_image_from_db($_GET['i']);
$image = @$wiib->images[0];

if( !$image ) {
	$image = $wiib->get_api_image($_GET['i']);
	if( !$image || !is_array($image) ) { 
		$wiib->fail('No image found in database nor commons api');
	} 
	$wiib->images = $wiib->get_image_from_db($_GET['i']);
	$image = @$wiib->images[0];
	if( !$image ) {
		$wiib->fail('Error getting image from commons api to database');
	} 
} 


$image['title'] = htmlspecialchars($wiib->pretty_title($image['title']));
$wiib->title = 'Image: ' . $image['title']; 
$wiib->portfolio = $image['portfolio'];
$c = $wiib->get_image_count();

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
include('../header.php');

?>
<table>
 <tr>
  <td style="background-color:#aaa;"><?php print $wiib->display_image($image,$return='list'); ?></td>
  <td style="text-align:left;color:black;background-color:#ccc;margin:0px 0px 0px 0px;">

<div style="font-size:32px;font-weight:bold;font-family:sans-serif;background-color:#ccc;color:black;">
 <?php print $image['title']; ?>
</div>

<pre>
ID  : <?php print $image['pageid']; ?> 
SHA1: <?php print @$image['sha1']; ?>  
Mime: <?php print @$image['mime']; ?> 

Portfolio: <?php print @$image['portfolio']; ?>  
Score    : <?php 

print ''
	. @round(($image['votes_for']-$image['votes_against'])/($image['votes_for']+$image['votes_against']),2)
	. ' ('
	. ($image['votes_for'] - $image['votes_against']) . '/' . ($image['votes_for'] + $image['votes_against'])
	. ')'
	; 
?> as of <?php print @$image['last_seen']; ?> GMT

Info URL : <a target="img" href="<?php print @$image['descriptionurl']; ?>"><?php print @$image['descriptionurl']; ?></a> 
User     : <?php print @$image['user']; ?> 
Timestamp: <?php print @$image['timestamp']; ?> 
License  : Wikimedia Commons <a target="img" href="http://en.wikipedia.org/wiki/Free_content">Free content</a> 

Mid size : <?php print @$image['thumbwidth'] . ' x ' . @$image['thumbheight']; ?> 
Mid URL  : <a target="img" href="<?php print @$image['thumburl']; ?>"><?php print @$image['thumburl']; ?></a> 

Full size: <?php print @$image['width'] . ' x ' . @$image['height']; ?> 
Full URL : <a target="img" href="<?php print @$image['url']; ?>"><?php print @$image['url']; ?></a> 
</pre>
  </td>
 </tr>
</table>
<?php

include('../footer.php');
exit;

