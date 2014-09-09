<?php
// WIIB 0.6.0
// TOOLS

$class = '../wiib.php'; 
if( !file_exists($class) || !is_readable($class) ) { print 'Site down for maintenance'; exit; } require_once($class);

$wiib = new wiib(
	$debug = 0,
	$title = 'Tools'
);

$result = do_tools();

include('../header.php');

?>
<div style="color:black;background-color:white;padding:10px;margin:10px;font-size:15pt;">
<div style="background-color:yellow;"><?php print $result; ?></div>
Tools:
<br /><br /> - <a href="./?t=t">empty trash</a>
<br /><br /> - <a href="./?t=0">clear all ratings</a>
<br /><br />

<br />Portfolios:
<pre>
<?php 
	reset($wiib->portfolio_list);
	while( list(,$x) = each($wiib->portfolio_list) ) {
		print 'Portfolio ' . $x['portfolio'] . ' (' . $x['count'] . ' images)';
		print ' +' . $x['votes_for'] . ' -' . $x['votes_against'];
		$nv = $wiib->number_of_votes($x['portfolio']);
		while(list(,$y) = each($nv) ) { 
			print '<br /> &nbsp; &nbsp;  # votes: ' . $y['votes'] 
			. ' &nbsp;  # images: ' . $y['count'];
		} 
		print '<br /><br />';
	} 

?>
</pre>
</div>

<?php
include('../footer.php');


///////////////////////////////////////////////
function do_tools() {

	global $wiib;

	if( !isset($_GET['t']) ) { return; } 

        switch( $_GET['t'] ) {
                case 't': $r = 'Empty trash: ' . ($wiib->empty_trash() ? 'OK':'ERROR'); break;
                case '0': $r = 'Clear all ratings: ' . ($wiib->clear_all_ratings() ? 'OK':'ERROR'); break;
		default : $r = 'ERROR - unknown option'; break;
        }
	return 'Result: '. $r . '<br />';
}



