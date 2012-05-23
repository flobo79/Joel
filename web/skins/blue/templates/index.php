<?php

/**
 * Template Index File
 * 
 * available Constants
 *  PROJECT_TITLE
 *  
 * available variables
 *  $skinpath
 *  
 */
?><?php echo '<?xml version="1.0" ?>'; ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title><?php echo PROJECT_TITLE ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php include(CLIENT_BASEDIR."/css.php"); ?>
	<link href="web/skins/<?php echo SKIN ?>/print.css" rel="stylesheet" type="text/css" media="print" />
	<?php include(CLIENT_BASEDIR."/js.php"); ?>
	<link id="ico1" rel="shortcut icon" href="web/skins/<?php echo SKIN ?>/gfx/favicon.png" type="image/x-icon" />
	<link id="ico2" rel="icon" href="web/skins/<?php echo SKIN ?>/gfx/favicon.png" type="image/x-icon" />
</head>

<body>
	
	<div id="Stopwatch_div">
		<div id="Stopwatch_button"><!-- IE FIX --></div>
		<div id="Stopwatch_info"></div>
		<div id="Stopwatch_elapsed">--:--:--</div>
	</div>
	
	
	<div id="contextmenue">
		 <div>
		 	<ul>
				<li id="cm_block"> <span class="small">(c+shift+h)</span><span id="cm_block_text">change to heading</span></li>
				<li id="cm_new"> <span class="small">(c+n)</span>insert new row</li>
				<li id="cm_copy" class="inactive"><span class="small">(c+shift+c)</span>copy rows</li>
				<li id="cm_cut" class="inactive"> <span class="small">(c+shift+x)</span>cut rows</li>
				<li id="cm_delete" class="inactive"> <span class="small">(c+&lt;-)</span>delete rows</li>
				<li id="cm_paste" class="inactive"> <span class="small">(c+shift+v)</span>paste copied rows</li>
			</ul>
		</div>
	</div>
	
	<div id="tablistbox"></div>
	<div class="contentbox_shdw l"> </div>
	<div class="contentbox_shdw r"> </div>
	
	<div id="contentbody">
		<div id="contentbox">
			<div class="content_loading">
				<div id="loading_status">Loading ...</div>
			</div>
		</div>
	</div>
</body>
</html>
