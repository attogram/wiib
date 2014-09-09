<?php if( !defined('__WIIB__') ) { exit; } ?>

<footer>
<br />

<div style="margin:10px; padding:10px; background-color:#3c3c3c; color:#999; font-size:12px; 
font-family:sans-serif,helvetica,arial; width:700px; margin-left:auto; margin-right:auto; 
border:1px solid black;">
WiiB Page Report:<br /><br />
Page Title: <?php print $wiib->title; ?><br/>
Page URL: <a style="color:#999;" href="<?php print $wiib->host . $_SERVER['REQUEST_URI'] 
	. '">' . $wiib->host .  $_SERVER['REQUEST_URI']; ?></a><br />
Page accessed: <?php print gmdate('Y-m-d H:i:s'); ?> GMT<br />
Accessed from: <?php print @$_SERVER['REMOTE_ADDR']; ?> <?php print @$_SERVER['REMOTE_HOST']; ?><br />
# of accesses: <?php print $wiib->user_hits; ?><br />

<?php
if( is_array($wiib->images) ) { 
	reset($wiib->images);
	print '<br />Images on page: ' . count($wiib->images) . "<br />";
	while(list(,$i) = each($wiib->images) ) {
		if( !isset($i['votes_for']) ) { $i['votes_for'] = '0'; } 
		if( !isset($i['votes_against']) ) { $i['votes_against'] = '0'; } 
		if( !isset($i['last_seen']) ) { $i['last_seen'] = 'new'; } 
		print "<br />";
		print 'ID: ' . $i['pageid'] . "<br />";
		print 'Title: ' . $wiib->pretty_title($i['title']) . "<br />";
		print ' URL: <a target="image_info" style="color:#999;" href="' . $i['descriptionurl'] . '">' . $i['descriptionurl'] . "</a><br />";
		print 'user: ' . $i['user'] . "<br />";
		print 'timestamp: ' . $i['timestamp'] . "<br />";
		print 'License: Wikimedia Commons Free Content<br />';
		print 'Votes: +' . $i['votes_for'] . ' -' . $i['votes_against'] 
			. ' as of ' . $i['last_seen'] . ' GMT' . "<br />";
	}
}

?>
</div>
</footer>
</body></html>
