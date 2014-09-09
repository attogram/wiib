<?php
// WIIB 0.6.0
// LIST IMAGES 

$class = '../wiib.php'; 
if( !file_exists($class) || !is_readable($class) ) { print 'Site down for maintenance'; exit; } require_once($class);

$wiib = new wiib(
	$debug = 0,
	$title = 'List Images'
);

if( !isset($wiib->portfolio) || !$wiib->is_number($wiib->portfolio) ) { 
	$wiib->fail('ERROR portfolio'); 
}

$wiib->get_image_count();

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
include('../header.php');


switch( @$_GET['n'] ) { 
	case '5': case '10': case '25': case '50': case '100':
		$limit = $_GET['n']; break;
	default: $limit = '25';
}
switch( @$_GET['s'] ) { 
	case 'pageid': case 'votes_for': case 'votes_against': case 'title': case 'timestamp': case 'last_seen':
		$sort = $_GET['s']; break;
	default: $sort = 'votes_for'; break;
}
switch( @$_GET['o'] ) { 
	case 'ASC':  case 'DESC':
		$order = $_GET['o']; break;
	default: $order = 'DESC'; break;
}
switch( @$_GET['z'] ) { 
	case '88': case '100': case '125': case '150': case '200':
		$size = $_GET['z']; break;
	default: $size = 100; break;
} 

print '
<div class="head">
Portfolio #' . $wiib->portfolio . ' - list images 1-' . $limit . ' of ' .  $wiib->image_count . ' total
<br />
<form action="' . $wiib->url('list') . '" method="GET">
<input type="hidden" name="port" value="'  . $wiib->portfolio . '" />
<select name="n">
<option value="5" ' . $wiib->selected($limit,'5')  . '>5 images</option>
<option value="10" ' . $wiib->selected($limit,'10')  . '>10 images</option>
<option value="25" ' . $wiib->selected($limit,'25')  . '>25 images</option>
<option value="50" ' . $wiib->selected($limit,'50')  . '>50 images</option>
<option value="100" ' . $wiib->selected($limit,'100')  . '>100 images</option>
</select> 
<select name="s">
<option value="pageid"' . $wiib->selected($sort,'pageid') . '>sort by pageid</option>
<option value="title"' . $wiib->selected($sort,'title') . '>sort by title</option>
<option value="timestamp"' . $wiib->selected($sort,'timestamp') . '>sort by timestamp</option>
<option value="last_seen"' . $wiib->selected($sort,'last_seen') . '>sort by last updated</option>
<option value="votes_for"' . $wiib->selected($sort,'votes_for') . '>sorty by votes for</option>
<option value="votes_against"' . $wiib->selected($sort,'votes_against') . '>sorty by votes against</option>
</select>
<select name="o">
<option value="DESC"' . $wiib->selected($order,'DESC') . '>descending</option>
<option value="ASC"' . $wiib->selected($order,'ASC') . '>ascending</option>
</select>
<select name="z">
<option value="88" ' . $wiib->selected($size,'88')  . '>size 88px</option>
<option value="100" ' . $wiib->selected($size,'100')  . '>size 100px</option>
<option value="125" ' . $wiib->selected($size,'125')  . '>size 125px</option>
<option value="150" ' . $wiib->selected($size,'150')  . '>size 150px</option>
<option value="200" ' . $wiib->selected($size,'200')  . '>size 200px</option>
</select>
<input type="submit" value="  UPDATE  " />
</form>
<br /><br />
';



$wiib->get_images_by_portfolio($limit, $sort, $order);
if( !$wiib->images ) { 
	//print "LIST: NO IMAGES FOUND";
	$wiib->images = array();
}
while( $i = each($wiib->images) ) { 
	print $wiib->display_image_mini($i['value'], $size);
}

include('../footer.php');
exit;

