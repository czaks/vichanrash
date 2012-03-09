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
print <<<EOF
  <div align="center">
   <table width="80%">
    <tr>
     <td>
      <div align="left">
       Welcome to the disorganized administration section.<br />
       <p>Update news</p>
       <p><form action="?news" method="post">
        <textarea rows="5" cols="80" name="news" class="basicinput"></textarea>
        <input type="submit" name="submit" value="Add News" class="basicsubmit">
       </form></p>
       <p>Change your password: input the password you want to change yours to into both boxes. If they do not equal each other, you will receive and error and have to do it again.</p>
       <p><form action="?password" method="post">
        <input type="password" name="password" class="basicinput">
        <input type="password" name="passwordchk" class="basicinput">
        <input type="submit" name="submit" value="Change Password" class="basicsubmit">
       </form></p>
       Add a user. Only super users can access this command. The password is "password" when the user logs in. (S)he may log in with that password and change it.
       <form action="?adduser" method="post">
       <input type="text" name="newuser" class="basicinput">
       1. Super User --&gt;<input type="radio" name="usrlvl" value="1" class="radioinput">
       2. Administrator --&gt;<input type="radio" name="usrlvl" value="2" class="radioinput">
       3. Moderator --&gt;<input type="radio" name="usrlvl" value="3" class="radioinput"><br />
       <input type="submit" name="submit" value="Add User" class="basicsubmit">
       </form>
       Add a quote, use this form to add a quote directly into the quote database. It bypasses all check systems.<br />
       <form action="?adminadd" method="post">
        <textarea rows="5" cols="80" name="quote" class="basicinput"></textarea>
        <input type="submit" name="submit" value="Add Quote" class="basicsubmit">
       </form></p>
      </div>
     </td>
    </tr>
    <tr>
     <td>
      <div align="left"><br />
       Quotes queued in submit database<br />
EOF;

//		SUBMITTED QUOTES

		$link = mysql_connect("$hostname", "$username", "$dbpasswd")
			or die($noservercnx . mysql_error());
		if (! @mysql_select_db("$dbname") ) {
			echo($nodbcnx);
			exit();
		}
		$result = mysql_query('SELECT * FROM `' . $subtable . '`')
			or die($queryerror . mysql_error());
		while ( $row = mysql_fetch_array($result) ) {
			echo(
				"       <a href=\"?disapprove&id=" . $row['id'] . "\" style=\"text-decoration:none;\">[disapprove]&nbsp;</a>\n" .
				"       <a href=\"?approve&quote=" . urlencode($row['quote']) . "\" style=\"text-decoration:none;\">[approve]&nbsp;</a>\n"
			);
			echo nl2br(
				"       <pre>" . $row["quote"] . "</pre>"
			);
		}
print <<<EOF
	Flagged quotes<br />
EOF;
//		FLAGGED QUOTES

		$result = mysql_query('SELECT * FROM `' . $quotetable . '` WHERE `check` =0')
			or die("Invalid query: " . mysql_error());
		while ( $row = mysql_fetch_array($result) ) {
			echo(
				"       <a href=\"?delete&id=" . $row['id'] . "\" style=\"text-decoration:none\">[delete]&nbsp;</a>\n" .
				"       <a href=\"?unflag&id=" . $row['id'] . "\" style=\"text-decoration:none\">[check]&nbsp;</a><br />\n"
			);
			echo nl2br(
				"       <pre>" . $row['quote'] . "</pre>\n");
		}
print <<<EOF
      </div>
     </td>
    </tr>
   </table>
EOF;
	}
	template();
}
?>