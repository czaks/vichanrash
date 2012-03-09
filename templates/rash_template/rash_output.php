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

function front_page_output(){		// Usually the first page you see. It's configured for use as Bash.org and the default Bash
	global $section;				// And Rash templates. Change at your own will.
	$welcomecolumn = '1';			// Turn this to zero to turn it off
	$newscolumn = '1';				// Turn this to zero to turn it off
	include('config.php');
	if($welcomecolumn){
?>
		<table id="news_format">
			<tr>
				<!--<td style="width:40%" valign="top">
					<div id="frontpage_left">
						<p><span style="font-weight:bold">Witaj!</span> Costam costam!
					</div>
				</td>-->
				<td style="width:100%" valign="top"><!-- was 40% -->
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
			echo ("<span class=\"news_date\">" . $row["date"] . "</span>\n"); 
			if(login_check(0)){
				echo(	"<a href=\"/news_edit" . $GET_SEPARATOR_HTML . "id=" . $row['id'] . "\">[Edit]</a>&nbsp;\n" .
						"<a href=\"/news_delete" . $GET_SEPARATOR_HTML . "id=" . $row['id'] . "\">[Delete]</a>\n");
			}
			echo nl2br("<div class=\"news_post_format\">" . $row["news"] . "</div>\n");
		}
?>					</div>
				</td>
			</tr>
		</table>
<?php
	}
}


#############
##ADMIN LOGIN
#############

function preadmin_output(){								//	Displayed at ./?admin IF the user is not logged in		Query for admin/mod login.
?>
       Witaj!<br />
								
       Q: Co jest śmieszniejszego od martwego noworodka?<br />
       A: Martwy noworodek w stroju klauna.<br />
       Q: A co jest śmieszniejszego od martwego noworodka w stroju klauna?<br />
       A: Nic...<br />

       <form method="post" action="/adminlogin">
        User Name: <input type="text" name="user" size="28" class="grey_input"><br />
        <p>
         Password:&nbsp;&nbsp; <input type="password" name="password" size="28" class="grey_input"><br />
        </p>
        <input type="submit" name="submit">
       </form>
<?php
}

################
##ADD QUOTE PAGE
################

function add_quote_page_output(){						//	Displayed at ./?add		Query for users to add quotes
?>
		Cytat dodajesz w formie IRC-owej (&lt;nick&gt; co powiedział), bez znaczników czasu, chyba, że są one istotne.
		<form method="post" action="/added">
			<textarea cols="80" rows="5" name="quote" class="grey_textarea"></textarea><br />
			Test na inteligencję <img src='generate.php' />: <input type='text' name='test' />
			<input type="submit" value="Dodaj cytat">
			<br />
		</form>
<?php
}

####################
##SEARCH QUOTES PAGE
####################

function search_quotes_page_ouput(){					//	Displayed at ./?search		The search query for the user
?>
       <form method="post" action="/searched">
        <input type="text" name="search" size="28" class="grey_input">
        <input type="submit" name="submit"><br />
        Sortuj cytaty wg.: <select name="sortby" size="1">
         <option selected='' value='rating'>oceny</option>
         <option value='id'>id</option>
        </select>&nbsp;&nbsp;&nbsp;&nbsp;
		Ile cytatów wyświetlić?: <select name="number" size="1">
         <option selected>10
         <option>25
         <option>50
         <option>75
         <option>100
        </select>
       </form>
<?php
}


########################
##ADD QUOTE CONFIRMATION
########################

function add_quote_confirmation($qQUOTE){
	include('config.php');
?>
		Dziękujemy, Twój cytat został dodany.<br />
		Dla weryfikacji, Twój cytat to:<br />
		<div class="quote_output">
<?=nl2br($qQUOTE)?>
		</div>
		Jeżeli to nie jest cytat, który chciałeś/aś wysłać, bądź popełniłeś/aś jakiś błąd, wyślij ten, który miał zostać wysłany. W przypadku duplikatów, tylko najnowsze będą mogły być zatwierdzone.<br />Dziękujemy.
<?php
}

##############
##QUOTE FORMAT
##############

function quote_format($sql, $qotw_check){			//	Displayed anywhere quotes are returned.
	include('config.php');
	global $qID;
	$result = database_connect($sql);
	while($row = mysql_fetch_array($result)){
?>
	<div class='topof'>
		<a href="/<?=$row['id']?>">#<?=$row['id']?></a>
		<a href="/ratingplus<?=$GET_SEPARATOR_HTML?>id=<?=$row['id']?>" class="quote_ratingplus">+</a>
		(<?=$row['rating']?>)
		<a href="/ratingminus<?=$GET_SEPARATOR_HTML?>id=<?=$row['id']?>" class="quote_ratingminus">-</a>
		<a href="/flag<?=$GET_SEPARATOR_HTML?>id=<?=$row['id']?>" class="quote_flag">x</a>
		(<?=date("d-m-Y", $row['date'])?> <?=date("H:i:s", $row['date'])?>)
		<?php
			$ssql = 'select count(*) as count from '.$commentstable.' where `of`=\''.$row['id'].'\'';
			$res = database_connect($ssql);
			$rw = mysql_fetch_array($res);
			echo mysql_error();

			printf ('<a href="/%d">[%d komentarzy]</a>',$row['id'], isset($rw['count']) ? $rw['count'] : -1);
		?>
<?
		if(login_check(0)){
?>
		<a href="/adelete<?=$GET_SEPARATOR_HTML?>id=<?=$row['id']?>" class="quote_adelete">[Usuń]</a>
		<a href="/aedit<?=$GET_SEPARATOR_HTML?>id=<?=$row['id']?>" class="quote_aedit">[Edytuj]</a>
<?
		}
?>
		</div>
		<div class="quote_output">
			<?=nl2br($row["quote"] . "\n");?>
		</div>
<?
	}
}

function quote_full($sql, $sqc) {
	include('config.php');
	global $qID;

	quote_format($sql, 0);

	$comments = database_connect($sqc);
	while($row = mysql_fetch_array($comments)){
		?>
		<div class='topof'>#<?php echo $row['id']; ?> <strong><?php echo $row['user']; ?></strong><?php echo $row['passwd'] ? ':'.$row['passwd'] : ''; ?> (<?=date("d-m-Y", $row['ts'])?> <?=date("H:i:s", $row['ts'])?>)<?
			if(login_check(0)){
			?>, adres IP: <?php echo $row['ip']; ?> <a href="/cdelete<?=$GET_SEPARATOR_HTML?>id=<?=$row['id']?>" class="quote_adelete">[Usuń]</a>
			<?php
			}
		?></div>
		<div class="quote_output">
			<?php echo nl2br($row["text"] . "\n");?>
		</div>
	<?php }?>

		<form method='post' action='/commentadd' style='background-color: #e4ffd1'>
			<input type='hidden' name='of' value='<?php echo $_SERVER['QUERY_STRING']; ?>' />
			<div class='topof' style='font-weight: bold;'>Dodaj komentarz:</div>
			Login#Hasło: <input type='text' name='auth' class="grey_input" /><br />
			<textarea name='text' class="grey_textarea" cols='80' rows='5'></textarea><br />
			Test na inteligencję <img src='/generate.php' />: <input type='text' name='test' class="grey_input" /><input type='submit' value='Wyślij' /><br />
<!--			<em><br />Pole Login#hasło możemy wypełnić na dwa sposoby:<br />a) bez autoryzacji (tj. sam login), pod taki nick bardzo łatwo się podszyć. Historia z komentarzami na Trawek's Comix pokazuje, że osób chętnych do takiego czynu jest bardzo dużo ;).<br />b) z autoryzacją (w formie: login#hasło) - kiedy pod login podszyć się można nadal, hasło jest nieodszyfrowalnie szyfrowane i taki kod jest wyświetlany obok nicka. Dane hasło ZAWSZE zwraca taki sam kod (nawet na 4chanie i podobnych!), dzięki czemu inni będą mogli Cię zidentyfikować. Mimo to, jest szansa, że zdeterminowany uzdolnięty hakier dosyć proste hasło złamie - z tego powodu polecam używać hasła <b>o długości 7-8 znaków zawierające duże i małe litery</b>, którego nie używasz w żadnym istotnym miejscu. Co się stanie, jeżeli hasło jednak zostanie złamane? Właściwie tyle tylko, że hakier będzie mógł pisać z Twojego nicka i kodu. Nic strasznego. Tak więc nie wprowadzamy żadnych ograniczeń co do hasła, jednak zastosowany algorytm do generacji kodu stosuje tylko 8 pierwszych bajtów (hasła: kochammyche i kochammydlo zwrócą taki sam kod!).</em>-->
			<em>Tripy są obsługiwane</em>
		</form>
		<?php
}

############
##QUOTE_EDIT
############
function quote_edit($sql){
	include('config.php');
	$result = database_connect($sql);
	while($row = mysql_fetch_array($result)){
?>
		<form action="/aedit" method="post">
			<textarea cols="70" rows="15" name="quote" class="grey_textarea">
<?=$row['quote']?>
			</textarea><br />
			Edytuj cytat.<br /><br />
			<input type="text" size="4" name="rating" class="grey_input" value="<?=$row['rating']?>"><br />
			Zmień ocenę cytatu.<br /><br />
			<input type="submit" value="Submit">
			<input type="hidden" value="1" name="editresult">
			<input type="hidden" value="<?=$row['id']?>" name="id">
		</form>
<?php
	}
}

############
##NEWS_CHANGE
############
function news_change($sql){
	include('config.php');
	$result = database_connect($sql);
while($row = mysql_fetch_array($result)){
?>
		<form action="/news_edit" method="post">
			<textarea cols="70" rows="15" name="news" class="grey_textarea">
<?=$row['news'] . "\n"?>
			</textarea><br />
			Edytuj newsa.<br /><br />
			<input type="text" size="4" name="date" class="grey_input" value="<?=$row['date']?>"><br />
			Edytuj datę newsa.<br /><br />
			<input type="submit" value="Submit">
			<input type="hidden" value="1" name="editresult">
			<input type="hidden" value="<?=$row['id']?>" name="id">
		</form>
<?php
	}
}
?>
