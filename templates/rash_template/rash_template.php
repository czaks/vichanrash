<?php
/*
RQMS - Rash Quote Management System
Copyright (C) 2003-2004 Tom Cuchta (tommah@instable.net / http://www.mastergoat.com) and Instable Network (p00p@instable.net / http://www.instable.net)

http://rqms.sourceforge.net

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

// This is a basic color shift of the bash_template (bash clone), but it uses more technology and advanced things
// Rather than straight up tables and HTML 4.01, this template uses more CSS and XHTML 1.0
// This template may cause older browsers to malfunction and such since it uses newer technologies, blabla

function template(){
global $section;
switch ($section){
	case 0:
		$section='Administracja';
		break;
	case 1:
		$section='Główna';
		break;
	case 2:
		$section='Ostatnie';
		break;
	case 3:
		$section='Przeglądaj';
		break;
	case 4:
		$section='Losowe';
		break;
	case 5:
		$section='Najlepsze 150';
		break;
	case 6:
		$section='Najgorsze';
		break;
	case 7:
		$section='Dodaj';
		break;
	case 8:
		$section='Szukaj';
		break;
	case 9:
		$section='Cytaty tygodnia';
		break;
	case 10:
		$section='Zagłosuj na cytat';
		break;
	case 11:
		$section='Zagłosuj na cytat';
		break;
}
global $rashversion;

header("Content-type: text/html; charset=utf-8");
//echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"? >\n"
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='pl' lang='pl'>
<head>
<title>6IRCNet QDB: <?=$section?></title>
<style type="text/css">
<!--
	@import url("/templates/rash_template/rash_style.css?1");
-->
</style>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
</head>
<body>
	<form method="post" action="/quotesearch">
		<table class="bars_table" cellspacing="0">
			<tr>
				<td class="blue_bar">
            		<span id="qms">6IRCNet QDB</span>&nbsp; 
	    	        <a href="/admin" id="admin_link">Admin</a>&nbsp;
<?
if(login_check(0)){
	echo "				<a href=\"./?logout\" id=\"log_out\">Log Out</a>\n";
}
?>
				</td>
				<td class="blue_bar">
            		<div id="section"><?=$section?></div>
				</td>
			</tr>
			<tr>
				<td class="grey_bar" colspan="2">
					<a href="/">Główna</a>&nbsp;/
					<a href="/latest">Ostatnie</a>&nbsp;/
					<a href="/browse">Przeglądaj</a>&nbsp;/
					<a href="/random">Losowe</a>
					<a href="/random2">&gt;0</a>&nbsp;/
					<a href="/top150">Najlepsze 150</a>&nbsp;/
					<a href="/bottom">Najgorsze</a>&nbsp;/
					<a href="/add"><span style="font-weight:bold">Dodaj cytat</span></a>&nbsp;/
					<a href="/qotw">Cytaty tygodnia</a>&nbsp;/
					<a href="/search">Szukaj</a>&nbsp;<span class="grey_hash">/&nbsp;#</span><input type="text" name="qSEARCH" size="4" class="grey_input" />
				</td>
			</tr>
		</table>
	</form>
	<div id="content_format">
<?content();// output of the rash index is inserted here?>
	</div>
	<form method="post" action="/quotesearch">
		<table class="bars_table" cellspacing="0">
			<tr>
				<td class="grey_bar" colspan="2">
					<a href="/">Główna</a>&nbsp;/
					<a href="/latest">Ostatnie</a>&nbsp;/
					<a href="/browse">Przeglądaj</a>&nbsp;/
					<a href="/random">Losowe</a>
					<a href="/random2">&gt;0</a>&nbsp;/
					<a href="/top150">Najlepsze 150</a>&nbsp;/
					<a href="/bottom">Najgorsze</a>&nbsp;/
					<a href="/add"><span style="font-weight:bold">Dodaj cytat</span></a>&nbsp;/
					<a href="/qotw">Cytaty tygodnia</a>&nbsp;/
					<a href="/search">Szukaj</a>&nbsp;<span class="grey_hash">/&nbsp;#</span><input type="text" name="qSEARCH" size="4" class="grey_input" />
				</td>
			</tr>
			<tr>
				<td class="blue_bar">
					<div id="rash_version">Wersja oprogramowania: <?=$rashversion?>&nbsp;</div>
				</td>
			</tr>
		</table>
	</form>
</body>
</html>
<? } ?>
