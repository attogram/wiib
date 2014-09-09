<?php
// WIIB 0.6.0
// IMPORT 

$class = '../wiib.php'; 
if( !file_exists($class) || !is_readable($class) ) { print 'Site down for maintenance'; exit; } require_once($class);

$wiib = new wiib(
	$debug = 0,
	$title = 'Import images'
);
///////////////////////////////////////////////////////////////////////////////////////////////

if( isset($_REQUEST['ids']) && $_REQUEST['ids'] ) { 

	print "IMPORT:";
	$ids = trim($_REQUEST['ids']);
	$raw = explode('|', $ids);
	if( !$raw || count($raw) < 1 ) { 
		$wiib->fail("ERROR: no image IDs found to import");
	} 
	print "<BR>" . count($raw) . " IDs found.";
	$good = $bad = array();
	while( $x = each($raw) ){  
		if( !isset($x['value']) || !$x['value'] || !$wiib->is_positive_number($x['value']) ) { 
			//print "<br />ERROR: #$x[0] invalid ID: '" . $x['value'] . "'";
			$bad[] = $x['value'];
		} else {
			$good[] = $x['value'];
		} 
	} 

	print "<BR>" . count($good) . " Good IDs found.";
	print "<BR>" . count($bad) . " Bad IDs found.";

	$plist = implode('|',$good);
	print "<BR>IDs list: $plist";

	$r = $wiib->get_api_image($plist);
	print "<BR>RESULTS: " . count($r) . " imported.";

	
 	print '<BR>GOTO: <a href="' . $wiib->url('home') . '">' . $wiib->url('home') . '</a>';

// 	header('Location: ' . $wiib->url('home'));
	exit;
}  



///////////////////////////////////////////////////////////////////////////////////////////////
include('../header.php');
?>
<div style="color:black;background-color:white;padding:10px;margin:10px;">

<div style="font-size:24px;">Import images into portfolio <?php print $wiib->portfolio; ?></div>

<br />
* <a href="../a/get.php?g=r&amp;port=<?php print $wiib->portfolio; ?>&amp;r=compare">add 10 random images</a>

<br /><br />
* Import by search:
<form action="/image/a/get.php" method="GET">
<input type="hidden" name="g" value="s" />
<input type="hidden" name="r" value="list" />
<input type="hidden" name="port" value="<?php print $wiib->portfolio ?>" />
<input id="s" name="s" type="text" size="45" value=""/>
<input type="submit" value="   Import images by Search    " />
</form>


<br /><br />
* Import by ID: 
<form action="<?php print $wiib->url('import'); ?>" method="POST">
<input type="hidden" name="r" value="list" />
<textarea name="ids" rows="5" style="width:100%;"></textarea>
<input type="submit" value="   Import ID list   " />
</form>

<br /><br />
* Import images from Category:
<form action="/image/a/get.php" method="GET">
<input type="hidden" name="r" value="list" />
<input type="hidden" name="g" value="cc" />
<input type="hidden" name="port" value="<?php print $wiib->portfolio ?>" />
<input id="c" name="c" type="text" size="45" value=""/>
<input type="submit" value="   Import images from category    " />
</form>

<?php
$cats = $wiib->get_category_list();
print '<br /><br />* Import from known Category:';
while($x=each($cats)){ 
	$cat = $x['value'] ? $x['value'] : 'ERROR';
	print '<br />' . $cat
	. ' - <a href="../a/get.php?g=cc&amp;c=' . urlencode($x['value']) . '&amp;r=list&amp;port=' . $wiib->portfolio . '">import images</a>'
	. ' - <a target="commons" href="https://commons.wikimedia.org/wiki/' . $x['value'] . '">view on commons</a>'
	;
}

print '</div>';
include('../footer.php');
