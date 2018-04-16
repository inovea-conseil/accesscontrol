<?php

/* Copyright (C) 2017 Inovea Conseil	<info@inovea-conseil.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *   \file       htdocs/admin/catalogtreeview_setupapage.php
 *   \ingroup    v
 *   \brief      Page to setup module catalogtreeview
 */
$res = 0;
if (!$res && file_exists("../main.inc.php"))
    $res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
    $res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (!$res && file_exists("../../../main.inc.php"))
    $res = @include '../../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (!$res && file_exists("../../../dolibarr/htdocs/main.inc.php"))
    $res = @include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (!$res && file_exists("../../../../dolibarr/htdocs/main.inc.php"))
    $res = @include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (!$res)
    die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

$langs->load("admin");
$langs->load("accesscontrol@accesscontrol");

if (!$user->admin)
    accessforbidden();

$action = GETPOST("action");
$name = GETPOST('name', 'alpha');
// Configuration header


/*
 * 	Actions
 */
if ($action == 'setvalue' && $user->admin) {
    $result = dolibarr_set_const($db, "AUTHORIZEDIP", GETPOST("AUTHORIZEDIP"), 'chaine', 0, '', $conf->entity);

    if ($result >= 0) {
        setEventMessage($langs->trans("ModifWin"));
    } else {
        setEventMessage($langs->trans("Error"), 'errors');
    }
}

if ($action == 'set') {
    dolibarr_set_const($db, $name, $conf->entity);
} else if ($action == 'del') {
    dolibarr_del_const($db, $name);
}

/*
 * View
 */

llxHeader('', $langs->trans("AccessControlSetup"), $wikihelp);
dol_fiche_head(
        $head, 'settings', $langs->trans("AccessControl"), 0, "accesscontrol@accesscontrol"
);

$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans("AccessControlSetup"), $linkback, 'setup');

print $langs->trans("AccessControlSetupDesc") . "<br>\n";

print '<br>';


$var = true;

$form = new Form($db);
$bonustype = $conf->global->CB_BONUSTYPE;
$bonustotal = $conf->global->CB_BONUSTOTAL;
$objectbonus = $conf->global->CB_OBJECTBONUS;
$cbuser = $conf->global->CB_USER;
print "<form method=\"post\" action=\"" . $_SERVER["PHP_SELF"] . "\">";
print '<input type="hidden" name="action" value="setvalue">';
print '<table class="" width="100%" cellspacing="0" cellpadding="10">';
print '<tr class="liste_titre">';
print '<td width="120" colspan="3">' . $langs->trans("Parametre") . '</td>';
print "</tr>\n";
$var = !$var;
print '<tr>';
showConf('AUTHORIZEDADMIN', $langs->trans("AUTHORIZED_ADMIN"), $langs->trans("AUTHORIZED_ADMIN"), $bc[$var]);
print '</tr>';
$var = !$var;

print '<tr ' . $bc[$var] . '><td class="fieldrequired">';
print $langs->trans("AuthorizedIP") . '</td><td>';
print '<textarea type="text" name="AUTHORIZEDIP" cols="40" rows="10">'.$conf->global->AUTHORIZEDIP.'</textarea>';
print '</table>';

print '<center><br><input type="submit" class="button" value="' . $langs->trans("Save") . '"></center>';

print '</form><br><br>';

function showConf($const, $texte_nom, $texte_descr, $var) {
    global $conf, $langs, $db;

    print '<tr ' . $var . '>' . "\n";
    print '<td>' . $texte_nom . '</td>' . "\n";
    print '<td> ' . $texte_descr . '</td>' . "\n";
//    print '<td class="nowrap">' . $texte_exemple . '</td>' . "\n";

    if ($conf->global->$const == '1') {
        echo '<td align="center">' . "\n";
        print '<a href="' . $_SERVER['PHP_SELF'] . '?action=del&name=' . $const . '">';
        echo img_picto($langs->trans("Activated"), 'switch_on');
        echo "</td>\n";
    } else if (is_null($conf->global->$const) || $conf->global->$const == 0) {
        $disabled = false;
        if (!empty($conf->multicompany->enabled) && (is_object($mc) && !empty($mc->sharings['referent']) && $mc->sharings['referent'] == $conf->entity) ? false : true)
            ;
        print '<td align="center">';
        if (!$disabled)
            print '<a href="' . $_SERVER['PHP_SELF'] . '?action=set&name=' . $const . '">';
        print img_picto($langs->trans("Disabled"), 'switch_off');
        if (!$disabled)
            print '</a>';
        print '</td>';
    }
}

llxFooter();

$db->close();
