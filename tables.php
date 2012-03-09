<!--
/*
RQMS - Rash Quote Management System
Copyright (C) 2003-2004 Tom Cuchta (tommah@instable.net / http://www.mastergoat.com) and Instable Network (p00p@instable.net / http://www.instable.net)

http://rqms.sourceforge.net

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
  <title>
   Rash.org
  </title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
 </head>
 <body>
<?php
include('config.php');
$link = mysql_connect("$hostname", "$username", "$dbpasswd")
	or die($noservercnx . mysql_error());
if (! @mysql_select_db("$dbname") ) {
	echo $nodbcnx;
	exit();
}

$sql = 'CREATE TABLE `' . $subtable . '` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote` text NOT NULL,
  PRIMARY KEY (`id`)
);';
mysql_query($sql)
	or die("The table " . $subtable . " did not create succesfully, this is the error outputted by the sql server: " . mysql_error());
echo "Victory! " . $subtable . " submit table has been installed successfully!<br />";

$sql = 'CREATE TABLE `' . $rashusers . '` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text NOT NULL,
  `password` text NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;';
mysql_query($sql)
	or die("The table " . $rashusers . " did not create succesfully, this is the error outputted by the sql server: " . mysql_error());
echo "Victory! " . $rashusers . " has been installed successfully! Two more to go!<br />";
$sql = 'CREATE TABLE `' . $quotetable . '` ( 
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote` text NOT NULL,
  `rating` int(11) NOT NULL,
  `approve` int(11) NOT NULL,
  `check` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);';
mysql_query($sql)
	or die("The table " . $quotetable . " did not create succesfully, this is the error outputted by the sql server: " . mysql_error());
echo "Victory! " . $quotetable . " has been created! One more to go!<br />";
$sql = 'CREATE TABLE `' . $newstable .  '` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news` text NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ;';
mysql_query($sql)
	or die("The table " . $commentstable . " did not create succesfully, this is the error outputted by the sql server: " . mysql_error());
echo "Victory! " . $commentstable . " has been created!<br />";
$sql = 'CREATE TABLE `' . $commentstable . '` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `of` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `passwd` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `ts` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `of` (`of`),
  KEY `ts` (`ts`,`ip`)
) ;';
mysql_query($sql)
	or die("The table " . $commentstable . " did not create succesfully, this is the error outputted by the sql server: " . mysql_error());
echo "Victory! " . $commentstable . " has been created!<br />";

$sql = 'INSERT INTO `' . $rashusers . '` (
`id` , `user` , `password` , `level` ) VALUES ( \'\', \'administrator\', \'5f4dcc3b5aa765d61d8327deb882cf99\', \'1\' );';
mysql_query($sql)
	or die("Fuck<Br />" . mysql_error());
echo "Well, I guess it worked?<br />"

?>
Please log into the administration section at http://www.yourdomain.com/rash/?admin  or by clicking the hidden link to the right of QMS in the top left hand corner of the page (if you're using a default template) with the User Name: administrator and Password: password . <br />
REMEMBER THE USERNAME AND PASSWORD ARE CASE SENSITIVE.
 </body>
</html>
