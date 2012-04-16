<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                //
// http://www.nuked-klan.org                                              //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify   //
// it under the terms of the GNU General Public License as published by   //
// the Free Software Foundation; either version 2 of the License.         //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")) {
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $language;
include("modules/Dispo/lang/" . $language . ".lang.php");
// tableau des noms des jours
$jour = array ("null", "" . _LUNDI . "", "" . _MARDI . "", "" . _MERCREDI . "", "" . _JEUDI . "", "" . _VENDREDI . "", "" . _SAMEDI . "", "" . _DIMANCHE . "");
// selection du niveau d'administration et d'utilisation du module
$sql = mysql_query("select niveau, admin from " . MODULES_TABLE . "  WHERE nom='Dispo'");
list($niveau, $admin) = mysql_fetch_array($sql);
if (!$user) {
    $user[1] = "0";
    $user[2] = "Anonym";
} 

if ($user[1] >= $niveau && $niveau > -1) {
    function index($pseudo)
    {
        global $theme, $nuked, $user, $jour, $niveau, $admin, $bgcolor1, $bgcolor2, $bgcolor3;

        opentable(); 
        // couleurs des differents types de disponibilité
        $coldispo = "#32FF32";
        $colindispo = "#FF3232";
        $colptet = "#FF8832";
        $colvac = "#8888ff";

        $today_heure = date(G);
        $today_jour = date(w);

        $num = array();
        $ligne = array(); 
        // selection des disponibilités correspondant au pseudo
        $sql = "SELECT DISPO.id,lun,mar,mer,jeu,ven,sam,dim,vac FROM $nuked[prefix]" . _dispo . " AS DISPO, " . USER_TABLE . " AS USER  WHERE DISPO.id=USER.id AND USER.pseudo='$pseudo' ";
        $req = mysql_query($sql) or die("" . _ERRORSQL . "<br />" . $sql . "<br />" . mysql_error());
        $nb = mysql_num_rows($req); 
        // Si il y a des dispo pour ce pseudo
        if ($nb > 0 || ($user[1] >= $admin) || ($user[2] == $pseudo)) {
            if ($nb == 0) $_REQUEST["op"] = "edit";
            while ($data = mysql_fetch_array($req)) {
                $test = array($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8]);
            } 
            echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"1\"><tr><td align=\"center\">";
            echo "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td align=\"right\">"; 
            // si administrateur du module ou ses propres dispos
            if (($user[1] >= $admin) || ($user[2] == $pseudo)) {
                echo"<script type=\"text/javascript\">\n"
                 . "<!--\n"
                 . "\n"
                 . "function deldispo(pseudo)\n"
                 . "{\n"
                 . "if (confirm('" . _DELETEDISPO . " '+pseudo+' ! " . _CONFIRM . "'))\n"
                 . "{document.location.href = 'index.php?file=Dispo&op=del_dispo&pseudo='+pseudo;}\n"
                 . "}\n"
                 . "\n"
                 . "// -->\n"
                 . "</script>\n";

                echo "<div style=\"text-align: right;\">";
                if ($_REQUEST["op"] != "edit") echo "<a href=\"index.php?file=Dispo&op=edit&pseudo=" . $pseudo . "\"><img style=\"border: 0;\" src=\"images/edition.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a>";
                else if ($test[8]) echo "<a href=\"index.php?file=Dispo&op=pref_add&vac=off&pseudo=" . $pseudo . "\"><img style=\"border: 0;\" src=\"images/back.gif\" alt=\"\" title=\"" . _MODVAC . " OFF\" /></a>";
                else echo "<a href=\"index.php?file=Dispo&op=pref_add&vac=on&pseudo=" . $pseudo . "\"><img style=\"border: 0;\" src=\"images/away.gif\" alt=\"\" title=\"" . _MODVAC . " ON\" /></a>";
                echo "&nbsp;<a href=\"javascript:deldispo('" . $pseudo . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DEL . "\" /></a></div>\n";
            } 
            echo "</td></tr><tr><td align=\"center\"><big><b>";
            echo "$nuked[name]<br />" . _DISPOOF_ . " " . $pseudo . "<br /><br />";
            echo "</b></big></td></tr>";
            if ($_REQUEST["op"] == "edit" && (($user[1] >= $admin) || ($user[2] == $pseudo))) {
                echo "<tr><td align=\"center\">(" . _INF . ")<br /><br /></td></tr>";
            } 
            echo "</table>";

            echo "
            <table width=\"100%\" style=\"background: " . $bgcolor3 . ";margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"0\"><tr><td style=\"background: " . $bgcolor1 . ";\">
            <table width=\"100%\"  border=\"0\" cellspacing=\"1\" cellpadding=\"2\">
			<tr style=\"background: " . $bgcolor3 . ";\"><td>&nbsp;</td>"; 
            // liste des horaires
            for ($i = 0;$i < 10;$i++) {
                echo "<td align=\"center\"><b>0" . $i . "<br />H</b></td>";
            } 
            for ($i = 10;$i < 24;$i++) {
                echo "<td align=\"center\"><b>" . $i . "<br />H</b></td>";
            } 
            // suite
            echo "</tr>";

            for ($j = 1;$j < 8;$j++) {
                echo "<tr style=\"background: " . $bgcolor3 . ";\"><td><b>$jour[$j]</b></td>";
                for ($i = 0;$i < 24;$i++) {
                    if ($today_jour == 0) $today_jour = 7;
                    if ($i == $today_heure && $j == $today_jour) $ok = "border: 1px solid #ffffff;";
                    else $ok = "";
                    $num[$i] = substr($test[$j], $i, 1);
                    if ($test[8] && $_REQUEST["op"] != "edit") {
                        $color = $colvac;
                    } else if ($num[$i] == 1) {
                        $color = $coldispo;
                    } else if ($num[$i] == 2) {
                        $color = $colptet;
                    } else {
                        $color = $colindispo;
                    } 
                    if ($_REQUEST["op"] == "edit" && (($user[1] >= $admin) || ($user[2] == $pseudo))) {
                        $edition = "onmouseover=\"this.style.backgroundColor='#000000'; this.style.cursor='hand';\" onmouseout=\"this.style.backgroundColor='" . $color . "'\" onClick=\"javascript:window.open('index.php?file=Dispo&nuked_nude=index&op=modif&pseudo=$pseudo&h=$i&j=$j&vac=$vac','popup','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=170');return(false)\"";
                    } 
                    echo "<td style=\"background: " . $color . "; width: 4%; " . $ok . "\" $edition >&nbsp;</td>";
                } 
                echo "</tr>";
            } 
            echo "</table></td></tr></table>";

            echo "  <br />
			<table cellspacing=\"0\" cellpadding=\"0\"><tr><td align=\"center\">			
			<fieldset style=\"border: 1px solid " . $bgcolor3 . "; text-align: left; padding: 3; width: auto; margin: auto;\">
			<legend style=\"color: " . $bgcolor3 . ";\">&nbsp;" . _LEG . "&nbsp;</legend> 			
            <table  cellspacing=\"1\" cellpadding=\"2\"><tr style=\"background: " . $bgcolor2 . ";\">
			<td><span style=\"color: " . $colindispo . ";\">" . _RED . "</span></td><td>" . _INDISP . "</td></tr><tr style=\"background: " . $bgcolor2 . ";\">
			<td><span style=\"color: " . $colptet . ";\">" . _ORANGE . "</span></td><td>" . _VAR . " (" . _VARDSC . ")<br /></td></tr><tr style=\"background: " . $bgcolor2 . ";\">
			<td><span style=\"color: " . $coldispo . ";\">" . _GREEN . "</span></td><td>" . _DISP . "</td></tr><tr style=\"background: " . $bgcolor2 . ";\">
			<td><span style=\"color: " . $colvac . ";\">" . _BLU . "</span></td><td>" . _VAC . "</td></tr></table>
			</fieldset></td></tr></table>";

            echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Team\"><b>" . _BACK . "</b></a> ]</div></td></tr></table>";
        } else {
            echo"<br /><br /><div style=\"text-align: center;\">" . _NODISP . "</div><br />";
            redirect("index.php?file=Team", 2);
        } 

        CloseTable();
    } 

    function modif($pseudo)
    {
        global $bgcolor2, $theme, $nuked, $user, $admin;
		$d = $_REQUEST["d"];
		$h = $_REQUEST["h"];
		$t = $_REQUEST["t"];
		$j = $_REQUEST["j"];
		$jour = $_REQUEST["jour"];
		$var = $_REQUEST["vac"];
		$load = $_REQUEST["load"];
        // si administrateur du module ou ses propres dispos
        if (($user[1] >= $admin) || ($user[2] == $pseudo)) {
            if ($load) $onload = "onload=\"opener.location.replace('index.php?file=Dispo&op=pref_add&pseudo=$pseudo&h=$h&d=$d&t=$t&j=$j&vac=$vac');self.close();opener.focus();\"";
            echo"	<head><title>" . _DISPO_ . "</title></head>
	<meta http-equiv=\"content-style-type\" content=\"text/css\">
	<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/$theme/style.css\">
	<body bgcolor=\"$bgcolor2\" $onload>";

            echo "<table width='100%'  border='0' cellspacing='0' cellpadding='0'>
	<tr><td>&nbsp</td></tr>
	<tr><td align=\"center\"><big><b>";
            echo "$nuked[name]<br />" . _DISPOOF_ . " $pseudo " . _APA . " $h H $jour[$j]";
            echo "</tr></td></table><br />";

            if (!isset($d)) $d = 1;
            if (!isset($t)) $t = 1;

            echo" 	<table width=\"100%\"><tr><td align=\"center\">
	<p align=\"center\">
	<form name=\"myForm\" action=\"\">
	Durée : <select name=\"t\">";
            for ($z = 1;$z <= 24 - $h;$z++) {
                echo"	<option value=\"$z\"";
                if ($t == $z) echo"selected";
                echo"	>$z H</option>";
            } 
            echo"	</select><br />
	" . _DISPO_ . " : 
	<select name=\"d\">
	<option value=\"1\"";
            if ($d == 1) echo"selected";
            echo">" . _DISP . "</option>
	<option value=\"2\"";
            if ($d == 2) echo"selected";
            echo">" . _VAR . "</option>
	<option value=\"0\"";
            if ($d == 0) echo"selected";
            echo">" . _INDISP . "</option>
	</select><br />
	<input name=\"\" type=\"submit\" value=\"" . _SUBMIT . "\">
	<input type=\"hidden\" name=\"file\" value=\"Dispo\">
	<input type=\"hidden\" name=\"op\" value=\"modif\">
	<input type=\"hidden\" name=\"nuked_nude\" value=\"index\">
	<input type=\"hidden\" name=\"pseudo\" value=\"$pseudo\">
	<input type=\"hidden\" name=\"h\" value=\"$h\">
	<input type=\"hidden\" name=\"j\" value=\"$j\">
	<input type=\"hidden\" name=\"vac\" value=\"$vac\">
	<input type=\"hidden\" name=\"load\" value=\"1\">
	</form></p>
	</td></tr></table>";

           
        } else echo" <br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /></div>";
    } 

    function pref_add($pseudo)
    {
        global $nuked, $user, $admin; 
		$d = $_REQUEST["d"];
		$h = $_REQUEST["h"];
		$t = $_REQUEST["t"];
		$j = $_REQUEST["j"];
		$vac = $_REQUEST["vac"];
        // si administrateur du module ou ses propres dispos
        if (($user[1] >= $admin) || ($user[2] == $pseudo)) {
            $verif = mysql_query("SELECT * FROM $nuked[prefix]" . _dispo . " AS DISPO, " . USER_TABLE . " AS USER where DISPO.id=USER.id AND USER.pseudo='$pseudo' ") or die("" . _ERRORSQL . "<br />" . $sql . "<br />" . mysql_error());
            $res = mysql_num_rows($verif);
            $data = mysql_fetch_array($verif);

            if ($data[$j] == "") $data[$j] = "000000000000000000000000";

            for ($i = 1;$i <= $t;$i++) $str2 .= "$d";
            $str1 = substr($data[$j], 0, $h);
            $str3 = substr($data[$j], $h + $t, 24 - ($h + $t));
            $str = $str1 . $str2 . $str3;
            $data[$j] = $str;

            if ($vac == "on") $data[8] = 1;
            else $data[8] = 0;

            if ($res > 0) {
                $sql = mysql_query("UPDATE $nuked[prefix]" . _dispo . " SET lun='$data[1]', mar='$data[2]', mer='$data[3]', jeu='$data[4]', ven='$data[5]', sam='$data[6]', dim='$data[7]', vac='$data[8]' WHERE id='$data[0]'") or die("" . _ERRORSQL . "1<br />" . $sql . "<br />" . mysql_error());
            } else {
                $sql = mysql_query("SELECT id FROM " . USER_TABLE . " WHERE pseudo='$pseudo'");
                $id = mysql_fetch_array($sql);
                $sql = mysql_query("insert into $nuked[prefix]" . _dispo . "  ( `id` , `lun` , `mar` , `mer` , `jeu` , `ven` , `sam` , `dim` , `vac` ) values ('$id[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]', '$data[8]')") or die("" . _ERRORSQL . "2<br />" . $sql . "<br />" . mysql_error());
            } 
            opentable();
            if ($sql) echo "<div style=\"text-align: center;\"><br />" . _PREFMODIF . "<br /><br /></div>";
            else echo "<div style=\"text-align: center;\"><br />" . _ERRORSQL . "<br /><br /></div>";
            CloseTable();
        } 
        redirect("index.php?file=Dispo&pseudo=$pseudo&op=edit", 2);
    } 

    function del_dispo($pseudo)
    {
        global $nuked, $user, $admin; 
        // si administrateur du module ou ses propres dispos
        if (($user[1] >= $admin) || ($user[2] == $pseudo)) {
            $sql = mysql_query("SELECT id FROM " . USER_TABLE . " WHERE pseudo='$pseudo'");
            $id = mysql_fetch_array($sql);

            $delete = mysql_query("DELETE FROM $nuked[prefix]" . _dispo . " WHERE id='$id[0]' ");

            opentable();
            if ($sql) echo "<div style=\"text-align: center;\"><br />" . _PREFMODIF . "<br /><br /></div>";
            else echo "<div style=\"text-align: center;\"><br />" . _ERRORSQL . "<br /><br /></div>";
            CloseTable();
        } 
        redirect("index.php?file=Team", 2);
    } 
	global $user;
	$pseudo = $_REQUEST["pseudo"];
    switch ($_REQUEST["op"]) {
        case"modif":
            modif($pseudo);
            break;

        case"pref_add":
            pref_add($pseudo);
            break;

        case"del_dispo":
            del_dispo($pseudo);
            break;

        default:
			if ($user[1] > 1 && $user[1] >= $niveau) {
                if (!isset($pseudo)) $pseudo = $user[2];
            }
            index($pseudo);
    } 
} else if ($niveau == -1) {
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
} else if ($niveau == 1 && $user[1] == 0) {
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>";
    closetable();
} else {
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
} 

?>