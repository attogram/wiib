<?php
// WIIB
// ADMIN 

$class = '../wiib.php'; 
if( !file_exists($class) || !is_readable($class) ) { print 'Site down for maintenance'; exit; } require_once($class);

$wiib = new wiib(
	$debug = 0,
	$title = 'Tools'
);

if( !$wiib->admin ) {
	$wiib->fail('Error admin');
} 
include('../header.php');
?><div style="color:black;background-color:white;padding:10px;margin:10px;">

ADMIN TOOLS:
<br /><br /> - <a href="../admin/sqladmin.php" target="admin">sql admin</a>

<br /><br /> - <a href="./?t=c">CREATE tables</a>

<br /><br /> - <a href="./?t=e">DELETE FROM images</a>

<br /><br /> - <a href="./?t=d">DROP tables</a>

<br /><br />Result: 
<?php 

if( isset($_GET['t']) ) { 
	switch( $_GET['t'] ) { 
		// admin tools
                case 'e': print $wiib->empty_images(); break;
		case 'c': print $wiib->create_tables(); break;
		case 'd': print $wiib->drop_tables(); break;
	}
}

print '</div>';
include('../footer.php');
