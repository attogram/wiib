<?php
// WIIB 0.6.0
// EXPORT 

$class = '../wiib.php'; 
if( !file_exists($class) || !is_readable($class) ) { print 'Site down for maintenance'; exit; } require_once($class);

$wiib = new wiib(
	$debug = 0,
	$title = 'Export images'
);

$wiib->get_image_count();

include('../header.php');
?>
<div style="color:black;background-color:white;padding:10px;margin:10px;">

<div style="font-size:24px;">Export <?php print $wiib->image_count; ?> images 
from portfolio <?php print $wiib->portfolio; ?></div>

<?php
	$wiib->get_images_by_portfolio($limit=$wiib->image_count, $sort='pageid', 'ASC');
	if( !$wiib->images ) { $i = array(); print "ERROR: NO IMAGES FOUND";  } 	
	$count = 0;
	$export = '';
	while( $x = each($wiib->images) ) { 
		$export .= @$x['value']['pageid'];
		$count++; if( $count != count($wiib->images) ) { $export .= '|'; }
	
	} 
	
?><br />ID list, deliminator |<br /><textarea rows="10" cols="60" style="width:100%;"><?php print $export;?></textarea>
<?php
/*
	$cr = "\n";
	$export_csv = 'pageid,votes_for,votes_against,last_seen,portfolio,sha1,timestamp,title,user,descriptionurl,mime,thumburl,thumbwidth,thumbheight,url,height,width' . $cr;
	reset($wiib->images);
	while( $x = each($wiib->images) ) { 
		$export_csv .= $x['value']['pageid'] . ','
		. $x['value']['votes_for'] . ','
		. $x['value']['votes_against'] . ','
		. '"' . $x['value']['last_seen'] . '",'
		. $x['value']['portfolio'] . ','
		. $x['value']['sha1'] . ','
		. $x['value']['timestamp'] . ','
		. '"' . $x['value']['title'] . '",'
		. '"' . $x['value']['user'] . '",'
		. $x['value']['descriptionurl'] . ','
		. $x['value']['mime'] . ','
		. $x['value']['thumburl'] . ','
		. $x['value']['thumbwidth'] . ','
		. $x['value']['thumbheight'] . ','
		. $x['value']['url'] . ','
		. $x['value']['height'] . ','
		. $x['value']['width'] // . ','
		. $cr
		;
	}
<br />CSV:<br /><textarea rows="10" cols="60" style="width:100%;"><?php print $export_csv;?></textarea>
*/
?>
</div>

<?php
$wiib->images = false;  // not needed on export page
include('../footer.php');
