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
		"description"	=> "Ein Questplugin für Quests",
		"author"		=> "white_rabbit",
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

    $db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `questboard_new` int(11) NOT NULL DEFAULT '0';");
    

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
            'description' => 'Welche Gruppen dürfen Quests sehen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 1
        ),

        'questboard_allow_groups_see_all' => array(
            'title' => 'Nicht freigegebene Quests sichtbar für',
            'description' => 'Welche Gruppen dürfen nicht freigegebene Quests sehen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 2
        ),

        'questboard_allow_groups_add' => array(
            'title' => 'Quests erstellen',
            'description' => 'Welche Gruppen dürfen Quests erstellen?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 3
        ),

        'questboard_allow_groups_take' => array(
            'title' => 'Quests annehmen',
            'description' => 'Welche Gruppen dürfen Quests annehmen?',
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
            'description' => 'Welche Gruppen dürfen Quests bearbeiten?',
            'optionscode' => 'groupselect',
            'value' => '4', // Default
            'disporder' => 6
        ),
	
	'questboard_allow_groups_lead' => array(
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
    <h1>Quest aufgeben</h1>

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
            <select name="type" id="type"  style="width: 100%;" required>
                <option value="">Wähle den Typ</option>
                <option value="AllgemeineQuest">Allgemeine Quest</option>
                <option value="Specialquest">Specialquest</option>
                <option value="Singlequest">Singlequest</option>
                <option value="BerufsbezogeneQuest">Berufsbezogene Quest</option>
                <option value="Sonstiges">Sonstiges</option>
            </select>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Kurzbeschreibung</b>
            <br>Gib hier eine kurze, aussagekräftige Beschreibung der Quest von max. 500 Zeichen an.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="shortdescription" id="shortdescription"></textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Questsbeschreibung</b>
            <br>Gib hier eine ausführliche Beschreibung der Quest an. Die User*innen müssen im Zweifelsfall mit dieser Beschreibung die Quest bestreiten können.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="quest" id="quest"></textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Questgeber*in</b>
            <br>Trage ein, von welchem Charakter oder NPC im Inplay die Quest kommt.
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="client" id="client">
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Keywords</b>
            <br>Gib bis zu 5 Schlüsselbegriffe ein, die für die Quest relevant sind und trenne sie jeweils mit einem , (Komma).
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="keywords" id="keywords">
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
            <input type="text" name="reward" id="reward">
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Geleitet?</b>
            <br>Wähle aus, ob die Quest geleitet wird. Wenn sie nicht geleitet wird, schreibe die Questinformationen so, dass die User die Quest ohne weitere Informationen absolvieren können.
        </div>
        <div class="questboard_formblock-field">
            <select name="lead" id="lead" style="width: 100%;">
                <option>Wähle eine Leitung aus</option>
                <option value="<i class=\'fa-solid fa-eye\'></i>">geleitet</option>
                <option value="<i class=\'fa-regular fa-eye\'></i>">frei geleitet</option>
                <option value="<i class=\'fa-solid fa-eye-slash\'></i>">nicht geleitet</option>
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

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Monster</b>
            <br>Gib an, mit welchem Monster die Charaktere zu rechnen haben - sofern es eines gibt. Sie können auch auf andere Monster treffen (insbesondere den Bossgegner).
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="monster" id="monster">
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
            <textarea name="treassure" id="treassure"></textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Endgegner</b>
            <br>Gib hier Informationen zum Endgegner an und wie man ihn besiegen kann. Halte es plausibel!
        </div>
        <div class="questboard_formblock-field">
            <textarea name="boss" id="boss"></textarea>
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


// ## Alert - questboard_alert
$insert_array = array(
    'title'	    => 'questboard_alert',
    'template'	=> $db->escape_string('
<div class="red_alert">
    Jemand hat eine neue Quest ausgeschrieben!
    {$questboard_read}
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
    Hier könnte Deine Werbung stehen!
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
    <h1>Quest aufgeben</h1>

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
                <option value="">Wähle den Typ</option>
                <option value="AllgemeineQuest">Allgemeine Quest</option>
                <option value="Specialquest">Specialquest</option>
                <option value="Singlequest">Singlequest</option>
                <option value="BerufsbezogeneQuest">Berufsbezogene Quest</option>
                <option value="Sonstiges">Sonstiges</option>
            </select>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Kurzbeschreibung</b>
            <br>Gib hier eine kurze, aussagekräftige Beschreibung der Quest von max. 500 Zeichen an.
        </div>
        <div class="questboard_formblock-field">
            <textarea name="shortdescription" id="shortdescription">{$questboard[\'shortdescription\']}</textarea>
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
            <b>Questgeber*in</b>
            <br>Trage ein, von welchem Charakter oder NPC im Inplay die Quest kommt.
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="client" id="client" value="{$questboard[\'client\']}">
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Keywords</b>
            <br>Gib bis zu 5 Schlüsselbegriffe ein, die für die Quest relevant sind und trenne sie jeweils mit einem , (Komma).
        </div>
        <div class="questboard_formblock-field">
            <input type="text" name="keywords" id="keywords" value="{$questboard[\'keywords\']}">
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
                <option value="<i class=\'fa-solid fa-eye\'></i>">geleitet</option>
                <option value="<i class=\'fa-regular fa-eye\'></i>">frei geleitet</option>
                <option value="<i class=\'fa-solid fa-eye-slash\'></i>">nicht geleitet</option>
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
                <option value="<i class=\'fa-duotone fa-signal-bars-weak\'></i>">leicht</option>
                <option value="<i class=\'fa-duotone fa-signal-bars-fair\'></i>">mittel</option>
                <option value="<i class=\'fa-duotone fa-signal-bars-good\'></i>">schwer</option>
                <option value="<i class=\'fa-light fa-skull-crossbones\'></i>">tödlich</option>
            </select>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Monster</b>
            <br>Wähle aus, mit welchem Monster die Charaktere zu rechnen haben - insofern es eines gibt. Sie können auch andere Monster treffen (insbesondere den Bossgegner).
        </div>
        <div class="questboard_formblock-field">
		<input type="text" name="monster" id="monster" value="{$questboard[\'monster\']}">
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
            <textarea name="treassure" id="treassure">{$questboard[\'treassure\']}</textarea>
        </div>
    </div>

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Endgegner</b>
            <br>Gib hier Informationen zum Endgegner an und wie man ihn besiegen kann. Halte es plausibel!
        </div>
        <div class="questboard_formblock-field">
            <textarea name="boss" id="boss">{$questboard[\'boss\']}</textarea>
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

    {$edit_players}

    <div class="questboard_formblock">
        <div class="questboard_formblock-label">
            <b>Quest erledigt?</b>
            <br>Wurde die Quest erledigt?
        </div>
        <div class="questboard_formblock-field"></div>
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


// ## Edit Spieler - questboard_edit_players
$insert_array = array(
    'title'	    => 'questboard_edit_players',
    'template'	=> $db->escape_string('
<div class="questboard_formblock">
    <div class="questboard_formblock-label">
        <b>Charaktere ändern</b>
    </div>
    <div class="questboard_formblock-field-radio">
        <label>Charaktere</label>
        <input type="text" name="players" id="players" value="{$questboard[\'players\']}">
        <br><label>Szene</label>
        <input type="text" name="scene" id="scene" value="{$questboard[\'scene\']}">
    </div>
</div>
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
    <div class="questboard_navigation-links"><a href="questboard.php">Über Quests</a></div>
    <div class="questboard_navigation-title">Übersicht</div>
    <div class="questboard_navigation-links">
        <div><a href="questboard.php?action=overview"><i class="fa-light fa-calendar-lines"></i> Alle Quests</a></div>
        <div><a href="questboard.php?action=free"><i class="fa-regular fa-circle"></i> freie Quests</a></div>
        <div><a href="questboard.php?action=taken"><i class="fa-regular fa-circle-half-stroke"></i> vergebene Quests</a></div>
        <div><a href="questboard.php?action=finished"><i class="fa-solid fa-circle"></i> erledigte Quests</a></div>
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
    <div><a href="questboard.php?action=pending"><i class="fa-light fa-eye-low-vision"></i> nicht freigegebene Quests</a></div>
    <div><a href="questboard.php?action=all"><i class="fa-light fa-list"></i> alle Quests</a></div>
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
    <div class="questboard_header">{$questboard[\'type\']} {$status} 
        {$sl_information}</div>
    <div class="questboard_quest-title">
    <div class="questboard_quest-title-title">{$questboard[\'title\']}</div>
    <div class="questboard_quest-title-contributor">{$questboard[\'client\']}</div>
    </div>
    <div class="questboard_quest-content switch">
        <div class="questboard_quest-content-short short{$questboard[\'nid\']}">{$questboard[\'shortdescription\']}</div>
        <div class="questboard_quest-content-long long{$questboard[\'nid\']}">{$questboard[\'quest\']}</div>
    </div>
            <button class="button{$questboard[\'nid\']}">Mehr</button>
    <div class="questboard_quest-keywords">
        {$keywords}
    </div>
    <div class="questboard_quest-footer">
    <div class="questboard_quest-footer-feats">
    {$skills}
    </div>
    <div class="questboard_quest-footer-right">
        <div class="questboard_quest-footer-right-item">
        <div class="questboard_quest-footer-right-item-top">{$questboard[\'location\']}</div>
        <div class="questboard_quest-footer-right-item-bottom">Location</div>
        </div>
        <div class="questboard_quest-footer-right-item">
        <div class="questboard_quest-footer-right-item-top">{$questboard[\'lead\']}</div>
        <div class="questboard_quest-footer-right-item-bottom">Geleitet</div>
        </div>
        <div class="questboard_quest-footer-right-item">
            <div class="questboard_quest-footer-right-item-top">{$questboard[\'monster\']}</div>
            <div class="questboard_quest-footer-right-item-bottom">Monster</div>
        </div>
        <div class="questboard_quest-footer-right-item">
        <div class="questboard_quest-footer-right-item-top">{$questboard[\'reward\']}</div>
        <div class="questboard_quest-footer-right-item-bottom">Belohnung</div>
        </div>
        <div class="questboard_quest-footer-right-item">
        <div class="questboard_quest-footer-right-item-top questboard_quest-footer-level">{$questboard[\'level\']}</div>
        <div class="questboard_quest-footer-right-item-bottom">Level</div>
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
      $(".button{$questboard[\'nid\']}").click(function(){
        $(".long{$questboard[\'nid\']}").toggle(\'slow\');	
        $(".short{$questboard[\'nid\']}").toggle(\'slow\');
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


// ## Quest erledigt - questboard_quest_finished
$insert_array = array(
    'title'	    => 'questboard_quest_finished',
    'template'	=> $db->escape_string('
<div class="questboard_quest-content">
    Diese Quest wurde von {$questboard[\'players\']} <a href="{$questboard[\'scene\']}">hier</a> erledigt.
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


// ## SL Informationen - questboard_quest_sl
$insert_array = array(
    'title'	    => 'questboard_quest_sl',
    'template'	=> $db->escape_string('
<div class="questboard_hidden-content">
    <h1>Informationen für die Spielleitung</h1>

    Diese Seite ist nur für die Spielleitung sichtbar. Die Informationen sollen nicht außerhalb der vorgesehenen Reihenfolge an die Spielenden weitergegeben werden. Du kannst die Informationen bei Bedarf ergänzen, indem Du die Quest editierst.

    <h2>Hintergründe</h2>
    {$questboard[\'background\']}

    <h2>Zusatzmaterial</h2>
    {$questboard[\'material\']}

    <h2>Karten</h2>
    {$questboard[\'maps\']}

    <h2>Schätze & Belohnungen</h2>
    {$questboard[\'treassure\']}

    <h2>Endgegner</h2>
    {$questboard[\'boss\']}

    <h2>Lösung</h2>
    {$questboard[\'solution\']}

</div>    
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Kein Zugang zu SL Informationen - questboard_quest_sl_nope
$insert_array = array(
    'title'	    => 'questboard_quest_sl_nope',
    'template'	=> $db->escape_string('
<div class="questboard_hidden-content">
    <div class="questboard_description">
        Netter Versuch ... kein Cheaten!
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
<div class="questboard_quest-take">
	<h1>Quest annehmen</h1>
	<form action="questboard.php?action=take&nid={$questboard[\'nid\']}" method="post">
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


// ## Quest angenommen - questboard_quest_taken
$insert_array = array(
    'title'	    => 'questboard_quest_taken',
    'template'	=> $db->escape_string('
<div class="questboard_quest-taken">
    <b>{$questboard[\'players\']}</b> haben diese Quest <a href="{$questboard[\'scene\']}">hier</a> angenommen.
</div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## SL Information - questboard_sl_information
$insert_array = array(
    'title'	    => 'questboard_sl_information',
    'template'	=> $db->escape_string('
    <button class="sl_button{$questboard[\'nid\']}">SL-Infos</i></button>
    <div class="questboard_hidden-sl-information sl{$questboard[\'nid\']}"> 
    
    
    <div class="questboard_hidden-content">
        <h1>Informationen für die Spielleitung</h1>
    
        Diese Seite ist nur für die Spielleitung sichtbar. Die Informationen sollen nicht außerhalb der vorgesehenen Reihenfolge an die Spielenden weitergegeben werden. Du kannst die Informationen bei Bedarf ergänzen, indem Du die Quest editierst.
    
        <h2>Hintergründe</h2>
        {$questboard[\'background\']}
    
        <h2>Zusatzmaterial</h2>
        {$questboard[\'material\']}
    
        <h2>Karten</h2>
        {$questboard[\'maps\']}
    
        <h2>Schätze & Belohnungen</h2>
        {$questboard[\'treassure\']}
    
        <h2>Endgegner</h2>
        {$questboard[\'boss\']}
    
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


// ## Status Quest erledigt - questboard_status_finished
$insert_array = array(
    'title'	    => 'questboard_status_finished',
    'template'	=> $db->escape_string('
<div class="questboard_quest-head-finished" title="Quest erledigt von {$questboard[\'players\']}"><i class="fa-solid fa-circle"></i></div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Status Quest frei - questboard_status_free
$insert_array = array(
    'title'	    => 'questboard_status_free',
    'template'	=> $db->escape_string('
<div class="questboard_quest-head-finished" title="Die Quest ist noch frei!"><i class="fa-regular fa-circle"></i></div>
    '),
    'sid'       => '-2',
    'dateline'  => TIME_NOW
);
$db->insert_query("templates", $insert_array);


// ## Status Quest angenommen - questboard_status_taken
$insert_array = array(
    'title'	    => 'questboard_status_taken',
    'template'	=> $db->escape_string('
    <div class="questboard_quest-head-taken" title="Quest angenommen von {$questboard[\'players\']}"><i class="fa-regular fa-circle-half-stroke"></i></div>
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
    .questboard button {
    cursor: pointer;
    width: 100px;
    background: #294DA5;
    border: none;
}

.questboard b {
    color: #294DA5;
}

/* Popup*/

.sl {
    display: none;
}

.questboard_hidden-sl-information {
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

.questboard_hidden-content {
  width: 1000px;
  margin: auto;
  padding: 50px;
  box-sizing: border-box;
}

.questboard {
  width: 100%;
  background: #fff;
  display: flex;
 gap: 40px;
  align-items: flex-start;
    
    font-family: Roboto, sans-serif;
}

.questboard_navigation {
    align-self: flex-start;
}

.questboard_navigation-title {
    width: 200px;
  background: #fafafa;
  padding: 20px;
  text-transform: uppercase;
  color: #294DA5;
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
    color: #294DA5;
    text-transform: uppercase;
    font-size: 16px;
}

.questboard_formblock-field textarea {
    width: 80%;
    height: 250px;
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

/* #################### Content #################### */

.questboard_content {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.questboard_description {
    padding: 40px;
    line-height: 180%;
}

.questboard_quest {
  display: flex;
  flex-direction: column;
  gap: 30px;
  width: 700px;
  margin: 30px auto;
  background: #fafafa;
  padding: 30px;
  box-sizing: border-box;
}

.questboard_header {
    display: flex;
    gap: 20px;
    justify-content: flex-end;
    align-items: center;
}

.questboard_quest-head-free,
.questboard_quest-head-taken,
.questboard_quest-head-finished
{
  align-self: flex-end;
    font-size: 30px;
    color: #294DA5;
}

.questboard_quest-title {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.questboard_quest-title-title {
  font-size: 25px;
  text-transform: uppercase;
}

.questboard_quest-title-contributor {
  color: #294DA5;
  text-transform: uppercase;
}

.questboard_quest-content {
}

.questboard_quest-keywords {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.questboard_quest-keywords div {
  background: #efefef;
  padding: 5px 20px;
  color: #294DA5;
  text-transform: uppercase;
}

.questboard_quest-footer {
  display: flex;
  justify-content: space-between;
}

.questboard_quest-footer-feats {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.questboard_quest-footer-feats div {
  background: #f8f8f8;
  padding: 5px 10px;
  color: #294DA5;
  font-size: 11px;
    text-align: left;
  text-transform: uppercase;
}

.questboard_quest-footer-right {
  display: flex;
  justify-content: flex-end;
  gap: 30px;
}

.questboard_quest-footer-right-item {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  align-items: center;
  text-transform: uppercase;
}

.questboard_quest-footer-right-item-top {
  color: #294DA5;
    text-align: center;
    font-size: 12px;
}

.questboard_quest-footer-level {
  display: flex;
  align-items: flex-end;
    font-size: 30px;
  gap: 5px;
}

.questboard_quest-footer-right-item-bottom {
}



/* ################### Szene annehmen ###################### */

.questboard_quest-take b {
    color: #294DA5;
}

.questboard_quest-taken b {
    color: #294DA5;
    text-transform: uppercase;
}

.questboard_quest-take input {
    width: 20%;
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

    find_replace_templatesets('header', '#'.preg_quote('{$bbclosedwarning}').'#', '{$questboard_new} {$bbclosedwarning}');
}

function questboard_deactivate()
{
     global $db, $cache;

    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

    // Variablen für den Alert im Header entfernen

    find_replace_templatesets('header', '#'.preg_quote('{$questboard_new} {$bbclosedwarning}').'#', '{$bbclosedwarning}');
}


// Hook
$plugins->add_hook('global_start', 'questboard_global');

function questboard_global(){

    global $db, $mybb, $templates, $questboard_new, $new_questboard, $questboard_read, $lang;

    if(is_member($mybb->settings['questboard_allow_groups_see'])) {

        $lang->load('questboard');

        $uid = $mybb->user['uid'];

        $questboard_read = "<a href='misc.php?action=questboard_read&read={$uid}' original-title='als gelesen markieren'><i class=\"fas fa-trash\" style=\"float: right;font-size: 14px;padding: 1px;\"></i></a>";

            // User hat Info auf dem Index gelesen

            if ($mybb->get_input ('action') == 'questboard_read') {

                $this_user = intval ($mybb->user['uid']);

                $as_uid = intval ($mybb->user['as_uid']);
                $read = $mybb->input['read'];
                if ($read) {
                    if($as_uid == 0){
                        $db->query ("UPDATE ".TABLE_PREFIX."users SET questboard_new = 1  WHERE (as_uid = $this_user) OR (uid = $this_user)");
                    }elseif ($as_uid != 0){
                        $db->query ("UPDATE ".TABLE_PREFIX."users SET questboard_new = 1  WHERE (as_uid = $as_uid) OR (uid = $this_user) OR (uid = $as_uid)");
                    }
                    redirect("index.php");
                }
            }
    }

    $select = $db->query ("SELECT * FROM " . TABLE_PREFIX ."questboard WHERE visible = 1");
    $row_cnt = $select->rowCount();
    if ($row_cnt > 0) {
        $select = $db->query ("SELECT questboard_new FROM " . TABLE_PREFIX . "users 
        WHERE uid = '" . $mybb->user['uid'] . "' LIMIT 1");


        $data = $db->fetch_array ($select);
        if (isset($data['questboard_new']) == '0') {

            eval("\$new_questboard = \"" . $templates->get ("questboard_alert") . "\";");

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

function questboard_online_location($plugin_array) {
global $mybb, $theme, $lang;

    if($plugin_array['user_activity']['activity'] == "questboard") {
        $plugin_array['location_name'] = "Betrachtet die <a href=\"questboard.php\">Questtafel</a>.";
    }
	if($plugin_array['user_activity']['activity'] == "overview") {
		$plugin_array['location_name'] = "Studiert die <a href=\"questboard.php?action=overview\">Quests</a>.";
	}
    if($plugin_array['user_activity']['activity'] == "free") {
		$plugin_array['location_name'] = "Sieht sich freie <a href=\"questboard.php?action=free\">Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "taken") {
		$plugin_array['location_name'] = "Sieht sich vergebene <a href=\"questboard.php?action=taken\">Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "finished") {
		$plugin_array['location_name'] = "Sieht sich erledigte <a href=\"questboard.php?action=finished\">Quests</a> an.";
	}
    if($plugin_array['user_activity']['activity'] == "add") {
		$plugin_array['location_name'] = "Pinnt eine neue Quest an.";
	}
    if($plugin_array['user_activity']['activity'] == "edit") {
		$plugin_array['location_name'] = "Bessert Fehler in einer Quest aus.";
	}

return $plugin_array;
}
