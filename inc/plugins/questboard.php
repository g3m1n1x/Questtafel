<?php

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}


// Informationen für den Plugin Manager
function questboard_info() 
{
	return array(
		"name"			=> "Questtafel",
		"description"	=> "Mit diesem Plugin können Quests für die User*innen erstellt und gemanaged werden.",
		"author"		=> "white_rabbit / g3m1n1",
		"authorsite"	=> "https://epic.quodvide.de/member.php?action=profile&uid=2",
		"version"		=> "1.0",
		"compatibility" => "18*"
	);
}

// Installation
function questboard_install()
{
    global $db, $cache, $mybb;

    // DB-Tabelle erstellen
    $db->query("CREATE TABLE ".TABLE_PREFIX."questboard(
        `nid` int(10) NOT NULL AUTO_INCREMENT,
        `type` VARCHAR(255) NOT NULL,
        `title` VARCHAR(2500) NOT NULL,
        `shortdescription` VARCHAR(600),
        `quest` LONGTEXT,
        `keywords` VARCHAR(500),
        `skills` VARCHAR(255),
        `location` VARCHAR(500),
        `lead` VARCHAR(255),
        `reward` VARCHAR(500),
        `level` VARCHAR(255),
        `status` VARCHAR(255),
        `background` LONGTEXT,
        `material` LONGTEXT,
        `maps` LONGTEXT,
        `treasure` LONGTEXT,
        `solution` LONGTEXT,
        `players` VARCHAR(500),
        `scene` VARCHAR(500),
        `visible` INT(10) NOT NULL,
        `reusable` INT(10) NOT NULL,
        PRIMARY KEY (`nid`),
        KEY `nid` (`nid`)
    )
     ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1
    "); 
    
    // Tabellenerweiterung der users-Tabelle für die Index Nachrichten
    $db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `questboard_new` int(11) NOT NULL DEFAULT '0';");

    $db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `questboard_new_registration` int(11) NOT NULL DEFAULT '0';");

    $db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `questboard_quest_evaluation` int(11) NOT NULL DEFAULT '1';");

    // Einstellungen ACP
    $setting_group = array(
        'name'          => 'questboard',
        'title'         => 'Questtafel',
        'description'   => 'Einstellungen für die Questtafel',
        'disporder'     => 1,
        'isdefault'     => 0
    );
        
    $gid = $db->insert_query("settinggroups", $setting_group); 

    $setting_array = array(
        'questboard_allow_groups_access' => array(
            'title' => 'Questtafel zugänglich',
            'description' => 'Welche Gruppen dürfen die Questtafel sehen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 0
        ),

        'questboard_allow_groups_see' => array(
            'title' => 'Quests sichtbar für',
            'description' => 'Welche Gruppen dürfen die unterschiedlichen Quests sehen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 1
        ),

        'questboard_allow_groups_take' => array(
            'title' => 'Quests annehmen',
            'description' => 'Welche Gruppen dürfen Quests annehmen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 2
        ),

        'questboard_allow_groups_see_all' => array(
            'title' => 'Unveröffentlichte Quests sichtbar für',
            'description' => 'Welche Gruppen dürfen unveröffentlichte Quests sehen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 3
        ),

        'questboard_allow_groups_add' => array(
            'title' => 'Quests erstellen',
            'description' => 'Welche Gruppen dürfen Quests erstellen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 4
        ),

        'questboard_allow_groups_finish' => array(
            'title' => 'Quests als erledigt markieren',
            'description' => 'Welche Gruppen dürfen Quests als erledigt markieren?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 5
        ),
	
	'questboard_allow_groups_edit' => array(
	    'title' => 'Quests bearbeiten',
            'description' => 'Welche Gruppen dürfen Quests nachträglich bearbeiten?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 6
        ),
	
	'questboard_allow_groups_lead' => array(
	    'title' => 'Spielleitungsinformationen',
            'description' => 'Welche Gruppen dürfen die Spielleitungsinformationen sehen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 7
        ),
    );

foreach($setting_array as $name => $setting)
    {
        $setting['name'] = $name;
        $setting['gid']  = $gid;
        $db->insert_query('settings', $setting);
    }

rebuild_settings();

 // Templates und CSS erstellen
require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	
// ## Templategruppe erstellen
$templategrouparray = array(
    'prefix' => 'questboard',
    'title'  => $db->escape_string('Questtafel'),
    'isdefault' => 1
  );

  $db->insert_query("templategroups", $templategrouparray);

// ## Seite - questboard
$insert_array = array(
    'title'	    => 'questboard',
    'template'	=> $db->escape_string('
<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->questboard}</title>
{$headerinclude}
</head>
<body>
    {$header}
    <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
        <tr>
            <td>
            <div class="questboard">
                {$navigation}
                <div class="questboard_content">
                {$questtype}
                <form>
                    <select name="action" id="action">
                        <option value="free">Freie Quests</option>
                        <option value="allgemein">Freie allgemeine Quests</option>
                        <option value="special">Freie Specialquests</option>
                        <option value="single">Freie Singlequests</option>
                        <option value="berufsbezogen">Freie berufsbezogene Quests</option>
                    </select>
                    <input type="submit" value="Filtern">
                </form>
                {$description}
                {$none}
                {$bit}
                </div>
            </div>
            </td>
        </tr>
    </table>
    {$footer}
</body>
</html>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Quest hinzufügen - questboard_add
$insert_array = array(
    'title'	    => 'questboard_add',
    'template'	=> $db->escape_string('
<html>
<head>
<title>{$settings[\'bbname\']} - Quest hinzufügen</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><h1>Quest hinzufügen</h1></td>
</tr>
<div class="questboard">
    <div class="questboard_navigation">
        {$navigation}
    </div>
    <div class="questboard_form">

    <form id="questboard" action="questboard.php?action=add" method="post">
    <h1>Quest hinzufügen</h1>

    <div class="questboard_description">Alle Felder mit einem * müssen ausgefüllt werden. Alle Felder im oberen Block sind für User*innen sichtbar, sobald die Quest auf "sichtbar" gestellt ist.</div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            Soll die Quest für alle sichtbar sein?*
        </div>
        <div class="questboard_formblock-field-radio">
            <input type="radio" id="1" name="visible" value="1">
                <label for="1">sichtbar</label>
            <input type="radio" id="0" name="visible" value="0">
                <label for="0">unsichtbar</label>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            Soll die Quest mehrmals bespielbar sein?*
        </div>
        <div class="questboard_formblock-field-radio">
            <input type="radio" id="1" name="reusable" value="1">
                <label for="1">Ja</label>
            <input type="radio" id="0" name="reusable" value="0">
                <label for="0">Nein</label>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            Questtitel
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="title" id="title">
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Questtyp*</b>
            <br>Wähle aus der Liste aus, um welche Art von Quest es sich handelt.
        </div>
        <div class="questboard_formblock-field">
            <select name="type" id="type" style="width: 100%;" required>
                <option value="">Wähle den Typ</option>
                <option value="Allgemeine Quest">Allgemeine Quest</option>
                <option value="Specialquest">Specialquest</option>
                <option value="Singlequest">Singlequest</option>
                <option value="Berufsbezogene Quest">Berufsbezogene Quest</option>
            </select>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Kurzbeschreibung</b>
            <br>Gib hier eine kurze, aussagekräftige Beschreibung der Quest von max. 500 Zeichen an.
        </div>
        <div class="questboard_formblock-field">
            <textarea maxlength="500" name="shortdescription" id="shortdescription"></textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Questbeschreibung</b>
            <br>Gib hier eine ausführliche Beschreibung der Quest an. Die User*innen müssen im Zweifelsfall mit dieser Beschreibung die Quest bestreiten können.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="quest" id="quest"></textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Fähigkeiten</b>
            <br>Gib hier Eigenschaften an, die mindestens ein Mitglied der Gruppe benötigt. Trenne die Eigenschaften mit , ab. Wenn sie für den Quest von Nachteil sind, setze eine 1 davor. Wenn sie bei dem Quest nicht zugelassen sind, setze eine 0 davor.
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="skills" id="skills">
        </div>
    </div>
    
    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Spielort</b>
            <br>Gib den Spielort für die Quest an.
        </div>
        <div class="questboard_formblock-field">
            <select name="location" id="location"  style="width: 100%;">
                <option value="">Wähle den Spielort</option>
                <option value="Hogwarts">Hogwarts</option>
                <option value="Hogsmeade">Hogsmeade</option>
                <option value="London">London</option>
                <option value="Sonstiger Ort">Sonstiger Ort</option>
            </select>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Belohnung</b>
            <br>Gib an, wie viel die Questgeber*innen der Gruppe an Entlohnung versprechen. Das muss nicht mit der Belohnung übereinstimmen, die Du für sie vorsiehst.
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="reward" id="reward" value="n/a">
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Geleitet?</b>
            <br>Wähle aus, ob die Quest geleitet wird. Wenn sie nicht geleitet wird, schreibe die Questinformationen so, dass die User die Quest ohne weitere Informationen absolvieren können.
        </div>
        <div class="questboard_formblock-field">
            <select name="lead" id="lead" style="width: 100%;">
                <option value="">Wähle eine Leitung aus</option>
                <option value="Ja">Ja</option>
                <option value="Nein">Nein</option>
            </select>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Schwierigkeitslevel</b>
            <br>Wähle aus, wie komplex die Quest zu spielen ist. Es geht nicht darum, wie schwer die Quest für die Charaktere ist, sondern wie viel er den Spielenden abverlangt.
        </div>
        <div class="questboard_formblock-field">
            <select name="level" id="level">
                <option value="">Wähle die Schwierigkeit</option>
                <option value="leicht">leicht</option>
                <option value="mittel">mittel</option>
                <option value="schwer">schwer</option>
                <option value="tödlich">tödlich</option>
            </select>
        </div>
    </div>

    <h2>Informationen für die Spielleitung</h2>

    <div class="questboard_description">Diese Informationen sind nicht für User*innen einsehbar.</div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Hintergrund</b>
            <br>Trage hier alle weiteren wichtigen Hintergrundinformationen ein.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="background" id="background"></textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Material</b>
            <br>Hier kannst Du Material wie Bilder verlinken, die Du in die Quest einbauen willst. Vergiss die Quellenangaben nicht!
        </div>
        <div class="questboard_formblock-field">
            <textarea name="material" id="material"></textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Karten</b>
            <br>Hier kannst Du auf Karten verlinken, die Du nutzen willst.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="maps" id="maps"></textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Belohnungen</b>
            <br>Trage hier weitere Belohnungen ein, die die Charaktere finden können.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="treasure" id="treasure"></textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Rätsel & Lösungen</b>
            <br>Erläutere hier Rätsel und Lösungen dazu, die die Charaktere im Ingame oder die User*innen zu knacken haben.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="solution" id="solution"></textarea>
        </div>
    </div>

<input type="submit" value="Absenden" name="submit" id="submit">
</form>
</div>
</td>
</tr>
</table>
{$footer}
</body>
</html>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Alert für neu erstellte Quests (alle User, die auf das Questboard zugreifen können) - questboard_alert
$insert_array = array(
    'title'	    => 'questboard_alert',
    'template'	=> $db->escape_string('
    <div class="quest_alert" id="new-quest">
        Eine neue <a href="https://beta-zone.de/mybb/questboard.php?action=free">Quest</a> wurde ausgeschrieben!
        {$questboard_read}
    </div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Alert für angenommene Quests (Spielleiter only) - questboard_alert_anmeldung
$insert_array = array(
    'title'	    => 'questboard_alert_anmeldung',
    'template'	=> $db->escape_string('
    <div class="quest_alert" id="new-registration">
        Jemand hat sich für eine <a href="https://beta-zone.de/mybb/questboard.php?action=taken">Quest</a> angemeldet!
        {$questboard_read_registration}
    </div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Alert für zur Auswertung freigegebene Quests (Spielleiter only) - questboard_alert_auswertung
$insert_array = array(
    'title'	    => 'questboard_alert_auswertung',
    'template'	=> $db->escape_string('
    <div class="quest_alert" id="new-evaluation">
        Eine <a href="https://beta-zone.de/mybb/questboard.php?action=inEvaluation">Quest</a> kann ausgewertet werden!
        {$questboard_read_evaluation}
    </div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Beschreibung - questboard_description
$insert_array = array(
    'title'	    => 'questboard_description',
    'template'	=> $db->escape_string('
<div class="questboard_description">
    <h1>Questtafel</h1>
    Willkommen an der Questtafel! Hier findest du alle aktuellen Aufträge, die dir zur Verfügung stehen. Die Questtafel dient als zentrale Anlaufstelle für alle, die sich nach Ruhm, Reichtum und Ehre sehnen – oder nach einer abwechslungsreichen Herausforderung.
    <br/>
    Die Quests sind nach Kategorien sortiert und umfassen verschiedenste Schwierigkeitsgrade und Belohnungen. Ob du nun schon mehrere Quests erfolgreich abgeschlossen hast oder auf der Suche nach deiner ersten Herausforderung bist – hier wirst du fündig!
    <br/><br/>
    So funktioniert es:
    <br/>
    <ul><li><b>Allgemeine Quests:</b> Diese Aufträge sind für Abenteurer aller Erfahrungsstufen geeignet. Sie bieten einfache oder mittelschwere Herausforderungen.</li>
    <li><b>Specialquests:</b> Für jene, die etwas Besonderes suchen. Diese Quests bieten außergewöhnliche Belohnungen und sind nur für die tapfersten Helden gedacht. Manchmal handelt es sich hierbei um spezielle, geleitete Quests, bei denen Discord erforderlich ist.</li>
    <li><b>Singlequests:</b> Singlequests bieten dir die Möglichkeit, unabhängig von Postpartner*innen, deinen Charakter noch einmal neu kennenzulernen. Hierfür ist nur ein Post von Nöten, die benötigte Wörteranzahl beträgt meistens 1000 Wörter.</li>
    <li><b>Berufsbezogene Quests:</b> Berufsbezogene Quests sind nur für bestimmte Berufssparten bespielbar und behandeln Themen, die zu dem jeweiligen Beruf passen könnten.</li></ul>
    <br/>
    Wie du mitmachst:
    <br/>
    <ol><li><b>Suche dir eine Quest aus:</b> Stöbere durch die Quests und finde jene, die dich und ggf. andere Questteilnehmer*innen anspricht.</li>
    <li><b>Melde dich und ggf. andere Questteilnehmer*innen an:</b> Wenn du eine Quest gefunden hast, die du annehmen möchtest, melde dich unterhalb der Quest mit dem Link zu deinem/eurem Play an.</li>
    <li><b>Schließe die Quest ab:</b> Arbeite dich durch die Aufgaben und Herausforderungen, die dir gestellt werden.</li>
    <li><b>Gebe deine Quest zur Auswertung frei:</b> Auf der Questtafel kannst du Quests, an denen dein Charakter mitwirkt, zur Auswertung freigeben. Das sollte erst passieren, wenn du der Meinung bist, dass deine Quest auch wirklich abgeschlossen ist.</li>
    <li><b>Auf die Auswertung warten:</b> Nun kannst du dich zurücklehnen und darauf warten, dass deine Quest ausgewertet wird. Erst dann erhältst du auch deine Belohnung.</li></ol>
    <b>Hinweis:</b> Manche Quests erfordern eine bestimmte Anzahl an Mitstreitern oder andere spezielle Anforderungen, um angenommen werden zu dürfen. Achte darauf, diese Anforderungen genau zu prüfen, bevor du dich für eine Quest entscheidest.<br/><br/>
    Viel Erfolg bei deinen Abenteuern – wir freuen uns, deine Fortschritte zu sehen!
</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Bearbeiten - questboard_edit
$insert_array = array(
    'title'	    => 'questboard_edit',
    'template'	=> $db->escape_string('
<html>
<head>
<title>{$settings[\'bbname\']} - Quest hinzufügen</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><h1>Quest hinzufügen</h1></td>
</tr>
<div class="questboard">
    <div class="questboard_navigation">
        {$navigation}
    </div>
    <div class="questboard_form">

    <form id="questboard" action="questboard.php?action=edit&nid={$questboard[\'nid\']}" method="post">
    <h1>Quest bearbeiten</h1>
    
    {$player_info}

    <div class="questboard_description">Alle Felder mit einem * müssen ausgefüllt werden. Alle Felder im oberen Block sind für User*innen sichtbar, sobald die Quest auf "sichtbar" gestellt ist.</div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            Soll die Quest für alle sichtbar sein?* 
        </div>
        <div class="questboard_formblock-field-radio">
            <input type="radio" id="1" name="visible" value="1" {$checked_visible_1}>
                <label for="1">sichtbar</label>
            <input type="radio" id="0" name="visible" value="0" {$checked_visible_0}>
                <label for="0">unsichtbar</label>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            Soll die Quest mehrmals bespielbar sein?* 
        </div>
        <div class="questboard_formblock-field-radio">
            <input type="radio" id="1" name="reusable" value="1" {$checked_reusable_1}>
                <label for="1">Ja</label>
            <input type="radio" id="0" name="reusable" value="0" {$checked_reusable_0}>
                <label for="0">Nein</label>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            Questtitel
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="title" id="title" value="{$questboard[\'title\']}">
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Questtyp*</b>
            <br>Wähle aus der Liste aus, um welche Art von Quest es sich handelt.
        </div>
        <div class="questboard_formblock-field">
            <select name="type" id="type"  style="width: 100%;" required>
                <option value="{$questboard[\'type\']}">{$questboard[\'type\']}</option>
                <option value="Allgemeine Quest">Allgemeine Quest</option>
                <option value="Specialquest">Specialquest</option>
                <option value="Singlequest">Singlequest</option>
                <option value="Berufsbezogene Quest">Berufsbezogene Quest</option>
            </select>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Kurzbeschreibung</b>
            <br>Gib hier eine kurze, aussagekräftige Beschreibung der Quest von max. 500 Zeichen an.
        </div>
        <div class="questboard_formblock-field">
            <textarea maxlength="500" name="shortdescription" id="shortdescription">{$questboard[\'shortdescription\']}</textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Questbeschreibung</b>
            <br>Gib hier eine ausführliche Beschreibung der Quest an. Die User*innen müssen im Zweifelsfall mit dieser Beschreibung die Quest bestreiten können.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="quest" id="quest">{$questboard[\'quest\']}</textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Fähigkeiten</b>
            <br>Gib hier Eigenschaften an, die mindestens ein Mitglied der Gruppe benötigt. Trenne die Eigenschaften mit , ab. Wenn sie für den Quest von Nachteil sind, setze eine 1 davor. Wenn sie bei dem Quest nicht zugelassen sind, setze eine 0 davor.
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="skills" id="skills"  value="{$questboard[\'skills\']}">
        </div>
    </div>
    
    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Spielort</b>
            <br>Gib den Spielort für die Quest an.
        </div>
        <div class="questboard_formblock-field">
            <select name="location" id="location"  style="width: 100%;">
                <option value="{$questboard[\'location\']}">{$questboard[\'location\']}</option>
                <option value="Hogwarts">Hogwarts</option>
                <option value="Hogsmeade">Hogsmeade</option>
                <option value="London">London</option>
                <option value="Sonstiger Ort">Sonstiger Ort</option>
            </select>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Belohnung</b>
            <br>Gib an, wie viel die Questgeber*innen der Gruppe an Entlohnung versprechen. Das muss nicht mit der Belohnung übereinstimmen, die Du für sie vorsiehst.
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="reward" id="reward" value="{$questboard[\'reward\']}">
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Geleitet?</b>
            <br>Wähle aus, ob die Quest geleitet wird. Wenn sie frei geleitet wird, kannst Du sie selbst leiten. Trage Dich dann entsprechend ein. Wenn sie nicht geleitet wird, schreibe die Questsinformationen so, dass die User den Quest ohne weitere Informationen absolvieren können.
        </div>
        <div class="questboard_formblock-field">
            <select name="lead" id="lead" style="width: 100%;">
                <option value="{$questboard[\'lead\']}">{$questboard[\'lead\']}</option>
                <option value="Ja">Ja</option>
                <option value="Nein">Nein</option>
            </select>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Schwierigkeitslevel</b>
            <br>Wähle aus, wie komplex die Quest zu spielen ist. Es geht nicht darum, wie schwer die Quest für die Charaktere ist, sondern wie viel er den Spielenden abverlangt.
        </div>
        <div class="questboard_formblock-field">
            <select name="level" id="level">
                <option value="{$questboard[\'level\']}">{$questboard[\'level\']}</option>
                <option value="leicht">leicht</option>
                <option value="mittel">mittel</option>
                <option value="schwer">schwer</option>
                <option value="tödlich">tödlich</option>
            </select>
        </div>
    </div>

    <h2>Informationen für die Spielleitung</h2>

    <div class="questboard_description">Diese Informationen sind nicht für User*innen einsehbar.</div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Hintergrund</b>
            <br>Trage hier alle weiteren wichtigen Hintergrundinformationen ein.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="background" id="background">{$questboard[\'background\']}</textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Material</b>
            <br>Hier kannst Du Material wie Bilder verlinken, die Du in die Quest einbauen willst. Vergiss die Quellenangaben nicht!
        </div>
        <div class="questboard_formblock-field">
            <textarea name="material" id="material">{$questboard[\'material\']}</textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Karten</b>
            <br>Hier kannst Du auf Karten verlinken, die Du nutzen willst.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="maps" id="maps">{$questboard[\'maps\']}</textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Belohnungen</b>
            <br>Trage hier weitere Belohnungen ein, die die Charaktere finden können. Wenn Du etwas Exotisches verteilen willst, sprich Dich mit der Spielleitung ab.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="treasure" id="treasure">{$questboard[\'treasure\']}</textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Rätsel & Lösungen</b>
            <br>Erläutere hier Rätsel und Lösungen dazu, die die Charaktere im Ingame oder die User*innen zu knacken haben.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="solution" id="solution">{$questboard[\'solution\']}</textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Quest erledigt?</b>
            <br>Wurde die Quest erledigt und ausgewertet?
        </div>
        <div class="questboard_formblock-field"></div>
            <input type="radio" id="finished" name="status" value="finished" {$checked_status_1}>
                <label for="finished">erledigt</label>
            <input type="radio" id="free" name="status" value="free" {$checked_status_0}>
                <label for="free">nicht erledigt</label>
        </div>

    <input type="submit" value="Absenden" name="submit" id="submit">
</form>
</div>
</td>
</tr>
</table>
{$footer}
</body>
</html>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Edit Button - questboard_edit_button
$insert_array = array(
    'title'	    => 'questboard_edit_button',
    'template'	=> $db->escape_string('
<div class="questboard_buttons">
    <div class="questboard_button"><a href="questboard.php?action=edit&nid={$questboard[\'nid\']}">Editieren</a> | <a href="questboard.php?action=delete&nid={$questboard[\'nid\']}">Löschen</a></div>
</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Player Info Feld - questboard_player_info_field
$insert_array = array(
    'title'	    => 'questboard_player_info_field',
    'template'	=> $db->escape_string('
    
     <div class="quest_player_info">
            <u>Teilnehmende Charaktere:</u>
            
                <ul class="player-info-list">
                <!-- Diese Liste wird automatisch befüllt -->
                </ul>   
           <a href="{$questboard[\'scene\']}">»Link zur Szene«</a>
        </div>

    <script type="text/javascript">    
        const players = {$questboard[\'players\']};
        const playerList = document.querySelector(".player-info-list");

        if (Array.isArray(players)) {
            if (playerList) {
                players.forEach(player => {
                    const reward = player.hp
                    ? "Nur HP"
                    : player.fifty
                    ? "50% von beidem"
                    : "Nur EP";
                    const li = document.createElement("li");
                    li.textContent = player.user + " (" + reward + ")";
                    playerList.appendChild(li);
                });
            }
        }
    </script>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Navigation - questboard_navigation
$insert_array = array(
'title'	    => 'questboard_navigation',
'template'	=> $db->escape_string('
    <div class="questboard_navigation">
        <div class="questboard_navigation-links"><a href="questboard.php">Questtafel</a></div>
        <div class="questboard_navigation-title">Übersicht</div>
        <div class="questboard_navigation-links">
            <div><a href="questboard.php?action=free"><i class="fa-solid fa-circle"></i> Freie Quests</a></div>
            <div><a href="questboard.php?action=taken"><i class="fa-solid fa-hourglass"></i> Bespielte Quests</a></div>
            <div><a href="questboard.php?action=inEvaluation"><i class="fa-solid fa-clipboard"></i> Auszuwertende Quests</a></div>
            <div><a href="questboard.php?action=finished"><i class="fa-solid fa-check"></i> Ausgewertete Quests</a></div>
        </div>
        {$questboard_cp}
    </div>
        '),
        'sid'       => '-2',
        'dateline'  => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

// ## Navigation CP - questboard_navigation_cp
$insert_array = array(
    'title'	    => 'questboard_navigation_cp',
    'template'	=> $db->escape_string('
    <div class="questboard_navigation-title">Control Panel</div>
    <div class="questboard_navigation-links">
        <div><a href="questboard.php?action=pending"><i class="fa-solid fa-eye-slash"></i> Unveröffentlichte Quests</a></div>
        <div><a href="questboard.php?action=add"><i class="fa-solid fa-plus"></i> Quest hinzufügen</a></div>
    </div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Navigation Keine Erlaubnis - questboard_no_permission
$insert_array = array(
    'title'	    => 'questboard_no_permission',
    'template'	=> $db->escape_string('
    <div class="questboard_quest">Du hast keine Erlaubnis, dir die Quests anzuschauen.</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Quest - questboard_quest
$insert_array = array(
    'title'	    => 'questboard_quest',
    'template'	=> $db->escape_string('
    <div class="questboard_quest">
        <div class="questboard_header">{$edit}<span class="questboard_header_status">{$questboard[\'type\']}{$sl_information}</span></div>
        <div class="questboard_quest-title">
            <div class="questboard_quest-title-title">{$questboard[\'title\']}</div>
        </div>
        <div class="questboard_quest-content switch">
            <div class="questboard_quest-content-short short{$questboard[\'nid\']}">{$questboard[\'shortdescription\']}</div>
            <div class="questboard_quest-content-long long{$questboard[\'nid\']}">{$questboard[\'quest\']}
            <div class="questboard_quest-footer-feats">
                {$skills}
            </div>
            </div>
        </div>
                <button class="button{$questboard[\'nid\']} mehr_anzeigen">» Mehr anzeigen</button>
    
        <hr/>
        <div class="questboard_quest-footer">
        <div class="questboard_quest-footer">
            <div class="questboard_quest-footer-item">
                <div class="questboard_quest-footer-item-top">{$questboard[\'location\']}</div>
                <div class="questboard_quest-footer-item-bottom">Ort</div>
            </div>
            <div class="questboard_quest-footer-item">
                <div class="questboard_quest-footer-item-top">{$questboard[\'lead\']}</div>
                <div class="questboard_quest-footer-item-bottom">Geleitet</div>
            </div>
            <div class="questboard_quest-footer-item">
                <div class="questboard_quest-footer-item-top">{$questboard[\'reward\']}</div>
                <div class="questboard_quest-footer-item-bottom">Belohnung</div>
            </div>
            <div class="questboard_quest-footer-item">
                <div class="questboard_quest-footer-item-top questboard_quest-footer-level">{$questboard[\'level\']}</div>
                <div class="questboard_quest-footer-item-bottom">Level</div>
            </div>
        </div>
        </div>
            {$take}
            {$finished}  
        
        <div class="questboard_reusable">
        {$questboard[\'reusable_text\']}
        </div>
    
        </div>
    
    <script type="text/javascript">
    
    /* Kurzbeschreibung und lange Beschreibung Quest */
    
    $(document).ready(function(){
      $(".button{$questboard[\'nid\']}").click(function(){
        $(".long{$questboard[\'nid\']}").toggle(\'slow\');	
        $(".short{$questboard[\'nid\']}").toggle(\'slow\');

        if ($(".long{$questboard[\'nid\']}").is(":visible")) {
            // Wenn die Langbeschreibung sichtbar ist, ändere den Button-Text auf \"Weniger anzeigen"
            $(this).text("» Weniger anzeigen");
        } else {
            // Wenn die Langbeschreibung nicht sichtbar ist, ändere den Button-Text auf "Mehr anzeigen"
            $(this).text("» Mehr anzeigen");
        }
        });
    });
        
    </script>
    <style type="text/css">
     .long{$questboard[\'nid\']} {
         display: none;
        }
    </style>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Quest zur Auswertung freigegeben - questboard_quest_in_evaluation
$insert_array = array(
    'title'	    => 'questboard_quest_in_evaluation',
    'template'	=> $db->escape_string('
    <div class="questboard_quest-content-finished">
        Diese Quest wurde im Rahmen <a href="{$questboard[\'scene\']}">dieser Szene</a> erledigt und wartet nun auf die Auswertung.
    </div>        
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Quest erledigt und ausgewertet - questboard_quest_finished
$insert_array = array(
    'title'	    => 'questboard_quest_finished',
    'template'	=> $db->escape_string('
    <div class="questboard_quest-content-finished">
        Diese Quest wurde im Rahmen <a href="{$questboard[\'scene\']}">dieser Szene</a> erledigt und ausgewertet.
    </div>        
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Keine Quests - questboard_quest_none
$insert_array = array(
    'title'	    => 'questboard_quest_none',
    'template'	=> $db->escape_string('
    <div class="questboard_description">
        Derzeit gibt es keine Quests, die auf diese Suchkriterien passen. Du musst warten, bis neue Quests auf der Tafel erscheinen!
    </div>       
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Spielleiter-Informationen - questboard_quest_sl
$insert_array = array(
    'title'	    => 'questboard_quest_sl',
    'template'	=> $db->escape_string('
<div class="questboard_hidden-content">
    <h1>Informationen für die Spielleitung</h1>

    Diese Seite ist nur für die Spielleitung sichtbar. Die Informationen sollten nicht an die Spielenden weitergegeben werden. Du kannst die Informationen bei Bedarf ergänzen, indem du die Quest editierst.

    <h2>Hintergründe</h2>
    {$questboard[\'background\']}

    <h2>Zusatzmaterial</h2>
    {$questboard[\'material\']}

    <h2>Karten</h2>
    {$questboard[\'maps\']}

    <h2>Schätze & Belohnungen</h2>
    {$questboard[\'treasure\']}

    <h2>Lösung</h2>
    {$questboard[\'solution\']}

</div>    
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Kein Zugang zu Spielleiter-Informationen - questboard_quest_sl_nope
$insert_array = array(
    'title'	    => 'questboard_quest_sl_nope',
    'template'	=> $db->escape_string('
    <div class="questboard_hidden-content">
        <div class="questboard_description">
            Du hast nicht die Berechtigung, um dir Spielleiterinformationen anzuschauen.
        </div>
    </div>   
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Quest annehmen - questboard_quest_take
$insert_array = array(
    'title'	    => 'questboard_quest_take',
    'template'	=> $db->escape_string('
    <link rel="stylesheet" href="{$mybb->asset_url}/jscripts/select2/select2.css?ver=1807">
    <script type="text/javascript" src="{$mybb->asset_url}/jscripts/select2/select2.min.js?ver=1806"></script>
    <script type="text/javascript">
    $(document).ready(function () {
        MyBB.select2();
        if (use_xmlhttprequest == "1") {
            function initializeSelect2() {
                $(".character-select").each(function () {
                    if (!$(this).hasClass("select2-hidden-accessible")) {
                        $(this).select2({
                            placeholder: "{$lang->search_user}",
                            minimumInputLength: 2,
                            maximumSelectionSize: \'\',
                            ajax: {
                                url: "xmlhttp.php?action=get_users",
                                dataType: \'json\',
                                data: function (term, page) {
                                    return {
                                        query: term,
                                    };
                                },
                                results: function (data, page) {
                                    return {results: data};
                                }
                            },
                            initSelection: function (element, callback) {
                                var query = $(element).val();
                                if (query !== "") {
                                    var newqueries = [];
                                    exp_queries = query.split(",");
                                    $.each(exp_queries, function (index, value) {
                                        if (value.replace(/\s/g, \'\') != "") {
                                            var newquery = {
                                                id: value.replace(/,\s?/g, ","),
                                                text: value.replace(/,\s?/g, ",")
                                            };
                                            newqueries.push(newquery);
                                        }
                                    });
                                    callback(newqueries);
                                }
                            }
                        });
                    }
                });
            }

            // Initialisierung auf bestehenden Feldern
            initializeSelect2();

            // Optional: Initialisierung nach AJAX oder dynamischem Hinzufügen
            $(document).on("contentUpdated", function () {
                initializeSelect2();
            });
        }
    });
    </script>

    <script type="text/javascript">
    (function() {
        let characterIndex = Date.now();

        // Funktion zum Hinzufügen eines neuen Charakters
        function addCharacterField() {
            const container = $(`#characters-container-{$questboard[\'nid\']}`);
            characterIndex++; // Erhöhe den Index für den nächsten Charakter
            const html = `
                <div class="character-form" data-index="{$questboard[\'nid\']}-{index}" id="character-{$questboard[\'nid\']}-{index}">
                    <input name="characters[{index}][user]" class="character-select" required></input>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="characters[{index}][xp]"> Nur EP
                        </label>
                        <label>
                            <input type="checkbox" name="characters[{index}][hp]"> Nur HP
                        </label>
                        <label>
                            <input type="checkbox" name="characters[{index}][fifty]"> 50% von beidem
                        </label>
                    </div>
                    <span class="remove-button remove-{$questboard[\'nid\']}" data-character-index="{index}" data-quest-id="{$questboard[\'nid\']}">✖</span>
                </div>
            `;
            
            const filledHtml = html.replace(/{index}/g, characterIndex);

            // Füge das neue Feld hinzu
            $("#characters-container-{$questboard[\'nid\']}").append(filledHtml);

            // Initialisiere select2 für das neu hinzugefügte Feld
            MyBB.select2();
            container.find(`.character-select`).last().select2({
            placeholder: "{$lang->search_user}",
            minimumInputLength: 2,
            ajax: {
                url: "xmlhttp.php?action=get_users",
                dataType: \'json\',
                data: function(term, page) {
                    return { query: term };
                },
                results: function(data, page) {
                    return { results: data };
                }
            },
        });
        }

        // Funktion zum Entfernen eines Charakter-Feldes
       $(document).on(\'click\', \'.remove-{$questboard[\'nid\']}\', function() {
            const questId = $(this).data(\'quest-id\');
            const characterIndex = $(this).data(\'character-index\');
            const templateId = "character-{$questboard[\'nid\']}-{index}";
            
            // Platzhalter {index} mit dem aktuellen characterIndex ersetzen
            const elementId = templateId.replace("{index}", characterIndex);

            // Element mit der generierten ID abrufen und entfernen
            const element = document.getElementById(elementId);
            if (element) {
                element.remove();
            }
        });

        // Initialisiere das erste Feld
        $(document).ready(function () {
            addCharacterField();

            // Button-Klick zum Hinzufügen eines weiteren Charakters
            $("#add-character-{$questboard[\'nid\']}").on("click", function () {
                addCharacterField();
            });
        });
    })();
    </script>

    <div class="questboard_quest-take">
        <details>
            <summary><b>Quest annehmen</b></summary>
            <form id="character-form" action="questboard.php?action=take&nid={$questboard[\'nid\']}" method="post">
            <div class="questboard_quest_scene-input"><b>Szene:</b> <input type="text" id="scene" name="scene" placeholder="URL einfügen" required/></div>
            <div id="characters-container-{$questboard[\'nid\']}">
                <!-- Hier wird das erste Feld dynamisch hinzugefügt -->
            </div>
            <button type="button" id="add-character-{$questboard[\'nid\']}">Charakter hinzufügen</button>
            <br><br>
            <input type="submit" value="Quest annehmen" name="take_quest" />
            </form>
        </details>
    </div> 
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Quest angenommen - questboard_quest_taken
$insert_array = array(
    'title'	    => 'questboard_quest_taken',
    'template'	=> $db->escape_string('

    <div id="questboard_quest-taken-{$questboard[\'nid\']}">
    </div>

    <script type="text/javascript">    
        (function () {
            const players = {$questboard[\'players\']};
            const questId = "{$questboard[\'nid\']}";
            const sceneLink = "{$questboard[\'scene\']}";

            if (!players || !questId || !sceneLink) {
                console.error("Fehlende Daten für die Quest.");
                return;
            }
                
            const containerId = `questboard-quest-taken-{index}`;
            const replacedContainerId = containerId.replace(/{index}/g, questId);
            if (document.getElementById(replacedContainerId)) {
                return;
            }

            // Create new Element
            const questTakenContainer = document.createElement("div");
            questTakenContainer.id = replacedContainerId;
            questTakenContainer.className = "questboard-quest-info";

            // List player
            if (Array.isArray(players)) {
                const playerNames = players.map(player => player.user); // Array der Spielernamen

                const playerNamesString = playerNames.join(", ");
                let text = `Die Quest wurde im Rahmen <a href="{$questboard[\'scene\']}"><strong>dieser Szene</strong></a> von <strong>{index}</strong> angenommen.`;
                
                // Überprüfung: Ist der aktuelle Benutzer in der Liste?
                if (playerNames.includes("{$mybb->user[\'username\']}")) {
                    const evaluateQuestLink = ` <a href="questboard.php?action=evaluate&nid={$questboard[\'nid\']}"><b>(Quest zur Auswertung freigeben)</b></a>`;
                    text += evaluateQuestLink;
                } 

                questTakenContainer.innerHTML = text.replace(/{index}/g, playerNamesString);
            } else {
                questTakenContainer.innerHTML = `
                    Die Quest wurde im Rahmen <a href="${sceneLink}">dieser Szene</a> angenommen, aber keine Spieler sind angegeben.
                `;
            }

            // Neuen Container zum Hauptbereich hinzufügen
            document.getElementById("questboard_quest-taken-{$questboard[\'nid\']}").appendChild(questTakenContainer);
        })();
    </script>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## Spielleiter-Information - questboard_sl_information
$insert_array = array(
    'title'	    => 'questboard_sl_information',
    'template'	=> $db->escape_string('
    <button class="sl_button{$questboard[\'nid\']}"><i class="fa-solid fa-lightbulb"></i></button>
        <div class="questboard_hidden-sl-information sl{$questboard[\'nid\']}"> 
        
        <div class="questboard_hidden-content">
            <div class="questboard_hidden-sl-information-header"><h1>Informationen für die Spielleitung</h1></div>
        
            Diese Seite ist nur für die Spielleitung sichtbar. Die Informationen sollten nicht an die Spielenden weitergegeben werden. Du kannst die Informationen bei Bedarf ergänzen, indem Du die Quest editierst.
        
            <h2>Hintergründe</h2>
            {$questboard[\'background\']}
        
            <h2>Zusatzmaterial</h2>
            {$questboard[\'material\']}
        
            <h2>Karten</h2>
            {$questboard[\'maps\']}
        
            <h2>Schätze & Belohnungen</h2>
            {$questboard[\'treasure\']}
        
            <h2>Lösung</h2>
            {$questboard[\'solution\']}
        
        </div> 
        </div>
    <script>
    $(document).ready(function(){
      $(".sl_button{$questboard[\'nid\']}").click(function(){
        $(".sl{$questboard[\'nid\']}").css(\'display\', \'block\');	
        });
    });	
        
    $(document).mouseup(function(e) 
    {
     $(".sl{$questboard[\'nid\']}").css(\'display\', \'none\');	
        $(".background").css(\'display\', \'none\');	
        var container = $(".sl{$questboard[\'nid\']}");
        if (!container.is(e.target) && container.has(e.target).length === 0) 
        {
            container.hide();
        }
    });
    </script>
    <style type="text/css">
    .sl{$questboard[\'nid\']} {
         display: none;
    }
    </style>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);

// ## CSS 
$css = array(
    'name'  => 'questboard.css',
    'tid'   => 1,
    'attachedto' => '',
    "stylesheet" =>	'

:root {
    --background-main: #171e20;
    --background: #416368;
    --emphasis: #5f3e43;
    --headline-color: #171e20;
    --alert-background: #5f3e43;
    --alert-text: #798681;
}

.questboard button {
    cursor: pointer;
    width: auto;
    background: transparent;
    color: var(--background);
    border: none;
    display:flex;
    padding: 0;
}

hr {
    width: 40%;
    background-color: var(--headline-color);
}

/* Popup*/
.quest_alert {
	background: var(--alert-background);
	text-align: center;
	padding: 12px 20px;
	margin-bottom: 15px;
	word-wrap: break-word;
	border-radius: 2px;
	font-size: 13px;
    color: var(----alert-text);
}

.quest_alert a {
    font-weight:900;
    text-decoration: underline;
    color: var(----alert-text);
}

.sl {
    display: none;
}

.questboard_hidden-sl-information {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  margin: auto;
  z-index: 1000;
  height: 400px;
  width: 800px;
  overflow-y: scroll;
  scrollbar-width: none;
  border: solid 0.5px var(--emphasis);
  animation-name: animatetop;
  animation-duration: 0.5s;
  box-shadow: 0 0px 2px var(--background);
}

.questboard_hidden-sl-information-header {
  text-align: center;
  padding-bottom: 15px;
  text-decoration: underline;
}

@keyframes animatetop {
  from {
    top: -300px;
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.questboard_hidden-content {
  margin: auto;
  padding: 30px;
  box-sizing: border-box;
  background-color: var(--background-main);
}

.questboard {
  width: 100%;
  background: var(--background-main);
  display: flex;
  gap: 20px;
  align-items: flex-start;
  font-family: Roboto, sans-serif;
}

.questboard_navigation {
  align-self: flex-start;
}

.questboard_navigation-title {
  width: 200px;
  background: var(--background);
  padding: 20px;
  text-transform: uppercase;
  color: var(--headline-color);
  font-weight: bold;
}

.questboard_navigation-links {
  padding: 10px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  text-align: left;
}

/* #################### Forms #################### */

.questboard_form {
    flex-basis: 1000px;
    padding: 40px;
}

.questboard_formblock {
    margin: 20px 0;
}

.questboard_formblock-label {
    width: 80%;
}

.questboard_formblock-label b {
    color: var(--emphasis);
    text-transform: uppercase;
    font-size: 16px;
}

.questboard_formblock-field textarea {
    width: 80%;
    height: 100px;
}

.questboard_formblock-field select {
    width: 50%;
}

.questboard_formblock-field input {
    width: 300px;
}

.questboard_formblock-field-radio {
    width: 300px;
}

.character-form {
    margin: 5px 0;
    display: flex;
    gap: 20px;
    align-items: center;
}

.checkbox-group {
    align-items: center;
    gap: 10px;
}
.remove-button {
    margin-left: auto;
    color: var(--emphasis);
    cursor: pointer;
}

.character-select {
    width: 28%;
}

/* #################### Content #################### */

.quest_player_info {
    font-size: 15px;
    border-left: 8px solid var(--emphasis);
    background-color: var(--background);
    color: var(--headline-color);
    padding: 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    font-weight: 900;
}

.quest_player_info a {
    color: var(--headline-color);
    font-weight: 900;
}

.player-info-list {
    color: var(--headline-color);
}

.questboard_content {
  display: flex;
  align-items:center;
  flex-wrap: wrap;
  margin-top: 15px;
  gap: 10px;
  justify-content: space-evenly;
}

.questboard_description {
    padding: 40px;
    line-height: 180%;
}

.questboard_description h1 {
    text-align: center;
}

.questboard_quest {
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%;
  margin: 30px;
  padding: 20px;
  box-sizing: border-box;
  border: 2px solid var(--background);
}

.questboard_header {
    display: flex;
    gap: 20px;
    justify-content: space-between;
}

.questboard_header_status{
    display:flex;
    flex-direction: row;
    gap: 20px;
}

.questboard_quest-head-free,
.questboard_quest-head-taken,
.questboard_quest-head-finished
{
    font-size: 13px;
    color: var(--emphasis);
    font-weight:900;
}

.questboard_quest-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.questboard_quest-title-title {
  font-size: 25px;
  width: 60%;
  text-transform: uppercase;
  color: var(--emphasis);
  font-weight:900;
}

.questboard_reusable {
    text-align:center;
    font-style: italic;
    color: var(--emphasis);
    font-weight:900;
}

.questboard_quest-footer {
  display: flex;
  justify-content: space-between;
}

.questboard_quest-footer-feats {
  display: flex;
  flex-direction: row;
  gap: 10px;
  margin-top:10px;
}

.questboard_quest-footer-feats div {
  padding: 5px 10px;
  color: var(--emphasis);
  font-size: 12px;
  text-align: left;
  text-transform: uppercase;
}

.questboard_quest-footer {
  display: flex;
  justify-content: center;
  gap: 40px;
}

.questboard_quest-footer-item {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  align-items: center;
  text-transform: uppercase;
}

.questboard_quest-footer-item-top {
  color: var(--emphasis);
  text-align: center;
  font-size: 12px;
}

.questboard_quest-footer-level {
  display: flex;
  align-items: flex-end;
  font-size: 12px;
  gap: 5px;
}

.select2-search-choice{
    width: 100%;
}

/* ################### Szene annehmen ###################### */

.questboard_quest_scene-input {
    margin: 10px 0;
}

.questboard_quest-take b {
    cursor: pointer;
}

.questboard_quest-taken b {
    text-transform: uppercase;
}

.questboard_quest-taken a {
    text-decoration: underline;
}

.questboard_quest-take h3 {
    text-transform: uppercase;
}
    ',
    'cachefile'     => $db->escape_string(str_replace('/', '', 'questboard.css')),
    'lastmodified'  => time()
    ); 
      
    $sid = $db->insert_query("themestylesheets", $css);
	$db->update_query("themestylesheets", array("cachefile" => "css.php?stylesheet=".$sid), "sid = '".$sid."'", 1);

	$tids = $db->simple_select("themes", "tid");
	while($theme = $db->fetch_array($tids)) {
	    update_theme_stylesheet_list($theme['tid']);
    }
}

// Anzeigen, dass Plugin installiert wurde

function questboard_is_installed()
{
    global $db, $cache, $mybb;
  
      if($db->table_exists("questboard"))  {
        return true;
      }
        return false;
}

// Deinstallieren

function questboard_uninstall()
{
  global $db;

    // DB löschen
    if($db->table_exists("questboard"))
    {
        $db->drop_table("questboard");
    }

    // Änderung in User Tabelle löschen
    if($db->field_exists("questboard_new", "users"))
    {
        $db->drop_column("users", "questboard_new");
    }
    if($db->field_exists("questboard_new_registration", "users"))
    {
        $db->drop_column("users", "questboard_new_registration");
    }
    if($db->field_exists("questboard_quest_evaluation", "users"))
    {
        $db->drop_column("users", "questboard_quest_evaluation");
    }
    
    // Einstellungen löschen
    $db->delete_query('settings', "name LIKE 'questboard%'");
    $db->delete_query('settinggroups', "name = 'questboard'");

    rebuild_settings();

    // Templates löschen
    $db->delete_query("templategroups", "prefix = 'questboard'");
    $db->delete_query("templates", "title LIKE '%questboard%'");

    // CSS löschen
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
	$db->delete_query("themestylesheets", "name = 'questboard.css'");
	$query = $db->simple_select("themes", "tid");
	while($theme = $db->fetch_array($query)) {
		update_theme_stylesheet_list($theme['tid']);
	}
}

// Plugin aktivieren

function questboard_activate()
{
    global $db, $cache;
    
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

    // Variable für den Alert im Header

    find_replace_templatesets('header', '#'.preg_quote('{$bbclosedwarning}').'#', '{$questboard_new} {$questboard_new_registration} {$questboard_quest_evaluation} {$bbclosedwarning}');
}

function questboard_deactivate()
{
     global $db, $cache;

    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

    // Variablen für den Alert im Header entfernen

    find_replace_templatesets('header', '#'.preg_quote('{$questboard_new} {$questboard_new_registration} {$questboard_quest_evaluation} {$bbclosedwarning}').'#', '{$bbclosedwarning}');
}


// Hook
$plugins->add_hook('global_start', 'questboard_global');

function questboard_global(){

    global $db, $mybb, $templates, $questboard_new, $questboard_new_registration, $questboard_quest_evaluation, $questboard_read, $questboard_read_registration, $questboard_read_evaluation, $lang;

    if(is_member($mybb->settings['questboard_allow_groups_see'])) {

        $lang->load('questboard');

        $uid = $mybb->user['uid'];

        echo "<script type='text/javascript' src='{$mybb->asset_url}/jscripts/questboard.js'></script>";

        $questboard_read = "<a href='questboard.php?action=questboard_read&read={$uid}' original-title='Als gelesen markieren' onclick=\"Questboard.dismissNewQuestAlert('{$mybb->settings['bburl']}/', '{$uid}'); return false;\"><i class=\"fas fa-trash\" style=\"float: right;font-size: 14px;padding: 1px;\"></i></a>";
    
        $select = $db->query("SELECT * FROM " . TABLE_PREFIX . "questboard WHERE visible = 1");
        $row_cnt = $select->num_rows;
        if ($row_cnt > 0) {
            $select = $db->query("SELECT questboard_new FROM " . TABLE_PREFIX . "users 
            WHERE uid = '" . $mybb->user['uid'] . "' LIMIT 1");

            $data = $db->fetch_array($select);
            if ($data['questboard_new'] == '0') {
                eval("\$questboard_new = \"" . $templates->get("questboard_alert") . "\";");

            }
                
        }
    }
    
    if(is_member($mybb->settings['questboard_allow_groups_lead'])) {

        $lang->load('questboard');

        $uid = $mybb->user['uid'];

        $questboard_read_registration = "<a href='questboard.php?action=questboard_registration_read&read={$uid}' original-title='Als gelesen markieren' onclick=\"Questboard.dismissNewQuestRegistrationAlert('{$mybb->settings['bburl']}/', '{$uid}'); return false;\"><i class=\"fas fa-trash\" style=\"float: right;font-size: 14px;padding: 1px;\"></i></a>";

        $select = $db->query("SELECT * FROM " . TABLE_PREFIX . "questboard WHERE visible = 1 AND players IS NOT NULL");
        $row_cnt = $select->num_rows;
        if ($row_cnt > 0) {
            $select = $db->query("SELECT questboard_new_registration FROM " . TABLE_PREFIX . "users 
            WHERE uid = '" . $mybb->user['uid'] . "' LIMIT 1");

            $data = $db->fetch_array($select);
            if ($data['questboard_new_registration'] == '0') {
                eval("\$questboard_new_registration = \"" . $templates->get("questboard_alert_anmeldung") . "\";");
            }
                
        }
    }

    if(is_member($mybb->settings['questboard_allow_groups_lead'])) {

        $lang->load('questboard');

        $uid = $mybb->user['uid'];

        $questboard_read_evaluation = "<a href='questboard.php?action=questboard_evaluation_read&read={$uid}' original-title='Als gelesen markieren' onclick=\"Questboard.dismissNewQuestEvaluationAlert('{$mybb->settings['bburl']}/', '{$uid}'); return false;\"><i class=\"fas fa-trash\" style=\"float: right;font-size: 14px;padding: 1px;\"></i></a>";
    
        $select = $db->query("SELECT * FROM " . TABLE_PREFIX . "questboard WHERE visible = 1 AND players IS NOT NULL");
        $row_cnt = $select->num_rows;
        if ($row_cnt > 0) {
            $select = $db->query("SELECT questboard_quest_evaluation FROM " . TABLE_PREFIX . "users 
            WHERE uid = '" . $mybb->user['uid'] . "' LIMIT 1");

            $dataEvaluation = $db->fetch_array($select);
            if ($dataEvaluation['questboard_quest_evaluation'] == '0') {
                eval("\$questboard_quest_evaluation = \"" . $templates->get("questboard_alert_auswertung") . "\";");
            }
                
        }
    }
}


// WER IST ONLINE Anzeige


$plugins->add_hook("fetch_wol_activity_end", "questboard_online_activity");
$plugins->add_hook("build_friendly_wol_location_end", "questboard_online_location");

function questboard_online_activity($user_activity) {
global $parameters;

    $split_loc = explode(".php", $user_activity['location']);
    if($split_loc[0] == $user['location']) {
        $filename = '';
    } else {
        $filename = my_substr($split_loc[0], -my_strpos(strrev($split_loc[0]), "/"));
    }
    
    switch ($filename) {
        case 'questboard':
        if($parameters['action'] == "" && empty($parameters['site'])) {
            $user_activity['activity'] = "questboard";
        }
        if($parameters['action'] == "allgemein" && empty($parameters['site'])) {
            $user_activity['activity'] = "allgemein";
        }
        if($parameters['action'] == "special" && empty($parameters['site'])) {
            $user_activity['activity'] = "special";
        }
        if($parameters['action'] == "single" && empty($parameters['site'])) {
            $user_activity['activity'] = "single";
        }
        if($parameters['action'] == "berufsbezogen" && empty($parameters['site'])) {
            $user_activity['activity'] = "berufsbezogen";
        }
        if($parameters['action'] == "free" && empty($parameters['site'])) {
            $user_activity['activity'] = "free";
        }
        if($parameters['action'] == "taken" && empty($parameters['site'])) {
            $user_activity['activity'] = "taken";
        }
        if($parameters['action'] == "add" && empty($parameters['site'])) {
            $user_activity['activity'] = "add";
        }
        if($parameters['action'] == "inEvaluation" && empty($parameters['site'])) {
            $user_activity['activity'] = "inEvaluation";
        }
        if($parameters['action'] == "edit" && empty($parameters['site'])) {
            $user_activity['activity'] = "edit";
        }
        break;
    }
      
return $user_activity;
}

function questboard_online_location($plugin_array) {
global $mybb, $theme, $lang;

    if($plugin_array['user_activity']['activity'] == "questboard") {
        $plugin_array['location_name'] = "Betrachtet die <a href=\"questboard.php\">Questtafel</a>.";
    }
    if($plugin_array['user_activity']['activity'] == "free") {
		$plugin_array['location_name'] = "Sieht sich <a href=\"questboard.php?action=free\">freie Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "allgemein") {
		$plugin_array['location_name'] = "Sieht sich die <a href=\"questboard.php?action=allgemein\">Allgemeinen Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "special") {
		$plugin_array['location_name'] = "Sieht sich die <a href=\"questboard.php?action=special\">Specialquests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "single") {
		$plugin_array['location_name'] = "Sieht sich die <a href=\"questboard.php?action=single\">Singlequests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "berufsbezogen") {
		$plugin_array['location_name'] = "Sieht sich die <a href=\"questboard.php?action=berufsbezogen\">Berufsbezogenen Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "taken") {
		$plugin_array['location_name'] = "Sieht sich die bespielten <a href=\"questboard.php?action=taken\">Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "inEvaluation") {
		$plugin_array['location_name'] = "Sieht sich <a href=\"questboard.php?action=inEvaluation\">Auszuwertende Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "finished") {
		$plugin_array['location_name'] = "Sieht sich <a href=\"questboard.php?action=finished\">ausgewertete Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "add") {
		$plugin_array['location_name'] = "Erstellt eine neue Quest.";
	}
    if($plugin_array['user_activity']['activity'] == "edit") {
		$plugin_array['location_name'] = "Bearbeitet eine Quest.";
	}

return $plugin_array;
}