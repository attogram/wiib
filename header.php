<?php if( !defined('__WIIB__') ) { exit; } ?>
<!doctype html><html><head>
<title><?php print $wiib->title; ?></title>
<meta charset="utf-8" />
<meta name="viewport" content="initial-scale=1" />
<link rel="stylesheet" type="text/css" href="<?php print $wiib->url('css'); ?>">
</head>
<body>
<noscript>
 <div style="background-color:white; color:black; font-size:22px; padding:20px;">
  Please enable Javascript to use all the features of this site
 </div>
</noscript>
<div class="head" style="font-size:13pt;">
 <div style="display:inline-block">
  <a href="<? print $wiib->url('home'); ?>">WiiB</a> 
  &nbsp; <?php print $wiib->portfolio_select(); ?>
  &nbsp; <?php print $wiib->count_unrated(); ?> unrated
 </div>
 <div style="float:right;display:inline-block;">
  &nbsp; <a href="<? print $wiib->url('compare'); ?>">Compare</a> 
  &nbsp; <a href="<? print $wiib->url('list'); ?>">List</a> 
  &nbsp; <a href="<? print $wiib->url('import'); ?>">Import</a> 
  &nbsp; <a href="<? print $wiib->url('export'); ?>">Export</a> 
  &nbsp; <a href="<? print $wiib->url('tools'); ?>">Tools</a> 
  <?php if( $wiib->admin ) { ?>&nbsp; <a href="<? print $wiib->url('admin'); ?>">ADMIN</a><?php } ?>
 </div>
</div>

