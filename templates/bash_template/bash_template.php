<?php
/*
RQMS - Rash Quote Management System
Copyright (C) 2003-2004 Tom Cuchta (tommah@instable.net / http://www.mastergoat.com) and Instable Network (p00p@instable.net / http://www.instable.net)

http://rqms.sourceforge.net

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

/*
##########
Template
This is the function that sets the layout of each page. Edit this if you're just into changing the looks of your page. Try to keep the quotations in order, or use some other strange uber-cool PHP command that I don't know about to do whatever. Note the periods and the last entry not having them. Have fun.
*/
function template(){
global $section;
switch ($section){
	case 0:
		$section='Administration';
		break;
	case 1:
		$section='Frontpage';
		break;
	case 2:
		$section='Latest';
		break;
	case 3:
		$section='Browse';
		break;
	case 4:
		$section='Random';
		break;
	case 5:
		$section='Top 150';
		break;
	case 6:
		$section='Bottom';
		break;
	case 7:
		$section='Add';
		break;
	case 8:
		$section='Search';
		break;
	case 9:
		$section='Quotes of the Week';
		break;
	case 10:
		$section='Upgrade Quote';
		break;
	case 11:
		$section='Downgrade Quote';
		break;
}
global $rashversion;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>QMS</title>
	<link rel="stylesheet" type="text/css" href="./templates/bash_template/bash_style.css">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta name="robots" content="noarchive,nofollow">
</head>
<body>
	<form method="post" action="?quotesearch">
		<table width="80%" cellpadding="2" cellspacing="0" border="0" align="center"> 
			<tr>
				<td class="orange_bar">
					<span class="qms">QMS</span>&nbsp;
					<a href="./?admin" class="admin">Admin&nbsp;</a>
<?
if(login_check(0)){
?>
					<a href="./?logout" class="log_out_button">Log Out</a>
<?
}
?>
				</td>
				<td style="background-color:#c08000;text-align:right">
					<span class="section">
						<?=$section?>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="background-color:#f0f0f0;text-align:right">
					<a href="./">Home</a>&nbsp;/
					<a href="./?latest">Latest</a>&nbsp;/
					<a href="./?browse">Browse</a>&nbsp;/
					<a href="./?random">Random</a>
					<a href="./?random2">&gt;0</a>&nbsp;/
					<a href="./?top150">Top 150</a>&nbsp;/
					<a href="./?bottom">Bottom</a>&nbsp;/
					<a href="./?add"><span style="font-weight:bold">Add Quote</span></a>&nbsp;/
					<a href="./?qotw">QotW</a>&nbsp;/
					<a href="./?search">Search</a>&nbsp;/#<input type="text" name="qSEARCH" size="4" class="bar_search" style="color:#000000">
				</td>
			</tr>
		</table>
	</form>
	<table width="80%" align="center">
		<tr>
			<td valign="top">
<?
content();     // output of the rash index is inserted here
?>
			</td>
		</tr>
	</table>
	<form method="post" action="?quotesearch">
		<table class="bottom_bars" align="center">
			<tr>
				<td style="background-color:#f0f0f0;text-align:right">
					<a href="./">Home</a>&nbsp;/
					<a href="./?latest">Latest</a>&nbsp;/
					<a href="./?browse">Browse</a>&nbsp;/
					<a href="./?random">Random</a>
					<a href="./?random2">&gt;0</a>&nbsp;/
					<a href="./?top150">Top 150</a>&nbsp;/
					<a href="./?bottom">Bottom</a>&nbsp;/
					<a href="./?add"><span style="font-weight:bold">Add Quote</span></a>&nbsp;/
					<a href="./?qotw">QotW</a>&nbsp;/
					<a href="./?search">Search</a>&nbsp;/#<input type="text" name="qSEARCH" size="4" class="bar_search" style="color:$searchbox">
				</td>
			</tr>
			<tr>
				<td style="background-color:#c08000;text-align:right">
					<span class="rashversion">
						Rash Version: <?=$rashversion?>
					</span>
				</td>
			</tr>
		</table>
	</form>
</body>
</html>
<?
}
?>
