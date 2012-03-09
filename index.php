<?php
/*
RQMS - Rash Quote Management System
Copyright (C) 2003-2004 Tom Cuchta (tommah@instable.net / http://www.mastergoat.com) and Instable Network (p00p@instable.net / http://www.instable.net)

http://rqms.sourceforge.net

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/
include('config.php');
include($templatefile);
include($admin_template);
include($outputfile);
$versionnumber  = "1.2.1";
$subversion    = "-vichanrash1.0.0";
$rashversion    = $versionnumber . $subversion;

setcookie('cen', 'nec', time() + 3600 * 24 * 31 * 6, '/', '.cytaty.6irc.net');
/*
####################
Database connection
Makes a connection to the database and executes a query.
####################
*/
function database_connect($sql)
{
	include('config.php');
	if(!isset ($GLOBALS['conncreated'])) { 
		$link = mysql_connect("$hostname", "$username", "$dbpasswd")
			or die($noservercnx . mysql_error());
		if(!@mysql_select_db("$dbname")){
			echo $nodbcnx;
			exit();
		}
		$GLOBALS['conncreated'] = true;
	}
	$result = mysql_query($sql)
		or die($queryerror . mysql_error());
	return $result;
}

/*
####################
Return_errors
This function is used only by login_check() internally. It determines if an error message is needed to echo to the user for login purposes. Utilizes an argument that comes from login_check (it's used as an argument there, too, I should probably say parameter someplace in this sentence, no?). Needed because everyime login_check is used and a bad log in is found, there doesn't need to be a log in error echo, such as for the Log Out link and [Edit] buttons on quotes.
####################
*/
function return_errors($login_error)
{
	include('config.php');
	if($login_error){
		echo $not_logged_in;
		return 0;
	}
}

/*
####################
Checks user level
Checks level of user from the database. Uses login_check to determine logged-in status, then checks level.
####################
*/
function level_check()
{
	include('config.php');
	if(login_check){
		$sql = "SELECT `level` FROM `" . $rashusers . "` WHERE `user` LIKE '" . addslashes($_COOKIE['User_Name']) . "'";
		$row = mysql_fetch_row(database_connect($sql));
			return $row;
	}
}

/*
####################
Logged in check Function
Checks log in status and ensures that user is logged in. Checks cookie stored values versus SQL stored values.
####################
*/
function login_check($login_error)
{
	include('config.php');
	if(isset($_COOKIE['User_Name'])){
		$sql = "SELECT * FROM `" . $rashusers . "` WHERE `user` LIKE '" . addslashes($_COOKIE['User_Name']) . "'";
		while($row = mysql_fetch_array(database_connect($sql))){
			if(md5($row['user']) == $_COOKIE['Logged_In']){
				if($row['password'] == $_COOKIE['User_Password']){
					return 1;
				}
			}
		}
	}
	else{
		return_errors($login_error);
	}
}
/*
####################
HOME
Home page, what should be the main page of the website. Where the news shows up.
####################
*/
function home()
{
	function content()
	{
		front_page_output();
	}
	template();
}
/*
####################
ADMIN
Called when the user is not logged in. It's the prompt for username/password.
####################
*/
function admin()   // is only called if user is not logged in
{
	function content()
	{
		include('config.php');
		preadmin_output();			//	output.php
	}
	template();
}
/*
####################
INDIVIDUAL QUOTE
typically ?# in the uri: Called when there is anything that doesn't correspond with any other function in the switch at the bottom. Usually works with integers like ?1 (in the url)
####################
*/
function quote()
{
	function content()
	{
		include('config.php');
		$qID = round($_SERVER['QUERY_STRING']);
		$sql = "SELECT * FROM `" . $quotetable . "` WHERE 1 AND `id` =" . $qID;
		$sqc = "SELECT * FROM `" . $commentstable ."` WHERE `of` = '$qID'";
		//if (strpos ($_SERVER['REMOTE_ADDR'], ':') !== FALSE) {
			quote_full($sql, $sqc);
		//}
		//else {
		//	quote_format($sql, 0);		//	output.php
		//}
	}
	template();
}
/*
####################
BROWSE QUOTES
?browse in the uri: It lets the user browse the quotes in numerical order (1,2,3,4,5,6...).
####################
*/
function browse()
{
	function content()
	{
		include('config.php');
		$sql = "SELECT * FROM `" . $quotetable . "` WHERE 1 AND `approve` =1 ORDER BY `id` asc";
		quote_format($sql, 0);		//	output.php
	}
	template();
}
/*
####################
LATEST
?latest in the uri: The user browses the quotes in reverse numerical order (9,8,7,6,5...).
####################
*/
function latest()
{
	function content()
	{
		include('config.php');
		$sql = "SELECT * FROM `" . $quotetable . "` WHERE `approve` =1 ORDER BY `id` desc LIMIT 50";
		quote_format($sql, 0);		//	output.php
		}
	template();
}
/*
####################
RANDOM QUOTES
?random at the end of a uri: The user gets a random mix of all approved quotes.
####################
*/
function random()
{
	function content()
	{
		include('config.php');
		$sql = "SELECT * FROM `" . $quotetable . "` WHERE `approve` =1 ORDER BY rand(  ) LIMIT 50";
		quote_format($sql, 0);		//	output.php
	}
	template();
}
/*
####################
RANDOM QUOTES 2 (>0)
?random2 at the end of a uri: The user gets a random mix of all approved quotes, but the quotes' ratings are all greater than 1. No quotes with a rating of 0 or less will appear.
####################
*/
function random2()
{
	function content()
	{
		include('config.php');
		$sql = "SELECT * FROM `" . $quotetable . "` WHERE `approve` =1 AND `rating` >0 ORDER BY rand(  ) LIMIT 50";
		quote_format($sql, 0);		//	output.php
	}
	template();
}
/*
####################
TOP 150
?top150 at the end of a uri: Will limit the amount of quotes displayed to 150 and only show the 150 with the highest ratings.
####################
*/
function top150()
{
	function content()
	{
		include('config.php');
		$sql = "SELECT * FROM `" . $quotetable . "` WHERE `approve` =1 AND `rating` >0 ORDER BY `rating` desc LIMIT 150";
		quote_format($sql, 0);		//	output.php
	}
	template();
}
/*
####################
BOTTOM
?bottom at the end of a uri: Shows only quotes with a rating less than 0. Bash.org got rid of this feature, probably because all of the quotes in the bottom were racist.
####################
*/
function bottom()
{
	function content()
	{
		include('config.php');
		$sql = "SELECT * FROM `" . $quotetable . "` WHERE `approve` =1 AND `rating` <1 ORDER BY `rating` asc LIMIT 150";
		quote_format($sql, 0);		//	output.php
	}
	template();
}
/*
####################
QUOTES OF THE WEEK
?qotw at the end of a uri: Shows quotes that have been submitted in the past 7 days. It shows the date submitted and the time of submission. The information is grabbed from a unix timestamp that is stored with the quote. This timestamp can be used elsewhere if needed :).
####################
*/
function qotw()
{
	function content()
	{
		global $today;
		include('config.php');
		$second = date ('s');
		$minute = date ('i');
		$hour = date ('G');
		$day = date ('d');
		$month = date ('m');
		$year = date ('Y');
		$today = mktime ($hour,$minute,$second,$month,$day,$year);
		$sql = "SELECT * FROM `" . $quotetable . "` WHERE `approve` =1 AND `date` >= (" . $today . " - (60*60*24*7)) ORDER BY `id` desc";
		quote_format($sql, 1);				//	ouput.php
	}
	template();
}

/*
####################
ADD
?add at the end of a uri: Takes user to a page whose design is defined in output.php where a user can submit a quote to the submit database.
####################
*/
function add()
{
	function content()
	{
		add_quote_page_output();	//	output.php
	}
	template();
}
/*
####################
QUOTE ADDED
?added at the end of a uri: The workhorse behind add(), it takes the submission and puts it into the submit table.
####################
*/
function added()
{
	include('config.php');
	setcookie ("Add_Quote_Cookie", "1",time()+$cookie_time);
	function content()
	{
		session_start();
		include('config.php');
		if (!isset ($_SESSION['test']) || $_POST['test'] != $_SESSION['test']) {
			echo "Nie zdałeś testu. Pała :&lt;";
		}
		elseif (isset($_COOKIE["Add_Quote_Cookie"])) {
			if ($_COOKIE['Add_Quote_Cookie'] == '1'){
				echo $cookie_already_set;
			}else{
				$qQUOTE = $_POST['quote'];
				$qQUOTE = addslashes(htmlspecialchars($qQUOTE));
				$sql = "INSERT INTO `" . $subtable . "` ( `id` , `quote` ) VALUES ( '', '" . $qQUOTE . "' );";
				database_connect($sql);
				add_quote_confirmation($qQUOTE);	// output.php
				unset ($_SESSION['test']);
			}
		}else{
			$qQUOTE = $_POST['quote'];
			$qQUOTE = addslashes(htmlspecialchars($qQUOTE));
			$sql = "INSERT INTO `" . $subtable . "` ( `id` , `quote` ) VALUES ( '', '" . $qQUOTE . "' );";
			database_connect($sql);
			add_quote_confirmation($qQUOTE);	// output.php
			unset ($_SESSION['test']);
		}
	}
	template();
}
/*
####################
SEARCH
?search at the end of a uri: This takes a user to the page where they can put words in to search for quotes with those words in it. The design behind the actual search page is in output.php.
####################
*/
function search()
{
	function content()
	{
		search_quotes_page_ouput();	//	output.php
	}
	template();
}
/*
####################
SEARCHED
?searched at the end of a uri: This is the workhorse behind search(). It scans the approved quote database for quotes that have the word the user inputted in common with it.
####################
*/
function searched ()
{
	function content()
	{
		include('config.php');
		$quote = addslashes($_POST['search']);
		$sortby = $_POST['sortby'] == 'id' ? 'id' : 'rating';
		$number = (int)$_POST['number'];
		if($sortby == 'rating'){
			$how = 'desc';
		}
		if($sortby == 'id'){
			$how = 'asc';
		}
		$sql = "SELECT * FROM `" . $quotetable . "` WHERE `quote` LIKE '%" . $quote . "%' ORDER BY `" . $sortby . "` " . $how . " LIMIT " . $number;
		quote_format($sql, 0);		// output.php
	}
	template();
}
/*
####################
ADDUSER
?adduser at the end of a uri: Admin only function, adds a new user to the
####################
*/
function adduser()
{
	function content()
	{
		include('config.php');
		$nUSER = addslashes($_POST['newuser']);
		$nLEVEL = addslashes($_POST['usrlvl']);
		$nPASSWORD = md5('password');
		if(login_check(1)){
			if(empty($_POST['newuser'])){
				echo $no_username_set;
			}
			else{
				if(!isset($_POST['usrlvl'])){
					echo $no_userlevel_set;
				}
				else {
					$sql = "INSERT INTO `" . $rashusers . "` ( `id` , `user` , `level` , `password` ) VALUES ( '', '" . $nUSER . "', '" . $nLEVEL . "', '" . $nPASSWORD . "' );";
					database_connect($sql);
					echo $add_user_confirm;
				}
			}
		}
	}
	template();
}
/*
####################
ADMINADD
?adminadd at the end of a uri: Admin only function, admin adds a quote that bypasses the submit database and is put directly into the approved quote datbaase.
####################
*/
function adminadd()
{
	function content()
	{
		include('config.php');
		if(login_check(1)){
 			$qQUOTE = addslashes(htmlspecialchars($_POST['quote']));
			$second = date ('s');
			$minute = date ('i');
			$hour = date ('G');
			$day = date ('d');
			$month = date ('m');
			$year = date ('Y');
			$today = mktime ($hour,$minute,$second,$month,$day,$year);
			$sql = "INSERT INTO `" . $quotetable . "` ( `id` , `quote` , `rating` , `approve` , `check` , `date` ) VALUES ( '', '" . $qQUOTE . "', '0', '1', '1', '" . $today . "' );";
			database_connect($sql);
			add_quote_confirmation($qQUOTE);	// output.php
		}
	}
	template();
}

/*
####################
NEWS
?news at the end of a uri: Admin only function, admin updates the news table with latest news.
####################
*/
function news()
{
	function content()
	{
		include('config.php');
		$news = addslashes(htmlspecialchars($_POST['news']));
		$dbcnx = @mysql_connect("$hostname", "$username", "$dbpasswd");
		if(login_check(1)){
			$today = date("Y-m-d");
			$sql = "INSERT INTO `" . $newstable . "` ( `id` , `news` , `date` ) VALUES ( '', '" . $news . "', '" . $today . "' );";
			database_connect($sql);
			echo $add_news_confirm;
		}
	}
	template();
}

/*
####################
PASSWORD CHANGE
?password at the end of a uri: Admin only function, admin changes the password to their account by inputting two equal passwords into the prompt and this function validates that both are equal, then changes it to the new password.
####################
*/
function password()
{
	include('config.php');
	$rash_username = addslashes($_COOKIE['User_Name']);
	$uPASSWORD = md5($_POST['password']);
	$passwordchk = md5($_POST['passwordchk']);
	if(login_check(0)){
		if ($uPASSWORD == $passwordchk){
			$sql = "UPDATE `" . $rashusers . "` SET `password` = '" . $uPASSWORD . "' WHERE `user` = '" . $rash_username . "' ;";
			database_connect($sql);
			setcookie("User_Name", "", time()-100);
			setcookie("User_Password", "", time()-100);
			setcookie("User_Level", "", time()-100);
			setcookie("Logged_In", "", time()-100);
			header("Location: ./?admin");
		}
		else {
			echo $password_mismatch;
		} 
	}
	else{
		function content()
		{
			include('config.php');
			echo $not_logged_in;
		}
		template();
	}
}

/*
####################
QUOTE APPROVAL
?approve at the end of a uri: Admin only function, admin approves a quote which has been submitted to the submit database. After being approved, the quote is set to be checked, approved, and non-flagged in the approve database, thusly letting it be viewed with browse, latest, or whatever.
####################
*/
function approve()
{
	function content()
	{
		include('config.php');
		$qQUOTE = addslashes($_GET['quote']);
		if(login_check(1)){
			//hour,minute,second,month,day,year
			$today = mktime (date('G'),date('i'),date('s'),date('m'),date('d'),date('Y'));
			$sql = "INSERT INTO `" . $quotetable . "` ( `quote` , `rating` , `approve` , `check` , `date` ) VALUES ( '" . $qQUOTE . "', '0', '1', '1', '" . $today . "' );";
			database_connect($sql);
			$sql = "DELETE FROM `" . $subtable . "` WHERE `quote` = '" . $qQUOTE . "'";
			database_connect($sql);
			echo $approve_quote_confirm;
		}
	}
	template();
}
/*
####################
QUOTE DISAPPROVAL
?disapprove at the end of a uri: Admin only function, admin disapproves a quote from the submit database. It is deleted and never heard from again.
####################
*/
function disapprove()
{
	function content()
	{
		include('config.php');
		$qID = addslashes(round($_GET['id']));
		if(login_check(1)){
			$sql = "DELETE FROM `" . $subtable . "` WHERE `id` = '" . $qID . "'";
			database_connect($sql);
			echo $quote_deleted;
			mysql_close();
		}
	}
	template();
}
/*
####################
DELETE FLAGGED QUOTE
?delete at the end of a uri: Admin only function, deletes a quote based on the id given in the uri. This is used from the admin panel with flagged quotes and differs from adelete because it doesn't require a vote=1 in the uri. 
####################
*/
function delquote()
{
	function content()
	{
		include('config.php');
		$qID = addslashes(round($_GET['id']));
		if(login_check(1)){
			$sql = "DELETE FROM `" . $quotetable . "` WHERE `id` = '" . $qID . "'";
			database_connect($sql);
			echo $quote_deleted;
		}
	}
	template();
}

/*
####################
UNFLAG
?unflag at the end of a uri: Admin only function, unflagged a quote that has been flagged offensive/sucky by the flag function by a user.
####################
*/
function unflag()
{
	function content()
	{
		include('config.php');
		$qID = addslashes(round($_GET['id']));
		if(login_check(1)){
			$sql = "UPDATE `" . $quotetable . "` SET `check` = '1' WHERE `id` = '" . $qID . "' LIMIT 1 ;";
			database_connect($sql);
			echo $deflag_confirm;
		}
	}
	template();
}

/*
####################
FLAG
?flag at the end of a uri: User clicks on the (default) [X] link and it takes that quote and changes a cell in the approved quote table. This change is shown in the administation section to warn you that the quote is either bad or offensive. The admin can do whatever is needed at the time. Times allowed to do it limited by a cookie.
####################
*/
function flag()
{
	function content()
	{
		include('config.php');
		if($GLOBALS['flag_cookie_set']){
			$qID = addslashes(round($_GET['id']));
			$sql = "UPDATE `" . $quotetable . "` SET `check` = '0' WHERE `id` = '" . $qID . "' LIMIT 1 ;";
			database_connect($sql);
			echo $flag_confirm;
		}
		if(!$GLOBALS['flag_cookie_set']){
			echo $cookie_already_set;
		}
	}
	template();
}

/*
####################
RATINGMINUS
?ratingminus at the end of a uri: This demotes the ranking of a quote by 1. It can go below zero into the negative numbers. Times allowed to do it limited by a cookie.
####################
*/
function ratingminus()
{
	function content()
	{
		include('config.php');
		if(!isset($_COOKIE['cen']) || in_array(round($_GET['id']), isset ($_COOKIE['siusiak']) ? explode(';', $_COOKIE['siusiak']) : array())) {
			echo 'Już głosowałeś na ten cytat!';
			return;
		}
		if($GLOBALS['dg_cookie_set'] == '1'){
			$qID = addslashes(round($_GET['id']));
			setcookie('siusiak', isset ($_COOKIE['siusiak']) ? $_COOKIE['siusiak'] . ';' . $qID : $qID, time() + 3600 * 24 * 31 * 6, '/', '.cytaty.6irc.net');
			header("Location: ".$_SERVER['HTTP_REFERER']);
			$sql = "SELECT * FROM `" . $quotetable . "` WHERE `id` =" . $qID;
			$row = mysql_fetch_assoc(database_connect($sql));
			$sql = "UPDATE `" . $quotetable . "` SET `rating` = '" . ($row['rating']-=1) . "' WHERE `id` = '" . $qID . "';";
			database_connect($sql);
			echo $downgrade_confirm;
		}
		if(!$GLOBALS['dg_cookie_set']){
			echo $cookie_already_set;
		}
	}
	template();
}

/*
####################
RATINGPLUS
?ratingplus at the end of a uri: This promotes the ranking of a quote by 1. It can go below zero into the negative numbers. Times allowed to do it limited by a cookie.
####################
*/
function ratingplus()
{
	function content()
	{
		include('config.php');
		if(!isset($_COOKIE['cen']) || in_array(round($_GET['id']), isset ($_COOKIE['siusiak']) ? explode(';', $_COOKIE['siusiak']) : array())) {
			echo 'Już głosowałeś na ten cytat!';
			return;
		}
		if($GLOBALS['ug_cookie_set']){
			$qID = addslashes(round($_GET['id']));
			setcookie('siusiak', isset ($_COOKIE['siusiak']) ? $_COOKIE['siusiak'] . ';' . $qID : $qID, time() + 3600 * 24 * 31 * 6, '/', '.cytaty.6irc.net');
			header("Location: ".$_SERVER['HTTP_REFERER']);
			$sql = "SELECT * FROM `" . $quotetable . "` WHERE `id` = " . $qID;
			$row = mysql_fetch_array(database_connect($sql));
			$sql = "UPDATE `" . $quotetable . "` SET `rating` = '" . ($row['rating']+=1) . "' WHERE `id` = '" . $qID . "' ;";
			database_connect($sql);
			echo $upgrade_confirm;
		}
		if(!$GLOBALS['ug_cookie_set']){
			echo $cookie_already_set;
		}
	}
	template();
}
/*
####################
ADMIN QUOTE DELETE
?adelete at the end of a uri: Admin only function, this function makes you verify that you want to delete a quote. It is used next to quotes themselves as they're outputted.
####################
*/
function adelete()
{
	function content()
	{
		if(login_check(1)){
			$qID = addslashes($_GET['id']);
			if($_GET['vote']){
				include('config.php');
				$sql = "DELETE FROM `" . $quotetable . "` WHERE `id` = '" . $qID . "'";
				database_connect($sql);
				echo $admin_del_confirm;
			}
			else{
				echo "Are you sure you want to delete quote " . $qID . "?<br />";
				echo "<a href=\"?adelete&vote=1&id=" . $qID . "\" style=\"text-decoration: none\">[Yes]</a>&nbsp;";
				echo "<a href=\"?" . $qID . "\" style=\"text-decoration: none\">[No]</a>";
			}
		}
	}
	template();
}

/*
####################
NEWS DELETE
?news_delete at the end of a uri: Admin only function, lets an admin delete a news post from the database completely.
####################
*/
function news_delete()
{
	function content()
	{
		if(login_check(1)){
			$nID = addslashes($_GET['id']);
			if($_GET['vote']){
				include('config.php');
				$sql = "DELETE FROM `" . $newstable . "` WHERE `id` = '" . $nID . "'";
				database_connect($sql);
				echo $admin_del_confirm;
			}
			else{
				include('config.php');
				$sql = "SELECT `news` FROM `" . $newstable . "` WHERE `id` = '" . $nID . "'";
				$result = database_connect($sql);
				while($row = mysql_fetch_assoc($result)){
					echo "Are you sure you wish to delete news item \"" . substr($row['news'], 0, 10) . "...\"?<br />";
					echo "<a href=\"?adelete&vote=1&id=" . $nID . "\" style=\"text-decoration: none\">[Yes]</a>&nbsp;";
					echo "<a href=\"./\" style=\"text-decoration: none\">[No]</a>";
				}
			}
		}
	}
	template();
}


/*
###################
ADMIN EDIT QUOTE
?aedit at the end of a uri: Admin only function, this function allows an admin to edit the contents and ranking of a quote. 
###################
*/
function aedit()
{
	function content()
	{
		if(login_check(1)){
			if($_POST['editresult']){
				global $sql;
				include('config.php');
				$qID = addslashes($_POST['id']);
				$qQU = addslashes($_POST['quote']);
				$qRA = addslashes($_POST['rating']);
				$qQU = addslashes(htmlspecialchars($qQU));
				$sql = "UPDATE `" . $quotetable . "` SET `quote` = '" . $qQU . "', `rating` ='" . $qRA . "' WHERE `id` = '" . $qID . "';";
				database_connect($sql);
				echo $quote_update_confirm;
			}
			else{
				$qID = addslashes($_GET['id']);
				include('config.php');
				$sql = "SELECT * FROM `" . $quotetable . "` WHERE `id` = '" . $qID . "'";
				quote_edit($sql);  // output.php
			}
		}
	}
	template();
}

/*
####################
NEWS EDIT
?news_edit at the end of a uri: Admin only function, this function allows an admin to edit a news post based on the id of the news post automatically generated in the uri.
####################
*/
function cdelete()
{
		if(login_check(1)){
				include('config.php');
				$nID = addslashes((int)$_GET['id']);
				$sql = "DELETE FROM `" . $commentstable . "` WHERE  `id` = '" . $nID . "';";
				database_connect($sql);
				header("Location: $_SERVER[HTTP_REFERER]");
		}
}


/*
####################
NEWS EDIT
?news_edit at the end of a uri: Admin only function, this function allows an admin to edit a news post based on the id of the news post automatically generated in the uri.
####################
*/
function news_edit()
{
	function content()
	{
		if(login_check(1)){
			if($_POST['editresult']){
				include('config.php');
				$nID = addslashes($_POST['id']);
				$nCO = addslashes($_POST['news']);
				$nDA = addslashes($_POST['date']);
				$nCO = addslashes(htmlspecialchars($nCO));
				$sql = "UPDATE `" . $newstable . "` SET  `news` = '" . $nCO . "', `date` ='" . $nDA . "' WHERE `id` = '" . $nID . "';";
				database_connect($sql);
				echo $news_update_confirm; // change
			}
			else{
				include('config.php');
				$nID = addslashes($_GET['id']);
				$sql = "SELECT * FROM `" . $newstable . "` WHERE `id` = '" . $nID . "'";
				news_change($sql); // output.php
			}
		}
	}
	template();
}

function tripcode($plain)
{
    $salt = substr($plain."H.",1,2);
    $salt = ereg_replace("[^\.-z]",".",$salt);
    $salt = strtr($salt,":;<=>?@[\\]^_`","ABCDEFGabcdef"); 
    return substr(crypt($plain,$salt),-10);
}

function commentadd() {
	function content() {
		session_start();
		include('config.php');
		if (!isset($_SESSION['test']) || $_POST['test'] != $_SESSION['test']) {
			echo ("Test nie zdany :< Pała!");
		}
		else {
			unset ($_SESSION['test']);
			$text = addslashes(htmlspecialchars($_POST['text']));
			$n = explode('#', $_POST['auth'], 2);
			if (isset ($n[1])) {
				$pass = addslashes(tripcode($n[1]));
			}
			else {
				$pass = '';
			}
			if (!$n[0] && !$pass) {
				$login = 'Anonymous';
			}
			else {
				$login = addslashes(htmlspecialchars($n[0]));
			}
			database_connect(sprintf(
				'insert into `%s` (of,user,passwd,ip,ts,text) values'.
				"('%d', '%s', '%s', '%s', '%d', '%s')",
				$commentstable, $_POST['of'], $login, $pass, $_SERVER['REMOTE_ADDR'], time(), $text
			));
			echo("Komentarz dodany poprawnie.");
			echo ("<script type='text/javascript'>/*<![CDATA[*/ document.location.replace(\"".str_replace('"', '\\"', $_SERVER['HTTP_REFERER'])."\"); /*]]>*/</script>");
		}
	}
	template();
}

/*
####################
Switch system. Page changer. Whatever.
Checks the query_string (http://bla.com/?this_right_here) of URI's and choosed the function to run from that. In URI's with complex query_strings, the query_string is cut to what is needed to verify what function should run. If there is no query_string it defaults to the home function. If there is a query string and it is not found, it will act as if it's a quote number.
*/
$query = $_SERVER['QUERY_STRING'];
$query = explode(';', $query);
$query = $query[0];

switch ($query) {
	case 'latest':
		$section='2';
		latest();
		break;
	case 'browse':
		$section='3';
		browse();
		break;
	case 'random':
		$section='4';
		random();
		break;
	case 'random2':
		$section='4';
		random2();
		break;
	case 'top150':
		$section='5';
		top150();
		break;
	case 'bottom':
		$section='6';
		bottom();
		break;
	case 'qotw':
		$section='9';
		qotw();
		break;
	case 'add':
		$section='7';
		add();
		break;
	case 'added':
		$section='7';
		added();
		break;
	case 'search':
		$section='8';
		search();
		break;
	case 'searched':
		$section='8';
		searched();
		break;
	case 'adduser':
		$section='0';
		adduser();
		break;
	case 'adminadd':
		$section='0';
		adminadd();
		break;
	case 'cdelete':
		$section='0';
		cdelete();
		break;
	case 'news':
		$section = '0';
		news();
		break;
	case 'password':
		$section = '0';
		password();
		break;
	case 'commentadd':
		$section = '3';
		commentadd();
		break;
	case 'logout':
		setcookie("User_Name", "", time()-100);
		setcookie("User_Password", "", time()-100);
		setcookie("User_Level", "", time()-100);
		setcookie("Logged_In", "", time()-100);
		header("Location: ./");
	case 'admin':
		include('config.php');
		if(login_check(0)){
			adminpanel();	//	(template)_admin.php in templates folder by default
			exit();
		}
		$section='0';
		admin();
		break;

	case 'quotesearch':
		$qNUMBER = addslashes($_POST['qSEARCH']);
		header("Location: ./?" . $qNUMBER);
		break;
	
	case 'adminlogin':
		include('config.php');
		$sql = "SELECT * FROM `" . $rashusers . "` WHERE `user` ='" . addslashes($_POST['user']) . "' AND `password` ='" . md5($_POST['password']) . "'";
		while($row = mysql_fetch_array(database_connect($sql))){
			$section='0';
			setcookie("User_Name", $row['user']);
			setcookie("User_Password", $row['password']);
			setcookie("User_Level", $row['level']);
			setcookie("Logged_In", md5($row['user']));
			adminpanel();  //	(template)_admin.php in templates folder by default
			exit();
		}
		function content()
		{
			global $loginfail;
			echo $loginfail;
		}
		template();
		break;
		
	default:
		$approve = substr($_SERVER['QUERY_STRING'] , 0, 7);
		$disapprove = substr($_SERVER['QUERY_STRING'] , 0, 10);
		$flag = substr($_SERVER['QUERY_STRING'] , 0, 4);
		$delete = substr($_SERVER['QUERY_STRING'] , 0, 6);
		$unflag = substr($_SERVER['QUERY_STRING'] , 0, 6);
		$ratingminus = substr($_SERVER['QUERY_STRING'] , 0, 11);
		$ratingplus = substr($_SERVER['QUERY_STRING'] , 0, 10);
		$adelete = substr($_SERVER['QUERY_STRING'] , 0, 7);
		$aedit = substr($_SERVER['QUERY_STRING'] , 0, 5);
		$news_edit = substr($_SERVER['QUERY_STRING'] , 0, 9);
		$news_delete = substr($_SERVER['QUERY_STRING'] , 0, 11);
		if ($ratingplus == "ratingplus"){
			if(!isset($_COOKIE['Upgrade_Cookie'])){
				$GLOBALS['ug_cookie_set'] = '1';
				setcookie ("Upgrade_Cookie", "1",time()+$cookie_time);
			}
			if(isset($_COOKIE['Upgrade_Cookie'])){
				$GLOBALS['ug_cookie_set'] = '0';
			}
			$section = '10';
			ratingplus();
			break;
		}
		
		if ($ratingminus == "ratingminus"){
			if(!isset($_COOKIE['Downgrade_Cookie'])){

				$GLOBALS['dg_cookie_set'] = '1';
				setcookie ("Downgrade_Cookie", "1",time()+$cookie_time);
			}
			if(isset($_COOKIE['Downgrade_Cookie'])){
				$GLOBALS['dg_cookie_set'] = '0';
			}
			$section = '11';
			ratingminus();
			break;
		}

		if ($unflag == "unflag"){
			$section = '0';
			unflag();
			break;
		}
		if($delete == "delete"){
			$section = '0';
			delquote();
			break;
		}

		if($flag == "flag"){
			if(!isset($_COOKIE['Flag_Quote_Cookie'])){
				$GLOBALS['flag_cookie_set'] = '1';
				setcookie ("Flag_Quote_Cookie", "1",time()+$cookie_time);
			}
			if(isset($_COOKIE['Flag_Quote_Cookie'])){
				$GLOBALS['flag_cookie_set'] = '0';
			}
			$section = 'Flag Quote';
			flag();
			break;
		}

		if($disapprove == "disapprove"){
			$section = '0';
			disapprove();
			break;
		}
		if($approve == "approve"){
			$section = '0';
			approve();
			break;
		}
		if($adelete == 'adelete'){
			$section = '0';
			adelete();
			break;
		}
		if($aedit == 'aedit'){
			$section = '0';
			aedit();
			break;
		}
		if($news_edit == 'news_edit'){
			$section = '0';
			news_edit();
			break;
		}
		if($news_delete == 'news_delete'){
			$section = '0';
			news_delete();
			break;
		}
		if($_SERVER['QUERY_STRING'] == 0){
			$section='1';
			home();
			break;
		}
		else{
			$section='3';
			quote();
			break;
		}
}
?>
