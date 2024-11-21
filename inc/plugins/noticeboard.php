<?php

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}


// Informationen für den Plugin Manager

function noticeboard_info() 
{
	return array(
		"name"			=> "Questtafel",
		"description"	=> "Ein Questplugin für Quests",
		"author"		=> "white_rabbit",
		"authorsite"	=> "https://epic.quodvide.de/member.php?action=profile&uid=2",
		"version"		=> "1.0",
		"compatibility" => "18*"
	);
}

// Installation

function noticeboard_install()
{
    global $db, $cache, $mybb;

    // DB-Tabelle erstellen

    $db->query("CREATE TABLE ".TABLE_PREFIX."noticeboard(
        `nid` int(10) NOT NULL AUTO_INCREMENT,
        `type` VARCHAR(255) NOT NULL,
        `title` VARCHAR(2500) NOT NULL,
        `shortdescription` VARCHAR(600),
        `quest` LONGTEXT,
        `client` VARCHAR(255),
        `keywords` VARCHAR(500),
        `skills` VARCHAR(255),
        `location` VARCHAR(500),
        `lead` VARCHAR(255),
        `leadby` VARCHAR(255),
        `reward` VARCHAR(500),
        `level` VARCHAR(255),
        `status` VARCHAR(255),
        `monster` VARCHAR(255),
        `background` LONGTEXT,
        `material` LONGTEXT,
        `maps` LONGTEXT,
        `treassure` LONGTEXT,
        `boss` LONGTEXT,
        `solution` LONGTEXT,
        `players` VARCHAR(500),
        `scene` VARCHAR(500),
        `visible` INT(10) NOT NULL,
        PRIMARY KEY (`nid`),
        KEY `nid` (`nid`)
    )
     ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1
    "); 
    
    // Tabellenerweiterung der users-Tabelle für die Index Nachricht

    $db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `noticeboard_new` int(11) NOT NULL DEFAULT '0';");
    

    // Einstellungen ACP

    $setting_group = array(
        'name'          => 'noticeboard',
        'title'         => 'Questtafel',
        'description'   => 'Einstellungen für die Questtafel',
        'disporder'     => 1,
        'isdefault'     => 0
    );
        
    $gid = $db->insert_query("settinggroups", $setting_group); 

    $setting_array = array(
        'noticeboard_allow_groups_access' => array(
            'title' => 'Questtafel zugänglich',
            'description' => 'Welche Gruppen dürfen die Questtafel sehen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 0
        ),

        'noticeboard_allow_groups_see' => array(
            'title' => 'Quests sichtbar für',
            'description' => 'Welche Gruppen dürfen Quests sehen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 1
        ),

        'noticeboard_allow_groups_see_all' => array(
            'title' => 'Nicht freigegebene Quests sichtbar für',
            'description' => 'Welche Gruppen dürfen nicht freigegebene Quests sehen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 2
        ),

        'noticeboard_allow_groups_add' => array(
            'title' => 'Quests erstellen',
            'description' => 'Welche Gruppen dürfen Quests erstellen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 3
        ),

        'noticeboard_allow_groups_take' => array(
            'title' => 'Quests annehmen',
            'description' => 'Welche Gruppen dürfen Quests annehmen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 4
        ),

        'noticeboard_allow_groups_finish' => array(
            'title' => 'Quests als erledigt markieren',
            'description' => 'Welche Gruppen dürfen Quests als erledigt markieren?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 5
        ),
	
	'noticeboard_allow_groups_edit' => array(
	    'title' => 'Quests bearbeiten',
            'description' => 'Welche Gruppen dürfen Quests bearbeiten?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 6
        ),
	
	'noticeboard_allow_groups_lead' => array(
	    'title' => 'Spielleitungsinformationen',
            'description' => 'Welche Gruppen dürfen Spielleitungsinformationen sehen?',
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
    'prefix' => 'noticeboard',
    'title'  => $db->escape_string('Queststafel'),
    'isdefault' => 1
  );

  $db->insert_query("templategroups", $templategrouparray);

// ## Seite - noticeboard
$insert_array = array(
    'title'	    => 'noticeboard',
    'template'	=> $db->escape_string('
<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->noticeboard}</title>
{$headerinclude}
</head>
<body>
    {$header}
    <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
        <tr>
            <td>
            <div class="noticeboard">
                {$navigation}
                <div class="noticeboard_content">
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

// ## Quest hinzufügen - noticeboard_add
$insert_array = array(
    'title'	    => 'noticeboard_add',
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
<div class="noticeboard">
    <div class="noticeboard_navigation">
        {$navigation}
    </div>
    <div class="noticeboard_form">

    <form id="noticeboard" action="noticeboard.php?action=add" method="post">
    <h1>Quest aufgeben</h1>

    <div class="noticeboard_description">Alle Felder mit einem * müssen ausgefüllt werden. Alle Felder im oberen Block sind für User*innen sichtbar, sobald die Quest auf "sichtbar" gestellt ist.</div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            Soll die Quest für alle sichtbar sein?*
        </div>
        <div class="noticeboard_formblock-field-radio">
            <input type="radio" id="1" name="visible" value="1">
                <label for="1">sichtbar</label>
            <input type="radio" id="0" name="visible" value="0">
                <label for="0">unsichtbar</label>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            Questtitel
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="title" id="title">
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Questtyp*</b>
            <br>Wähle aus der Liste aus, um welche Art von Quest es sich handelt.
        </div>
        <div class="noticeboard_formblock-field">
            <select name="type" id="type"  style="width: 100%;" required>
                <option value="">Wähle den Typ</option>
                <option value="AllgemeineQuest">Allgemeine Quest</option>
                <option value="Spezialquest">Spezialquest</option>
                <option value="Spezialquest">Spezialquest</option>
                <option value="BerufsbezogeneQuest">Berufsbezogene Quest</option>
                <option value="Sonstiges">Sonstiges</option>
            </select>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Kurzbeschreibung</b>
            <br>Gib hier eine kurze, aussagekräftige Beschreibung der Quest von max. 500 Zeichen an.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="shortdescription" id="shortdescription"></textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Questsbeschreibung</b>
            <br>Gib hier eine ausführliche Beschreibung der Quest an. Die User*innen müssen im Zweifelsfall mit dieser Beschreibung die Quest bestreiten können.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="quest" id="quest"></textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Questgeber*in</b>
            <br>Trage ein, von welchem Charakter oder NPC im Inplay die Quest kommt.
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="client" id="client">
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Keywords</b>
            <br>Gib bis zu 5 Schlüsselbegriffe ein, die für die Quest relevant sind und trenne sie jeweils mit einem , (Komma).
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="keywords" id="keywords">
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Fähigkeiten</b>
            <br>Gib hier Eigenschaften an, die mindestens ein Mitglied der Gruppe benötigt. Trenne die Eigenschaften mit , ab. Wenn sie für den Quest von Nachteil sind, setze eine 1 davor. Wenn sie bei dem Quest nicht zugelassen sind, setze eine 0 davor.
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="skills" id="skills">
        </div>
    </div>
    
    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Spielort</b>
            <br>Gib den Spielort für die Quest an.
        </div>
        <div class="noticeboard_formblock-field">
            <select name="location" id="location"  style="width: 100%;">
                <option value="">Wähle den Spielort</option>
                <option value="ort 1">Hogwarts</option>
                <option value="ort 2">Hogsmeade</option>
                <option value="ort 3">London</option>
                <option value="ort 4">Sonstiger Ort</option>
            </select>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Belohnung</b>
            <br>Gib an, wie viel die Questgeber*innen der Gruppe an Entlohnung versprechen. Das muss nicht mit der Belohnung übereinstimmen, die Du für sie vorsiehst.
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="reward" id="reward">
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Geleitet?</b>
            <br>Wähle aus, ob die Quest geleitet wird. Wenn sie nicht geleitet wird, schreibe die Questinformationen so, dass die User die Quest ohne weitere Informationen absolvieren können.
        </div>
        <div class="noticeboard_formblock-field">
            <select name="lead" id="lead" style="width: 100%;">
                <option>Wähle eine Leitung aus</option>
                <option value="<i class=\'fa-solid fa-eye\'></i>">geleitet</option>
                <option value="<i class=\'fa-regular fa-eye\'></i>">frei geleitet</option>
                <option value="<i class=\'fa-solid fa-eye-slash\'></i>">nicht geleitet</option>
            </select>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Schwierigkeitslevel</b>
            <br>Wähle aus, wie komplex die Quest zu spielen ist. Es geht nicht darum, wie schwer die Quest für die Charaktere ist, sondern wie viel er den Spielenden abverlangt.
        </div>
        <div class="noticeboard_formblock-field">
            <select name="level" id="level">
                <option value="">Wähle die Schwierigkeit</option>
                <option value="<i class=\'fa-duotone fa-signal-bars-weak\'></i>">leicht</option>
                <option value="<i class=\'fa-duotone fa-signal-bars-fair\'></i>">mittel</option>
                <option value="<i class=\'fa-duotone fa-signal-bars-good\'></i>">schwer</option>
                <option value="<i class=\'fa-light fa-skull-crossbones\'></i>">tödlich</option>
            </select>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Monster</b>
            <br>Gib an, mit welchem Monster die Charaktere zu rechnen haben - sofern es eines gibt. Sie können auch auf andere Monster treffen (insbesondere den Bossgegner).
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="monster" id="monster">
        </div>
    </div>

    <h2>Informationen für die Spielleitung</h2>

    <div class="noticeboard_description">Diese Informationen sind nicht für User*innen einsehbar.</div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Hintergrund</b>
            <br>Trage hier alle weiteren wichtigen Hintergrundinformationen ein.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="background" id="background"></textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Material</b>
            <br>Hier kannst Du Material wie Bilder verlinken, die Du in die Quest einbauen willst. Vergiss die Quellenangaben nicht!
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="material" id="material"></textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Karten</b>
            <br>Hier kannst Du auf Karten verlinken, die Du nutzen willst.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="maps" id="maps"></textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Belohnungen</b>
            <br>Trage hier weitere Belohnungen ein, die die Charaktere finden können.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="treassure" id="treassure"></textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Endgegner</b>
            <br>Gib hier Informationen zum Endgegner an und wie man ihn besiegen kann. Halte es plausibel!
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="boss" id="boss"></textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Rätsel & Lösungen</b>
            <br>Erläutere hier Rätsel und Lösungen dazu, die die Charaktere im Ingame oder die User*innen zu knacken haben.
        </div>
        <div class="noticeboard_formblock-field">
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


// ## Alert - noticeboard_alert
$insert_array = array(
    'title'	    => 'noticeboard_alert',
    'template'	=> $db->escape_string('
<div class="red_alert">
    Jemand hat eine neue Quest ausgeschrieben!
    {$noticeboard_read}
</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Beschreibung - noticeboard_description
$insert_array = array(
    'title'	    => 'noticeboard_description',
    'template'	=> $db->escape_string('
<div class="noticeboard_description">
    <h1>Questtafel</h1>
    Hier könnte Deine Werbung stehen!
</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Bearbeiten - noticeboard_edit
$insert_array = array(
    'title'	    => 'noticeboard_edit',
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
<div class="noticeboard">
    <div class="noticeboard_navigation">
        {$navigation}
    </div>
    <div class="noticeboard_form">

    <form id="noticeboard" action="noticeboard.php?action=edit&nid={$noticeboard[\'nid\']}" method="post">
    <h1>Quest aufgeben</h1>

    <div class="noticeboard_description">Alle Felder mit einem * müssen ausgefüllt werden. Alle Felder im oberen Block sind für User*innen sichtbar, sobald die Quest auf "sichtbar" gestellt ist.</div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            Soll die Quest für alle sichtbar sein?* 
        </div>
        <div class="noticeboard_formblock-field-radio">
            <input type="radio" id="1" name="visible" value="1" {$checked_visible_1}>
                <label for="1">sichtbar</label>
            <input type="radio" id="0" name="visible" value="0" {$checked_visible_0}>
                <label for="0">unsichtbar</label>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            Questtitel
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="title" id="title" value="{$noticeboard[\'title\']}">
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Questtyp*</b>
            <br>Wähle aus der Liste aus, um welche Art von Quest es sich handelt.
        </div>
        <div class="noticeboard_formblock-field">
            <select name="type" id="type"  style="width: 100%;" required>
                <option value="">Wähle den Typ</option>
                <option value="AllgemeineQuest">Allgemeine Quest</option>
                <option value="Spezialquest">Spezialquest</option>
                <option value="Spezialquest">Spezialquest</option>
                <option value="BerufsbezogeneQuest">Berufsbezogene Quest</option>
                <option value="Sonstiges">Sonstiges</option>
            </select>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Kurzbeschreibung</b>
            <br>Gib hier eine kurze, aussagekräftige Beschreibung der Quest von max. 500 Zeichen an.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="shortdescription" id="shortdescription">{$noticeboard[\'shortdescription\']}</textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Questbeschreibung</b>
            <br>Gib hier eine ausführliche Beschreibung der Quest an. Die User*innen müssen im Zweifelsfall mit dieser Beschreibung die Quest bestreiten können.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="quest" id="quest">{$noticeboard[\'quest\']}</textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Questgeber*in</b>
            <br>Trage ein, von welchem Charakter oder NPC im Inplay die Quest kommt.
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="client" id="client" value="{$noticeboard[\'client\']}">
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Keywords</b>
            <br>Gib bis zu 5 Schlüsselbegriffe ein, die für die Quest relevant sind und trenne sie jeweils mit einem , (Komma).
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="keywords" id="keywords" value="{$noticeboard[\'keywords\']}">
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Fähigkeiten</b>
            <br>Gib hier Eigenschaften an, die mindestens ein Mitglied der Gruppe benötigt. Trenne die Eigenschaften mit , ab. Wenn sie für den Quest von Nachteil sind, setze eine 1 davor. Wenn sie bei dem Quest nicht zugelassen sind, setze eine 0 davor.
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="skills" id="skills"  value="{$noticeboard[\'skills\']}">
        </div>
    </div>
    
    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Spielort</b>
            <br>Gib den Spielort für die Quest an.
        </div>
        <div class="noticeboard_formblock-field">
            <select name="location" id="location"  style="width: 100%;">
                <option value="{$noticeboard[\'location\']}">{$noticeboard[\'location\']}</option>
                <option value="ort 1">Hogwarts</option>
                <option value="ort 2">Hogsmeade</option>
                <option value="ort 3">London</option>
                <option value="ort 4">Sonstiger Ort</option>
            </select>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Belohnung</b>
            <br>Gib an, wie viel die Questgeber*innen der Gruppe an Entlohnung versprechen. Das muss nicht mit der Belohnung übereinstimmen, die Du für sie vorsiehst.
        </div>
        <div class="noticeboard_formblock-field">
            <input type="text" name="reward" id="reward" value="{$noticeboard[\'reward\']}">
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Geleitet?</b>
            <br>Wähle aus, ob die Quest geleitet wird. Wenn sie frei geleitet wird, kannst Du sie selbst leiten. Trage Dich dann entsprechend ein. Wenn sie nicht geleitet wird, schreibe die Questsinformationen so, dass die User den Quest ohne weitere Informationen absolvieren können.
        </div>
        <div class="noticeboard_formblock-field">
            <select name="lead" id="lead" style="width: 100%;">
                <option value="{$noticeboard[\'lead\']}">{$noticeboard[\'lead\']}</option>
                <option value="<i class=\'fa-solid fa-eye\'></i>">geleitet</option>
                <option value="<i class=\'fa-regular fa-eye\'></i>">frei geleitet</option>
                <option value="<i class=\'fa-solid fa-eye-slash\'></i>">nicht geleitet</option>
            </select>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Schwierigkeitslevel</b>
            <br>Wähle aus, wie komplex die Quest zu spielen ist. Es geht nicht darum, wie schwer die Quest für die Charaktere ist, sondern wie viel er den Spielenden abverlangt.
        </div>
        <div class="noticeboard_formblock-field">
            <select name="level" id="level">
                <option value="{$noticeboard[\'level\']}">{$noticeboard[\'level\']}</option>
                <option value="<i class=\'fa-duotone fa-signal-bars-weak\'></i>">leicht</option>
                <option value="<i class=\'fa-duotone fa-signal-bars-fair\'></i>">mittel</option>
                <option value="<i class=\'fa-duotone fa-signal-bars-good\'></i>">schwer</option>
                <option value="<i class=\'fa-light fa-skull-crossbones\'></i>">tödlich</option>
            </select>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Monster</b>
            <br>Wähle aus, mit welchem Monster die Charaktere zu rechnen haben - insofern es eines gibt. Sie können auch andere Monster treffen (insbesondere den Bossgegner).
        </div>
        <div class="noticeboard_formblock-field">
		<input type="text" name="monster" id="monster" value="{$noticeboard[\'monster\']}">
        </div>
    </div>

    <h2>Informationen für die Spielleitung</h2>

    <div class="noticeboard_description">Diese Informationen sind nicht für User*innen einsehbar.</div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Hintergrund</b>
            <br>Trage hier alle weiteren wichtigen Hintergrundinformationen ein.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="background" id="background">{$noticeboard[\'background\']}</textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Material</b>
            <br>Hier kannst Du Material wie Bilder verlinken, die Du in die Quest einbauen willst. Vergiss die Quellenangaben nicht!
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="material" id="material">{$noticeboard[\'material\']}</textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Karten</b>
            <br>Hier kannst Du auf Karten verlinken, die Du nutzen willst.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="maps" id="maps">{$noticeboard[\'maps\']}</textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Belohnungen</b>
            <br>Trage hier weitere Belohnungen ein, die die Charaktere finden können. Wenn Du etwas Exotisches verteilen willst, sprich Dich mit der Spielleitung ab.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="treassure" id="treassure">{$noticeboard[\'treassure\']}</textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Endgegner</b>
            <br>Gib hier Informationen zum Endgegner an und wie man ihn besiegen kann. Halte es plausibel!
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="boss" id="boss">{$noticeboard[\'boss\']}</textarea>
        </div>
    </div>

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Rätsel & Lösungen</b>
            <br>Erläutere hier Rätsel und Lösungen dazu, die die Charaktere im Ingame oder die User*innen zu knacken haben.
        </div>
        <div class="noticeboard_formblock-field">
            <textarea name="solution" id="solution">{$noticeboard[\'solution\']}</textarea>
        </div>
    </div>

    {$edit_players}

    <div class="noticeboard_formblock">
        <div class="noticeboard_formblock-label">
            <b>Quest erledigt?</b>
            <br>Wurde die Quest erledigt?
        </div>
        <div class="noticeboard_formblock-field"></div>
            <input type="radio" id="1" name="status" value="1" {$checked_status_1}>
                <label for="1">erledigt</label>
            <input type="radio" id="0" name="status" value="0" {$checked_status_0}>
                <label for="0">nicht erledigt</label>
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


// ## Edit Button - noticeboard_edit_button
$insert_array = array(
    'title'	    => 'noticeboard_edit_button',
    'template'	=> $db->escape_string('
<div class="noticeboard_buttons">
    <div class="noticeboard_button"><a href="noticeboard.php?action=edit&nid={$noticeboard[\'nid\']}">Editieren</a> | <a href="noticeboard.php?action=delete&nid={$noticeboard[\'nid\']}">Löschen</a></div>
</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Edit Spieler - noticeboard_edit_players
$insert_array = array(
    'title'	    => 'noticeboard_edit_players',
    'template'	=> $db->escape_string('
<div class="noticeboard_formblock">
    <div class="noticeboard_formblock-label">
        <b>Charaktere ändern</b>
    </div>
    <div class="noticeboard_formblock-field-radio">
        <label>Charaktere</label>
        <input type="text" name="players" id="players" value="{$noticeboard[\'players\']}">
        <br><label>Szene</label>
        <input type="text" name="scene" id="scene" value="{$noticeboard[\'scene\']}">
    </div>
</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Navigation - noticeboard_navigation
$insert_array = array(
'title'	    => 'noticeboard_navigation',
'template'	=> $db->escape_string('
<div class="noticeboard_navigation">
    <div class="noticeboard_navigation-links"><a href="noticeboard.php">Über Quests</a></div>
    <div class="noticeboard_navigation-title">Übersicht</div>
    <div class="noticeboard_navigation-links">
        <div><a href="noticeboard.php?action=overview"><i class="fa-light fa-calendar-lines"></i> Alle Quests</a></div>
        <div><a href="noticeboard.php?action=free"><i class="fa-regular fa-circle"></i> freie Quests</a></div>
        <div><a href="noticeboard.php?action=taken"><i class="fa-regular fa-circle-half-stroke"></i> vergebene Quests</a></div>
        <div><a href="noticeboard.php?action=finished"><i class="fa-solid fa-circle"></i> erledigte Quests</a></div>
    </div>
    {$noticeboard_cp}
</div>
                '),
                'sid'       => '-2',
                'dateline'  => TIME_NOW
            );
            $db->insert_query("templates", $insert_array);


// ## Navigation CP - noticeboard_navigation_cp
$insert_array = array(
    'title'	    => 'noticeboard_navigation_cp',
    'template'	=> $db->escape_string('
<div class="noticeboard_navigation-title">Control Panel</div>
<div class="noticeboard_navigation-links">
    <div><a href="noticeboard.php?action=pending"><i class="fa-light fa-eye-low-vision"></i> nicht freigegebene Quests</a></div>
    <div><a href="noticeboard.php?action=all"><i class="fa-light fa-list"></i> alle Quests</a></div>
    <div><a href="noticeboard.php?action=add"><i class="fa-solid fa-plus"></i> Quest hinzufügen</a></div>
</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Navigation Keine Erlaubnis - noticeboard_no_permission
$insert_array = array(
    'title'	    => 'noticeboard_no_permission',
    'template'	=> $db->escape_string('
<div class="noticeboard_quest">Du hast keine Erlaubnis, dir die Quests anzuschauen.</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Quest - noticeboard_quest
$insert_array = array(
    'title'	    => 'noticeboard_quest',
    'template'	=> $db->escape_string('
    <div class="noticeboard_quest">
    <div class="noticeboard_header">{$noticeboard[\'type\']} {$status} 
        {$sl_information}</div>
    <div class="noticeboard_quest-title">
    <div class="noticeboard_quest-title-title">{$noticeboard[\'title\']}</div>
    <div class="noticeboard_quest-title-contributor">{$noticeboard[\'client\']}</div>
    </div>
    <div class="noticeboard_quest-content switch">
        <div class="noticeboard_quest-content-short short{$noticeboard[\'nid\']}">{$noticeboard[\'shortdescription\']}</div>
        <div class="noticeboard_quest-content-long long{$noticeboard[\'nid\']}">{$noticeboard[\'quest\']}</div>
    </div>
            <button class="button{$noticeboard[\'nid\']}">Mehr</button>
    <div class="noticeboard_quest-keywords">
        {$keywords}
    </div>
    <div class="noticeboard_quest-footer">
    <div class="noticeboard_quest-footer-feats">
    {$skills}
    </div>
    <div class="noticeboard_quest-footer-right">
        <div class="noticeboard_quest-footer-right-item">
        <div class="noticeboard_quest-footer-right-item-top">{$noticeboard[\'location\']}</div>
        <div class="noticeboard_quest-footer-right-item-bottom">Location</div>
        </div>
        <div class="noticeboard_quest-footer-right-item">
        <div class="noticeboard_quest-footer-right-item-top">{$noticeboard[\'lead\']}</div>
        <div class="noticeboard_quest-footer-right-item-bottom">Geleitet</div>
        </div>
        <div class="noticeboard_quest-footer-right-item">
            <div class="noticeboard_quest-footer-right-item-top">{$noticeboard[\'monster\']}</div>
            <div class="noticeboard_quest-footer-right-item-bottom">Monster</div>
        </div>
        <div class="noticeboard_quest-footer-right-item">
        <div class="noticeboard_quest-footer-right-item-top">{$noticeboard[\'reward\']} <i class="fa-light fa-coins"></i></div>
        <div class="noticeboard_quest-footer-right-item-bottom">Belohnung</div>
        </div>
        <div class="noticeboard_quest-footer-right-item">
        <div class="noticeboard_quest-footer-right-item-top noticeboard_quest-footer-level">{$noticeboard[\'level\']}</div>
        <div class="noticeboard_quest-footer-right-item-bottom">Level</div>
        </div>
    </div>
    
    </div>
    {$quest_status}
    {$edit}
    {$take}
    {$finished}
    </div>
    
    <script type="text/javascript">
    
    /* Kurzbeschreibung und lange Beschreibung Quest */
    
    $(document).ready(function(){
      $(".button{$noticeboard[\'nid\']}").click(function(){
        $(".long{$noticeboard[\'nid\']}").toggle(\'slow\');	
        $(".short{$noticeboard[\'nid\']}").toggle(\'slow\');
        });
    });
        
    </script>
    <style type="text/css">
     .long{$noticeboard[\'nid\']} {
         display: none;
        }
    </style>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Quest erledigt - noticeboard_quest_finished
$insert_array = array(
    'title'	    => 'noticeboard_quest_finished',
    'template'	=> $db->escape_string('
<div class="noticeboard_quest-content">
    Diese Quest wurde von {$noticeboard[\'players\']} <a href="{$noticeboard[\'scene\']}">hier</a> erledigt.
</div>        
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Keine Quests - noticeboard_quest_none
$insert_array = array(
    'title'	    => 'noticeboard_quest_none',
    'template'	=> $db->escape_string('
<div class="noticeboard_description">
    Derzeit gibt es keine Quests, die auf diese Suchkriterien passen. Du musst warten, bis neue Quests auf der Tafel erscheinen!
</div>       
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## SL Informationen - noticeboard_quest_sl
$insert_array = array(
    'title'	    => 'noticeboard_quest_sl',
    'template'	=> $db->escape_string('
<div class="noticeboard_hidden-content">
    <h1>Informationen für die Spielleitung</h1>

    Diese Seite ist nur für die Spielleitung sichtbar. Die Informationen sollen nicht außerhalb der vorgesehenen Reihenfolge an die Spielenden weitergegeben werden. Du kannst die Informationen bei Bedarf ergänzen, indem Du die Quest editierst.

    <h2>Hintergründe</h2>
    {$noticeboard[\'background\']}

    <h2>Zusatzmaterial</h2>
    {$noticeboard[\'material\']}

    <h2>Karten</h2>
    {$noticeboard[\'maps\']}

    <h2>Schätze & Belohnungen</h2>
    {$noticeboard[\'treassure\']}

    <h2>Endgegner</h2>
    {$noticeboard[\'boss\']}

    <h2>Lösung</h2>
    {$noticeboard[\'solution\']}

</div>    
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Kein Zugang zu SL Informationen - noticeboard_quest_sl_nope
$insert_array = array(
    'title'	    => 'noticeboard_quest_sl_nope',
    'template'	=> $db->escape_string('
<div class="noticeboard_hidden-content">
    <div class="noticeboard_description">
        Netter Versuch ... kein Cheaten!
    </div>
</div>   
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Quest annehmen - noticeboard_quest_take
$insert_array = array(
    'title'	    => 'noticeboard_quest_take',
    'template'	=> $db->escape_string('
<div class="noticeboard_quest-take">
	<h1>Quest annehmen <i class="fa-light fa-circle-info" title="Wenn du den Quest annehmen willst, trage hier den Namen der Charaktere ein, die daran beteiligt sind und verlinke die Szene für den Quest. Du kannst eine Quest erst annehmen, nachdem eine Szene erstellt wurde!"></i></h1>
	<form action="noticeboard.php?action=take&nid={$noticeboard[\'nid\']}" method="post">
		<b>Charaktere</b>
			<input type="text" name="players" id="players">
		<b>Szene</b>
		<input type="text" name="scene" id="scene">
		<input type="submit" value="Quest annehmen" name="take_quest" />
	</form>
</div> 
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Quest angenommen - noticeboard_quest_taken
$insert_array = array(
    'title'	    => 'noticeboard_quest_taken',
    'template'	=> $db->escape_string('
<div class="noticeboard_quest-taken">
    <b>{$noticeboard[\'players\']}</b> haben diese Quest <a href="{$noticeboard[\'scene\']}">hier</a> angenommen.
</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## SL Information - noticeboard_sl_information
$insert_array = array(
    'title'	    => 'noticeboard_sl_information',
    'template'	=> $db->escape_string('
    <button class="sl_button{$noticeboard[\'nid\']}"><i class="fa-regular fa-circle-info"></i></button>
    <div class="noticeboard_hidden-sl-information sl{$noticeboard[\'nid\']}"> 
    
    
    <div class="noticeboard_hidden-content">
        <h1>Informationen für die Spielleitung</h1>
    
        Diese Seite ist nur für die Spielleitung sichtbar. Die Informationen sollen nicht außerhalb der vorgesehenen Reihenfolge an die Spielenden weitergegeben werden. Du kannst die Informationen bei Bedarf ergänzen, indem Du die Quest editierst.
    
        <h2>Hintergründe</h2>
        {$noticeboard[\'background\']}
    
        <h2>Zusatzmaterial</h2>
        {$noticeboard[\'material\']}
    
        <h2>Karten</h2>
        {$noticeboard[\'maps\']}
    
        <h2>Schätze & Belohnungen</h2>
        {$noticeboard[\'treassure\']}
    
        <h2>Endgegner</h2>
        {$noticeboard[\'boss\']}
    
        <h2>Lösung</h2>
        {$noticeboard[\'solution\']}
    
    </div>
        
    </div>
    
    <script>
    $(document).ready(function(){
      $(".sl_button{$noticeboard[\'nid\']}").click(function(){
        $(".sl{$noticeboard[\'nid\']}").css(\'display\', \'block\');	
        });
    });	
        
    $(document).mouseup(function(e) 
    {
        var container = $(".sl{$noticeboard[\'nid\']}");
        if (!container.is(e.target) && container.has(e.target).length === 0) 
        {
            container.hide();
        }
    });
    </script>
    <style type="text/css">
     .sl{$noticeboard[\'nid\']} {
         display: none;
        }
    </style>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Status Quest erledigt - noticeboard_status_finished
$insert_array = array(
    'title'	    => 'noticeboard_status_finished',
    'template'	=> $db->escape_string('
<div class="noticeboard_quest-head-finished" title="Quest erledigt von {$noticeboard[\'players\']}"><i class="fa-solid fa-circle"></i></div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Status Quest frei - noticeboard_status_free
$insert_array = array(
    'title'	    => 'noticeboard_status_free',
    'template'	=> $db->escape_string('
<div class="noticeboard_quest-head-finished" title="Die Quest ist noch frei!"><i class="fa-regular fa-circle"></i></div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Status Quest angenommen - noticeboard_status_taken
$insert_array = array(
    'title'	    => 'noticeboard_status_taken',
    'template'	=> $db->escape_string('
    <div class="noticeboard_quest-head-taken" title="Quest angenommen von {$noticeboard[\'players\']}"><i class="fa-regular fa-circle-half-stroke"></i></div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## CSS 

$css = array(
    'name'  => 'noticeboard.css',
    'tid'   => 1,
    'attachedto' => '',
    "stylesheet" =>	'
    .noticeboard button {
    cursor: pointer;
    width: 100px;
    background: #294DA5;
    border: none;
}

.noticeboard b {
    color: #294DA5;
}

/* Popup*/

.sl {
    display: none;
}

.noticeboard_hidden-sl-information {
  position: absolute;
  z-index: 1;
  left: 25%;
  width: 1000px;
  background-color: rgba(190, 190, 190, 0.9);
  animation-name: animatetop;
  animation-duration: 0.4s;
}

@keyframes animatetop {
  from {
    top: -300px;
    opacity: 0;
  }
  to {
    top: 0;
    opacity: 1;
  }
}

.noticeboard_hidden-content {
  width: 1000px;
  margin: auto;
  padding: 50px;
  box-sizing: border-box;
}

.noticeboard {
  width: 100%;
  background: #fff;
  display: flex;
 gap: 40px;
  align-items: flex-start;
    
    font-family: Roboto, sans-serif;
}

.noticeboard_navigation {
    align-self: flex-start;
}

.noticeboard_navigation-title {
    width: 200px;
  background: #fafafa;
  padding: 20px;
  text-transform: uppercase;
  color: #294DA5;
  font-weight: bold;
}

.noticeboard_navigation-links {
  padding: 10px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  text-align: left;
}

/* #################### Forms #################### */

.noticeboard_form {
    flex-basis: 1000px;
    padding: 40px;
}

.noticeboard_formblock {
    margin: 20px 0;
}

.noticeboard_formblock-label {
    width: 80%;
}

.noticeboard_formblock-label b {
    color: #294DA5;
    text-transform: uppercase;
    font-size: 16px;
}

.noticeboard_formblock-field textarea {
    width: 80%;
    height: 250px;
}

.noticeboard_formblock-field select {
    width: 50%;
}

.noticeboard_formblock-field input {
    width: 300px;
}

.noticeboard_formblock-field-radio {
    width: 300px;
}

/* #################### Content #################### */

.noticeboard_content {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.noticeboard_description {
    padding: 40px;
    line-height: 180%;
}

.noticeboard_quest {
  display: flex;
  flex-direction: column;
  gap: 30px;
  width: 700px;
  margin: 30px auto;
  background: #fafafa;
  padding: 30px;
  box-sizing: border-box;
}

.noticeboard_header {
    display: flex;
    gap: 20px;
    justify-content: flex-end;
}

.noticeboard_quest-head-free,
.noticeboard_quest-head-taken,
.noticeboard_quest-head-finished
{
  align-self: flex-end;
    font-size: 30px;
    color: #294DA5;
}

.noticeboard_quest-title {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.noticeboard_quest-title-title {
  font-size: 25px;
  text-transform: uppercase;
}

.noticeboard_quest-title-contributor {
  color: #294DA5;
  text-transform: uppercase;
}

.noticeboard_quest-content {
}

.noticeboard_quest-keywords {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.noticeboard_quest-keywords div {
  background: #efefef;
  padding: 5px 20px;
  color: #294DA5;
  text-transform: uppercase;
}

.noticeboard_quest-footer {
  display: flex;
  justify-content: space-between;
}

.noticeboard_quest-footer-feats {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.noticeboard_quest-footer-feats div {
  background: #f8f8f8;
  padding: 5px 10px;
  color: #294DA5;
  font-size: 11px;
    text-align: left;
  text-transform: uppercase;
}

.noticeboard_quest-footer-right {
  display: flex;
  justify-content: flex-end;
  gap: 30px;
}

.noticeboard_quest-footer-right-item {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  align-items: center;
  text-transform: uppercase;
}

.noticeboard_quest-footer-right-item-top {
  color: #294DA5;
    text-align: center;
    font-size: 12px;
}

.noticeboard_quest-footer-level {
  display: flex;
  align-items: flex-end;
    font-size: 30px;
  gap: 5px;
}

.noticeboard_quest-footer-right-item-bottom {
}



/* ################### Szene annehmen ###################### */

.noticeboard_quest-take b {
    color: #294DA5;
}

.noticeboard_quest-taken b {
    color: #294DA5;
    text-transform: uppercase;
}

.noticeboard_quest-take input {
    width: 20%;
}

    ',
    'cachefile'     => $db->escape_string(str_replace('/', '', 'noticeboard.css')),
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

function noticeboard_is_installed()
{
    global $db, $cache, $mybb;
  
      if($db->table_exists("noticeboard"))  {
        return true;
      }
        return false;
}

// Deinstallieren

function noticeboard_uninstall()
{
  global $db;

    // DB löschen
    if($db->table_exists("noticeboard"))
    {
        $db->drop_table("noticeboard");
    }

    // Änderung in User Tabelle löschen
    if($db->field_exists("noticeboard_new", "users"))
    {
        $db->drop_column("users", "noticeboard_new");
    }
    
    // Einstellungen löschen
    $db->delete_query('settings', "name LIKE 'noticeboard%'");
    $db->delete_query('settinggroups', "name = 'noticeboard'");

    rebuild_settings();

    // Templates löschen
    $db->delete_query("templategroups", "prefix = 'noticeboard'");
    $db->delete_query("templates", "title LIKE '%noticeboard%'");

    // CSS löschen
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
	$db->delete_query("themestylesheets", "name = 'noticeboard.css'");
	$query = $db->simple_select("themes", "tid");
	while($theme = $db->fetch_array($query)) {
		update_theme_stylesheet_list($theme['tid']);
	}
}

// Plugin aktivieren

function noticeboard_activate()
{
    global $db, $cache;
    
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

    // Variable für den Alert im Header

    find_replace_templatesets('header', '#'.preg_quote('{$bbclosedwarning}').'#', '{$noticeboard_new} {$bbclosedwarning}');
}

function noticeboard_deactivate()
{
     global $db, $cache;

    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

    // Variablen für den Alert im Header entfernen

    find_replace_templatesets('header', '#'.preg_quote('{$noticeboard_new} {$bbclosedwarning}').'#', '{$bbclosedwarning}');
}


// Hook
$plugins->add_hook('global_start', 'noticeboard_global');

function noticeboard_global(){

    global $db, $mybb, $templates, $noticeboard_new, $new_noticeboard, $noticeboard_read, $lang;

    if(is_member($mybb->settings['noticeboard_allow_groups_see'])) {

        $lang->load('noticeboard');

        $uid = $mybb->user['uid'];

        $noticeboard_read = "<a href='misc.php?action=noticeboard_read&read={$uid}' original-title='als gelesen markieren'><i class=\"fas fa-trash\" style=\"float: right;font-size: 14px;padding: 1px;\"></i></a>";

            // User hat Info auf dem Index gelesen

            if ($mybb->get_input ('action') == 'noticeboard_read') {

                $this_user = intval ($mybb->user['uid']);

                $as_uid = intval ($mybb->user['as_uid']);
                $read = $mybb->input['read'];
                if ($read) {
                    if($as_uid == 0){
                        $db->query ("UPDATE ".TABLE_PREFIX."users SET noticeboard_new = 1  WHERE (as_uid = $this_user) OR (uid = $this_user)");
                    }elseif ($as_uid != 0){
                        $db->query ("UPDATE ".TABLE_PREFIX."users SET noticeboard_new = 1  WHERE (as_uid = $as_uid) OR (uid = $this_user) OR (uid = $as_uid)");
                    }
                    redirect("index.php");
                }
            }
    }

    $select = $db->query ("SELECT * FROM " . TABLE_PREFIX ."noticeboard WHERE visible = 1");
    $row_cnt = mysqli_num_rows ($select);
    if ($row_cnt > 0) {
        $select = $db->query ("SELECT noticeboard_new FROM " . TABLE_PREFIX . "users 
        WHERE uid = '" . $mybb->user['uid'] . "' LIMIT 1");


        $data = $db->fetch_array ($select);
        if (isset($data['noticeboard_new']) == '0') {

            eval("\$new_noticeboard = \"" . $templates->get ("noticeboard_alert") . "\";");

        }
            
    }

}


// WER IST ONLINE Anzeige


$plugins->add_hook("fetch_wol_activity_end", "noticeboard_online_activity");
$plugins->add_hook("build_friendly_wol_location_end", "noticeboard_online_location");

function noticeboard_online_activity($user_activity) {
global $parameters;

    $split_loc = explode(".php", $user_activity['location']);
    if($split_loc[0] == $user['location']) {
        $filename = '';
    } else {
        $filename = my_substr($split_loc[0], -my_strpos(strrev($split_loc[0]), "/"));
    }
    
    switch ($filename) {
        case 'noticeboard':
        if($parameters['action'] == "" && empty($parameters['site'])) {
            $user_activity['activity'] = "noticeboard";
        }
        if($parameters['action'] == "overview" && empty($parameters['site'])) {
            $user_activity['activity'] = "overview";
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
        if($parameters['action'] == "edit" && empty($parameters['site'])) {
            $user_activity['activity'] = "edit";
        }
        break;
    }
      
return $user_activity;
}

function noticeboard_online_location($plugin_array) {
global $mybb, $theme, $lang;

    if($plugin_array['user_activity']['activity'] == "noticeboard") {
        $plugin_array['location_name'] = "Betrachtet die <a href=\"noticeboard.php\">Questtafel</a>.";
    }
	if($plugin_array['user_activity']['activity'] == "overview") {
		$plugin_array['location_name'] = "Studiert die <a href=\"noticeboard.php?action=overview\">Quests</a>.";
	}
    if($plugin_array['user_activity']['activity'] == "free") {
		$plugin_array['location_name'] = "Sieht sich freie <a href=\"noticeboard.php?action=free\">Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "taken") {
		$plugin_array['location_name'] = "Sieht sich vergebene <a href=\"noticeboard.php?action=taken\">Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "finished") {
		$plugin_array['location_name'] = "Sieht sich erledigte <a href=\"noticeboard.php?action=finished\">Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "add") {
		$plugin_array['location_name'] = "Pinnt eine neue Quest an.";
	}
    if($plugin_array['user_activity']['activity'] == "edit") {
		$plugin_array['location_name'] = "Bessert Fehler in einer Quest aus.";
	}

return $plugin_array;
}
