<!--
test_table.php - Preliminary interface to upgraded database table system.
Copyright (c) 2018 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
-->

<?php
include($_SERVER['DOCUMENT_ROOT'].'/cxa/php/session.php');
include($_SERVER['DOCUMENT_ROOT'].'/cxa/meta.php');
boot_user(4);
?>
<html>
	<head>
		<title><?=$sitetitle?> - User Management</title>
		<link rel="stylesheet" type="text/css" href="/cxa/css/cxa-ui.css">
		<link rel="stylesheet" type="text/css" href="/cxa/css/cxa-um.css">
		<link rel="icon" type="image/png" href="/cxa/img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=0.5">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="/cxa/js/jquery.min.js"><\/script>')</script>
		<script src="/cxa/js/cxa-ui.js"></script>
		<script src="/cxa/js/cxa-table.js"></script>
		<script>
		var tUsers={
			'columns': {
				'userid': {
					label:      'ID',
					cell_class: 'Remote',
					cell_style: 'col-pre',
					mandatory:  false
				},
				'name': {
					label:      'Name',
					cell_class: 'Text',
					cell_style: 'col-18',
					mandatory:  true
				},
				'username': {
					label:      'Username',
					cell_class: 'Text',
					cell_style: 'col-18',
					mandatory:  true
				},
				'email': {
					label:      'E-Mail',
					cell_class: 'Text',
					cell_style: 'col-18',
					mandatory:  true
				},
				'password': {
					label:      'New Password',
					cell_class: 'Password',
					cell_style: 'col-18',
					mandatory:  true
				},
				'otpsecret': {
					label:      'TOTP Secret',
					cell_class: 'Text',
					cell_style: 'col-18',
					mandatory:  false
				},
				'authorization': {
					label:      'Auth',
					label_tip:  'Authorization Level',
					cell_class: 'Number',
					cell_style: 'col-7',
					mandatory:  true
				},
				'editrow': {
					label:      '',
					cell_class: 'EditButton',
					cell_style: 'col-post',
					mandatory:  false
				}
			},
			'data': {
				row_class:  'row',
				row_pkid:   'userid',
				address:    '/cxa/userinter.php',
				get_action: 'getusers',
				set_action: 'setuser',
				del_action: 'deluser'
			}
		};
		

		function scrollWidth ()
		{
			var outer = $('<div></div>').css({visibility: 'hidden', width: 25, overflow: 'scroll'}).appendTo('body');
			var inner = $('<div></div>').css({width: '100%'}).appendTo(outer).outerWidth();
			outer.remove();
			return 25 - inner;
		}
		
		function adjustHeader ()
		{
			$(".theader").css('padding-right', scrollWidth);
		}
		
		function doUsers(){
			$.post(interAddress,{action:"getusers",data:""},function(data){
				if(!data){
					serverError();
				}else{
					populateTable('#userboard', cElements, data, tUsers);
				}
			},"json");
		}
		
		function refresh ()
		{
			doUsers();
			serverFail = false;
		}
		
		
		//refresh();
		//$("#refresher").click(refresh);
		//adjustHeader();
		//$(window).resize(adjustHeader);
		
		$(document).ready(
			function ()
			{
				var table = new Table($('#split-c'), tUsers);
				$('#refresher').click(function(){table.refresh();});
			}
		);
		$(document).ready(CXAUI);
		</script>
	</head>
	<body>
		<div id="bigmain">
			<div id="topbar" class="loginbar noselect"><?php cxa_header("User Manager") ?><div id="refresher" style="margin-left: 10px;"></div></div>
			<div id="split-c"></div>
			<div id="footer"><?php cxa_footer() ?></div>
		</div>
	</body>
</html>