<?php
/* 
RQMS - Rash Quote Management System
Copyright (C) 2003-2004 Tom Cuchta (tommah@instable.net / http://www.mastergoat.com) and Instable Network (p00p@instable.net / http://www.instable.net)

http://rqms.sourceforge.net

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

############
##FRONT PAGE
############

function front_page_output(){		// Usually the first page you see. It's configured for use as Bash.org and the default rash
	global $section;				// And Bash templates. Change at your own will.
	$welcomecolumn = '1';			// Turning this to anything but 1 turns the welcome column off the main page of your site. The news updates will stretch across the empty space.
	$newscolumn = '1';				// Turning this to anything but 1 turns the news updates off and the welcome page will stretch across the empty space.
	include('config.php');
	if($welcomecolumn == '1'){
?>
		<div id="news_format">
			<div id="frontpage_left">
				<p><span style="font-weight:bold">Hello!</span> Enjoy our collection of quotes and if you feel the pleasure, you can add some of your own. 	</p>
				<p>Depending on if you like or dislike a quote, there are links that are + and - signs that you can click to promote or demote a quote. If you think a quote has slipped by our highly technological sensors and isn't appropriate for this website, you can always click the [X] link near it. <br /> Note: The quote is not removed from the database, it just puts a flag up for the admins to check it to verify that it is alright, it may or may not be removed. </p>
				Queries? Remarks? <a href="mailto:<?=$email?>"><?=$email?></a>
			</div>
			<div id="frontpage_right">
<?php
	}
	if($newscolumn){
	$link = mysql_connect("$hostname", "$username", "$dbpasswd")
			or die($noservercnx . mysql_error());
		if (! @mysql_select_db("$dbname") ) {
			echo($nodbcnx);
			exit();
		}
		$sql = "SELECT * FROM `" . $newstable . "` ORDER BY `id` desc LIMIT " . $newslimit;
		$result = database_connect($sql);
		while($row = mysql_fetch_array($result)){
			echo nl2br("<p class=\"news_date\">" . $row["date"] . "</p>\n"); 
			if(login_check(0)){
				echo nl2br(	"<a href=\"?news_edit" . $GET_SEPARATOR_HTML . "id=" . $row['id'] . "\">[Edit]</a>&nbsp;" .
							"<a href=\"?news_delete" . $GET_SEPARATOR_HTML . "id=" . $row['id'] . "\">[Delete]</a>");
			}
			echo nl2br("<div class=\"news_post_format\">" . $row["news"] . "</div>\n");
		}
		echo "</div>\n</div>\n";
	}
}



#############
##ADMIN LOGIN
#############

function preadmin_output(){								//	Displayed at ./?admin IF the user is not logged in		Query for admin/mod login.
print <<<EOF
       Welcome Guest!<br />								
       If your account says "does not exist" or something of the sort, try using all lowercase letters. The usernames ARE case sensitive.<br />
       <form method="post" action="?adminlogin">
        User Name: <input type="text" name="user" size="28" class="basicinput"><br />
        <p>
         Password:&nbsp;&nbsp; <input type="password" name="password" size="28" class="basicinput"><br />
        </p>
        <input type="submit" name="submit" class="basicsubmit">
       </form>
EOF;
}

################
##ADD QUOTE PAGE
################

function add_quote_page_output(){						//	Displayed at ./?add		Query for users to add quotes
print <<<EOF
       Please remove timestamps unless necessary.
      </div>
     </td>
    </tr>
    <tr>
     <td>
      <div align="left">
       <form method="post" action="?added">
        <textarea cols="80" rows="5" name="quote" class="basicinput"></textarea><br />
        <input type="submit" value="Add Quote" class="basicsubmit">
        <input type="reset" value="Reset" class="basicsubmit"><br />
       </form>
EOF;
}

####################
##SEARCH QUOTES PAGE
####################

function search_quotes_page_ouput(){					//	Displayed at ./?search		The search query for the user
print <<<EOF
       <form method="post" action="?searched">
        <input type="text" name="search" size="28" class="basicinput">
        <input type="submit" name="submit" class="basicsubmit"><br />
        Sort the quotes by: <select name="sortby" size="1">
         <option selected>rating
         <option>id
        </select>&nbsp;&nbsp;&nbsp;&nbsp;
		How many quotes to show?: <select name="number" size="1">
         <option selected>10
         <option>25
         <option>50
         <option>75
         <option>100
        </select>
       </form>
EOF;
}


########################
##ADD QUOTE CONFIRMATION
########################

function add_quote_confirmation($qQUOTE){
	include('config.php');
print <<<EOF
       Thanks, your quote has been submitted.<br />
       For verification, the quote you have submitted is<br />
EOF;
	echo nl2br(
		"<pre>" . $qQUOTE . "</pre>\n"
	);
print <<<EOF
       if this is not the quote you have submitted or planned to submit, send an e-mail to <a href="mailto:$email">$email</a>, and explain your problem.<br />
       Thank you.
EOF;
}

##############
##QUOTE FORMAT
##############

function quote_format($sql, $qotw_check){								//	Displayed anywhere quotes are returned.
	include('config.php');
	global $qID;
	$result = database_connect($sql);
	while ( $row = mysql_fetch_array($result) ) {
		echo(
			"       <a href=\"?" . $row['id'] . "\">#" . $row['id'] . "</a>\n" .
			"       <a href=\"?ratingplus" . $GET_SEPARATOR_HTML . "id=" . $row['id'] . "\" style=\"text-decoration:none;\">+ </a>\n" . 
			"       (" . $row['rating'] . ")\n" .
			"       <a href=\"?ratingminus" . $GET_SEPARATOR_HTML . "id=" . $row['id'] . "\" style=\"text-decoration:none;\">-</a>\n" .
			"       <a href=\"?flag" . $GET_SEPARATOR_HTML . "id=" . $row['id'] . "\" style=\"text-decoration:none;\">[X]</a>\n"
		);
		if($qotw_check){
			echo "       <span style=\"font-weight:bold\">Submitted on " . date ("M d Y", $row['date']) . " at " . date ("g:i A", $row['date']) . "</span>";
		}
		if(login_check(0)){
			echo(
				"	<a href=\"?adelete" . $GET_SEPARATOR_HTML . "id=" . $row['id'] . "\" style=\"text-decoration:none\">[Delete]</a>\n" .
				"	<a href=\"?aedit" . $GET_SEPARATOR_HTML . "id=" . $row['id'] . "\" style=\"text-decoration:none\">[Edit]</a>\n"
			);
		}
		echo nl2br(
			"<br /><div class=\"quote_output\">" . $row["quote"] . "</div>"
		);
	}
}

############
##QUOTE_EDIT
############
function quote_edit($sql){
	include('config.php');
	while ( $row = mysql_fetch_array($result)){
print <<<EOF

      <form action="?aedit" method="post">
       <textarea cols="70" rows="15" name="quote" class="basicinput">
EOF;
echo $row['quote'];
print <<<EOF
</textarea><br />
       Edit the quote.<br /><br />
       <input type="text" size="4" name="rating" class="basicinput" value="
EOF;
echo $row['rating'];
print <<<EOF
"><br />
       Edit the quote's rating.<br /><br />
       <input type="submit" class="basicsubmit" value="Submit">
       <input type="hidden" value="1" name="editresult">
       <input type="hidden" value="
EOF;
echo $row['id'];
print <<<EOF
" name="id">
      </form>
EOF;
	}
}

############
##NEWS_CHANGE
############
function news_change($sql){
	include('config.php');
	$link = mysql_connect("$hostname", "$username", "$dbpasswd")
		or die($noservercnx . mysql_error());
	if (! @mysql_select_db("$dbname") ) {
		echo $nodbcnx;
		exit();
	}
	$result = mysql_query($sql)
		or die($queryerror . mysql_error());
	while($row = mysql_fetch_array($result)){
print <<<EOF
      <form action="?news_edit" method="post">
	   <textarea cols="70" rows="15" name="news" class="basicinput">
EOF;
echo $row['news'];
print <<<EOF
</textarea><br />
      Edit the news.<br /><br />
	  <input type="text" size="4" name="date" class="basicinput" value="
EOF;
echo $row['date'];
print <<<EOF
"><br />
		Edit the news' date.<br /><br />
		<input type="submit" class="basicsubmit" value="Submit">
		<input type="hidden" value="1" name="editresult">
		<input type="hidden" value="
EOF;
echo $row['id'];
print <<<EOF
" name="id">
	</form>
EOF;
	}
}
?>
