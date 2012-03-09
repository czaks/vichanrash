<?php
/*
RQMS - Rash Quote Management System
Copyright (C) 2003-2004 Tom Cuchta (tommah@instable.net / http://www.mastergoat.com) and Instable Network (p00p@instable.net / http://www.instable.net)

http://rqms.sourceforge.net

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/


function adminpanel(){
	function content(){
		include('config.php');
?>
		<span class="admin_sheader">Update news</span>
		<p><form action="/news" method="post">
			<textarea rows="5" cols="80" name="news" class="grey_textarea"></textarea>
			<input type="submit" name="submit" value="Add News">
		</form></p>
		<span class="admin_sheader">Change Password</span><br />
        input the password you want to change yours to into both boxes. If they do not equal each other, you will receive and error and have to do it again.</p>
		<p><form action="/password" method="post">
			<input type="password" name="password" class="grey_input">
			<input type="password" name="passwordchk" class="grey_input">
			<input type="submit" name="submit" value="Change Password">
		</form></p>
		<span class="admin_sheader">Add a user</span><br />
		<!--only super users can access this command.--> The password is "password" when the user logs in. (S)he may log in with that password and change it.
		<form action="/adduser" method="post">
			<input type="text" name="newuser" class="grey_input">
			1. Super User --&gt;<input type="radio" name="usrlvl" value="1" class="grey_radio">
			2. Administrator --&gt;<input type="radio" name="usrlvl" value="2" class="grey_radio">
			3. Moderator --&gt;<input type="radio" name="usrlvl" value="3" class="grey_radio"><br />
			<input type="submit" name="submit" value="Add User">
		</form>
		<span class="admin_sheader">Add a quote</span><br />
		Use this form to add a quote directly into the quote database. It bypasses all check systems.<br />
		<form action="/adminadd" method="post">
			<textarea rows="5" cols="80" name="quote" class="basicinput"></textarea>
			<input type="submit" name="submit" value="Add Quote" class="basicsubmit">
		</form></p>
		<span class="admin_sheader">Queue</span><br />
<?
		$sql = "SELECT * FROM `" . $subtable . "`";
		$result = database_connect($sql);
		while($row = mysql_fetch_array($result)){
?>
		<a href="/disapprove;id=<?=$row['id']?>" class="asection_disapprovelink">[disapprove]&nbsp;</a>
		<a href="/?approve<?=$GET_SEPARATOR_HTML?>quote=<?=urlencode($row['quote'])?>" class="asection_approvelink">[approve]&nbsp;</a>
		<div class="quote_output">
<?
			echo nl2br($row["quote"] . "\n");
?>
		</div>
<?
		}
?>
		<span class="admin_sheader">Flagged quotes</span><br />
<?
		$sql = "SELECT * FROM `" . $quotetable . "` WHERE `check` =0";
		$result = database_connect($sql);
		while($row = mysql_fetch_array($result)){
?>
		<a href="/delete;id=<?=$row['id']?>" class="asection_deletelink">[delete]&nbsp;</a>
		<a href="/unflag;id=<?=$row['id']?>" class="asection_checklink">[check]&nbsp;</a><br />
		<div class="quote_output">
<?
			echo nl2br($row['quote']);
?>
		</div>
<?
		}
	}
	template();
}
?>