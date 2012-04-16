
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
include 'modules/Admin/design.php';

admintop();
$link_dispo = "file=Dispo&amp;page=admin";
global $user, $language, $link_dispo;

include("modules/Dispo/lang/" . $language . ".lang.php");
include("modules/Admin/lang/" . $language . ".lang.php");

list ($langname, ,) = split ('[.]', $language);
$ModName = basename(dirname(__FILE__));

if ($user[1] >= admin_mod($ModName) && admin_mod($ModName) > -1) {
    $lup_cpt = 0; 
    // variable
    $coldispo = "#32FF32";
    $colindispo = "#FF3232";
    $colptet = "#FF8832";
    $colvac = "#8888ff";
    $n_jour = array ("" . _DIMANCHE . "", "" . _LUNDI . "", "" . _MARDI . "", "" . _MERCREDI . "", "" . _JEUDI . "", "" . _VENDREDI . "", "" . _SAMEDI . "");
    $n_mois = array ("null", "" . _JAN . "", "" . _FEB . "", "" . _MAR . "", "" . _APR . "", "" . _MAY . "", "" . _JUN . "", "" . _JUL . "", "" . _AUG . "", "" . _SEP . "", "" . _OCT . "", "" . _NOV . "", "" . _DEC . "");
    $icon_na = "modules/Dispo/images/na.gif";

    function main()
    {
        global $link_dispo, $icon_na, $bgcolor1, $bgcolor2, $bgcolor3, $theme, $nuked, $user, $langname, $lup_cpt, $coldispo, $colptet, $colvac, $colindispo, $n_mois, $n_jour, $id_jeux, $jour;
		$date = $_REQUEST["date"];
		$heure = $_REQUEST["heure"];
		$id_jeux = $_REQUEST["id_jeux"];
		$jour = $_REQUEST["jour"];
		
        $jour_tmp = date(j);
        $mois = date(n);
        $annee = date(Y);
        if (!isset($date)) $date = $jour_tmp . "/" . $mois . "/" . $annee;
        else {
            list($jour_tmp, $mois, $annee) = explode("/", $date);
            settype($mois, "integer");
            settype($jour_tmp, "integer");
            $jour = date(w, MakeTime(0, 0, 0, $mois, $jour_tmp, $annee));
        } 

        if (!isset($heure)) $heure = date(G);
        else settype($heure, "integer");

        if (!isset($jour)) $jour = date(w);

        $sql_game_menu = mysql_query("SELECT id, name, icon FROM $nuked[prefix]" . _games . " ORDER BY name");
        $nb_game = mysql_num_rows($sql_game_menu);


        if ($nb_game > 1) {
            $sql_game_menualpha = mysql_query("SELECT id, name, icon FROM $nuked[prefix]" . _games . " ORDER BY name");
            while (list($id, $game_name, $icon) = mysql_fetch_array($sql_game_menualpha)) {
                if ($icon) {
                    $iconizateur = $iconizateur . "<a href=\"index.php?$link_dispo&amp;date=$date&amp;heure=$heure\"><img style=\"border: 0;\" src=\"$icon\" alt=\"$game_name\" /></a>&nbsp;";
                } else {
                    $iconizateur = $iconizateur . "<a href=\"index.php?$link_dispo&amp;date=$date&amp;heure=$heure\"><img style=\"border: 0;\" src=\"$icon_na\" alt=\"$game_name\" /></a>&nbsp;";
                } 
            } 
            $iigame = 1;
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: center;\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\"><tr><td align=\"center\" valign=\"bottom\">$iconizateur<br /><a href=\"index.php?$link_dispo&amp;date=$date&amp;heure=$heure\">[" . _TOUS . "]</a></td>";
            while (list($id, $game_name, $icon) = mysql_fetch_array($sql_game_menu)) {
                $game_name = stripslashes($game_name);
                echo "<td align=\"center\" valign=\"bottom\"><a href=\"index.php?$link_dispo&amp;id_jeux=$id&amp;date=$date&amp;heure=$heure\">";
                if ($icon) {
                    echo "<img style=\"border: 0;\" src=\"$icon\" alt=\"$game_name\" />";
                } else {
                    echo "<img style=\"border: 0;\" src=\"$icon_na\" alt=\"$game_name\" />";
                } 
                echo "<br />[$game_name]</a></td>";
                $iigame++;
            } 
            echo "</tr></table>";
            echo "<div style=\"text-align: center;\">" . _CLIQUERJEUX . "</div><br />"; 
            // fin du menu jeux
        } 

        echo"<div class=\"content-box\">\n 
			<div class=\"content-box-header\"><h3>" . _LINEUPDU . "&nbsp;" . $n_jour[$jour] . "&nbsp;" . $jour_tmp . "&nbsp;" . $n_mois[$mois] . "&nbsp;" . _AT . "&nbsp;" . $heure . " h</h3>\n
			<div style=\"text-align:right\"><a href=\"help/" . $langname . "/Dispo.html\" rel=\"modal\">\n
			<img style=\"border: 0\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n
			</div></div>\n
			<table width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">
			<tr style=\"background: " . $bgcolor3 . ";\">
			<td style=\"width: 5%;\" align=\"center\">&nbsp;</td>
			<td style=\"width: 55%;\"><b>" . _NICK . "</b></td>
			<td style=\"width: 40%;\" align=\"center\"><b>" . _TEAM . "</b></td></tr>";
        $sql = mysql_query("SELECT cid, titre, tag FROM $nuked[prefix]" . _team . " ORDER BY ordre");
        $nb_team = mysql_num_rows($sql);
        if ($nb_team > 0) {
            while (list($team, $titre, $team_tag) = mysql_fetch_array($sql)) {
                $titre = stripslashes($titre);
                $team_tag = stripslashes($team_tag);
                if ($id_jeux) {
                    $alphajeux = "AND game='$id_jeux'";
                } 
                $sql2 = mysql_query("SELECT pseudo, country, id, game FROM $nuked[prefix]" . _users . " WHERE niveau>1 AND team='$team' $alphajeux ORDER BY niveau DESC");
                $nb_members = mysql_num_rows($sql2);
                if ($nb_members > 0) {
                    while (list($pseudo, $country, $id, $game) = mysql_fetch_array($sql2)) {
                        $sql_game = mysql_query("SELECT name, icon FROM $nuked[prefix]" . _games . " WHERE id='$game'");
                        list($game_name, $icon) = mysql_fetch_array($sql_game);
                        $game_name = stripslashes($game_name);
                        list ($pays, $ext) = split ('[.]', $country);
                        if ($team_tag != "") {
                            $temp = $team_tag . $pseudo . $nuked[tag_suf];
                        } else {
                            $temp = $nuked[tag_pre] . $pseudo . $nuked[tag_suf];
                        } 
                        $verif = mysql_query("SELECT lun,mar,mer,jeu,ven,sam,dim,vac FROM $nuked[prefix]" . _dispo . " where id='$id' and vac!='1'");
                        $res = mysql_num_rows($verif);
                        $data = array();
                        list($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[0], $vac) = mysql_fetch_array($verif);
                        $horaire = $data[$jour];
                        $now = substr($horaire, $heure, 1);
                        if ($now) {
                            $a++;
                            echo "<tr style=\"background: " . $bgcolor2 . ";\" onmouseover=\"this.style.backgroundColor='" . $bgcolor1 . "'; this.style.cursor='hand';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor2 . "'\">
                            <td align=\"center\" >";
                            if ($icon) {
                                echo "<img src=\"$icon\" alt=\"$game_name\" style=\"border: 0;\" />";
                            } else {
                                echo "<img src=\"$icon_na\" alt=\"$game_name\" style=\"border: 0;\" />";
                            } 
                            echo "</td>";

                            echo "<td>&nbsp;<img src=\"images/flags/$country\" alt=\"$pays\" style=\"border: 0;\" />&nbsp;<a href=\"index.php?file=Dispo&amp;pseudo=$pseudo\" title=\"" . _SEEDISPOOF . " $pseudo\">";
                            if (!$vac) {
                                if ($now == 1) echo "<span style=\"color: " . $coldispo . ";\">";
                                else if ($now == 2) echo "<span style=\"color: " . $colptet . ";\">";
                            } else {
                                echo "<span style=\"color: " . $colvac . ";\">";
                            } 
                            echo "$temp</span></a>";
                            echo"	</td>";
                            echo"	<td align=\"center\" >$titre</td></tr>";
                        } 
                    } 
                } 
            } 
        } else echo "<tr><td align=\"center\" colspan=\"3\">" . _NOTEAMINDB . "</td></tr>";
        if (!$a) {
            echo "<tr><td align=\"center\" colspan=\"3\">" . _AUCUNJOUEUR . "</td></tr>";
        } 
        

        if ($a > 1) $s = "s";
        if ($a) echo"<tr><td align=\"center\" colspan=\"3\"><i>( $a " . _JOUEUR . "$s )</i></td></tr>";
        echo"</table></div>";
		
		
		
        // Visu des matchs
        echo"	<br />";
		 echo"<div class=\"content-box\">\n 
			<div class=\"content-box-header\"><h3>" . _FUTURMATCH. " </h3></div>\n
			<table width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">
			<tr style=\"background: " . $bgcolor3 . ";\">
			<td align=\"center\" style=\"width: 5%;\">&nbsp;</td>
			<td style=\"width: 30%;\"><b>" . _ADVERSAIRE . "</b></td>
			<td align=\"center\" style=\"width: 35%;\"><b>" . _DATE . "</b></td>
			<td align=\"center\" style=\"width: 20%;\"><b>" . _TYPE . "</b></td>
			<td align=\"center\" style=\"width: 10%;\"><b>" . _DETAIL . "</b></td></tr>";
        $sql3 = mysql_query("SELECT warid, adversaire, style, date_jour, date_mois, date_an, heure, game FROM $nuked[prefix]" . _match . " WHERE etat='0' $alphajeux ORDER BY date_an, date_mois, date_jour LIMIT 10");
        $nb_match = mysql_num_rows($sql3);
        if ($nb_match > 0) {
            while (list($war_id, $adv_name, $style, $mjour, $mmois, $mannee, $mheure, $game) = mysql_fetch_array($sql3)) {
                $sql_game = mysql_query("SELECT name, icon FROM $nuked[prefix]" . _games . " WHERE id='$game'");
                list($game_name, $icon) = mysql_fetch_array($sql_game);
                $game_name = stripslashes($game_name);
                $adv_name = stripslashes($adv_name);
                $sql4 = mysql_query("SELECT pays_adv, date_jour, date_mois, date_an, type FROM $nuked[prefix]" . _match . " WHERE warid=$war_id");
                list($pays_adv, $date_jour, $date_mois, $date_an, $type) = mysql_fetch_array($sql4);
                list ($pays, $ext) = split ('[.]', $pays_adv);
                if ($mjour && checkdate($mmois, $mjour, $mannee)) {
                    if ($mjour < 10) $mjour = "0" . $mjour;
                    if ($mmois < 10) $mmois = "0" . $mmois;
                    $mdate = $mjour . "/" . $mmois . "/" . $mannee;
                    echo"	<tr style=\"background: " . $bgcolor2 . ";\" onmouseover=\"this.style.backgroundColor='" . $bgcolor1 . "'; this.style.cursor='hand';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor2 . "'\"><td align=\"center\" >";
                    if ($icon) {
                        echo "<img src=\"$icon\" alt=\"$game_name\" />";
                    } else {
                        echo "<img style=\"border: 0;\" src=\"$icon_na\" alt=\"$game_name\" />";
                    } 
                    echo "</td>";
                    echo "<td >&nbsp;<img src=\"images/flags/$pays_adv\" alt=\"$pays\" />&nbsp;<a href=\"index.php?$link_dispo&amp;date=$mdate&amp;heure=$mheure&amp;id_jeux=$game\" title=\"" . _SEELUPOF . "\"><b>$adv_name</b></a>";
                    echo "</td>";
                    if (!$type) $type = "N/A";
                    echo "	<td align=\"center\" >" . _THE . " $date_jour $n_mois[$date_mois] $date_an " . _AT . " $mheure</td>
						<td align=\"center\" >$type</td>
						<td align=\"center\" ><a href=\"index.php?file=Wars&amp;op=detail&amp;war_id=$war_id\"><img src=\"images/edit.gif\" alt=\"" . _DETAILOF . " $adv_name\" style=\"border: 0;\" /></a></td>
						</tr>";
                } 
            } 
        } else {
            echo "<tr><td colspan=\"5\" align=\"center\" >" . _AUCUNMATCH . "</td></tr>";
        } 
        echo"</table></div><br />";

        echo "<link rel=\"stylesheet\" href=\"dynCalendar.css\" type=\"text/css\" media=\"screen\" />
	<script src=\"browserSniffer.js\" type=\"text/javascript\"></script>
	<script src=\"dynCalendar.js\" type=\"text/javascript\"></script>
	<script type=\"text/javascript\">
	<!--
		// Calendar callback. When a date is clicked on the calendar
		// this function is called so you can do as you want with it
		function calendarCallback(date, month, year)
		{
			date = date + '/' + month + '/' + year;
			document.forms[0].date.value = date;
		}
	// -->
	</script>
	<form method=\"post\" action=\"index.php?file=Dispo&amp;page=admin\">
	<fieldset style=\"border: 1px solid " . $bgcolor3 . "; text-align: center; padding: 3;\">
	<legend style=\"color: " . $bgcolor3 . ";\">&nbsp;<big><b>" . _VOIRDISPOJOUR . "</b></big>&nbsp;</legend>     
	<input type=\"text\" name=\"date\" value=\"$date\" />
	<script type=\"text/javascript\">
    <!--
    	fooCalendar = new dynCalendar('fooCalendar', 'calendarCallback', 'images/');
    //-->
    </script>
    &nbsp;&nbsp;<select name=\"heure\">";
        for($i = 0;$i < 24;$i++) {
            if ($heure == $i) $selected = "selected=\"selected\"";
            else $selected = "";
            echo "<option value=\"" . $i . "\" " . $selected . ">" . $i . "h</option>";
        } 
        echo "</select>
        <input type=\"hidden\" name=\"file\" value=\"Dispo\" />
		<input type=\"hidden\" name=\"page\" value=\"admin\" />
		<input type=\"hidden\" name=\"id_jeux\" value=\"" . $id_jeux . "\" />
		&nbsp;&nbsp;<input type=\"submit\" value=\"" . _AFFICHER . "\" /><br /><br /></fieldset></form>";

        Legende();

        echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div>";
    } 

    function Legende()
    {
        global $colindispo, $colptet, $coldispo, $colvac, $bgcolor1, $bgcolor2, $bgcolor3;
        echo "<br /><table cellspacing=\"0\" cellpadding=\"0\" style=\"margin-left: auto;margin-right: auto;text-align: left;\"><tr><td>			
			<fieldset style=\"border: 1px solid " . $bgcolor3 . "; text-align: left; padding: 3;\">
			<legend style=\"color: " . $bgcolor3 . ";\">&nbsp;" . _LEG . "&nbsp;</legend> 			
            <table  cellspacing=\"1\" cellpadding=\"2\"><tr style=\"background: " . $bgcolor2 . ";\">
			<td><span style=\"color: " . $colptet . ";\">" . _ORANGE . "</span></td><td>" . _VAR . " (" . _VARDSC . ")<br /></td></tr><tr style=\"background: " . $bgcolor2 . ";\">
			<td><span style=\"color: " . $coldispo . ";\">" . _GREEN . "</span></td><td>" . _DISP . "</td></tr></table>
			</fieldset></td></tr></table>";
    } 
    // fonction mktime compatible windows
    function MakeTime()
    {
        $objArgs = func_get_args();
        $nCount = count($objArgs);
        if ($nCount < 7) {
            $objDate = getdate();
            if ($nCount < 1)
                $objArgs[] = $objDate["hours"];
            if ($nCount < 2)
                $objArgs[] = $objDate["minutes"];
            if ($nCount < 3)
                $objArgs[] = $objDate["seconds"];
            if ($nCount < 4)
                $objArgs[] = $objDate["mon"];
            if ($nCount < 5)
                $objArgs[] = $objDate["mday"];
            if ($nCount < 6)
                $objArgs[] = $objDate["year"];
            if ($nCount < 7)
                $objArgs[] = -1;
        } 
        $nYear = $objArgs[5];
        $nOffset = 0;
        if ($nYear < 1970) {
            if ($nYear < 1902)
                return 0;
            else if ($nYear < 1952) {
                $nOffset = -2650838400;
                $objArgs[5] += 84;
                if ($nYear < 1942)
                    $objArgs[6] = 0;
            } else {
                $nOffset = -883612800;
                $objArgs[5] += 28;
            } 
        } 
        return call_user_func_array("mktime", $objArgs) + $nOffset;
    } 

    function nbjm($m, $a)
    {
        $cm = $a * 12 + $m;
        if (($cm > 24443) || ($cm < 1970)) {
            return 0;
        } 
        $as = floor(($cm + 1) / 12);
        $ms = $cm + 1 - 12 * $as;
        $duree = mktime(0, 0, 1, $ms, 1, $as) - mktime(0, 0, 1, $m, 1, $a);
        return ($duree / 86400);
    } 

    switch ($_REQUEST["op"]) {
        case "affiche_lup":
            lineup($quand);
            break;

        case "affiche_lup_pre":
            affiche_lup_pre();
            break;

        case "affiche_lup_day":
            affiche_lup_day();
            break;

        default:
            main();
            break;
    } 
} else if ($user[1] > 1) {
    echo"<div style=\"text-align: center;\"><br />" . _NOENTRANCE . "<br /><br />[ <a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a> ]<br /><br /></div>";
} else {
    echo "<div style=\"text-align: center;\"><br />" . _ZONEADMIN . "<br /><br />[ <a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a> ]<br /><br /></div>";
} 


?>