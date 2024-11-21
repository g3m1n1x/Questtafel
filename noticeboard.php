<?php

define('IN_MYBB', 1);
require_once './global.php';

global $db, $cache, $mybb, $lang, $templates, $theme, $header, $headerinclude, $footer;

$lang->load('noticeboard');

// ########### Seiten aufbauen ####################

// Allgemeine Seite

add_breadcrumb("Questtafel", "noticeboard.php");

// ### NAVIGATION

// CP nur für Gruppen mit Rechten sichtbar
if(is_member($mybb->settings['noticeboard_allow_groups_see_all'])) {
    eval("\$noticeboard_cp = \"". $templates->get("noticeboard_navigation_cp")."\";");
}
else {
    $noticeboard_cp = "";
}

eval("\$navigation = \"".$templates->get("noticeboard_navigation")."\";");


// Usernamen für Questersteller aufbauen

$uid = $mybb->user['uid'];

$username = format_name($user['username'], $user['usergroup'], $user['displaygroup']);

// ### FUNKTIONEN UND CO. ###

// Quests Status

function noticeboard_status() {
    $status = "";

    if($noticeboard['status'] == "0" && $noticeboard['players'] == "") {
        eval("\$status = \"".$templates->get("noticeboard_status_free")."\";");
    }
    elseif($noticeboard['status'] == "0" && $noticeboard['players'] != "") {
        eval("\$status = \"".$templates->get("noticeboard_status_taken")."\";");
    }
    elseif($noticeboard['status'] == "1") {
        eval("\$status = \"".$templates->get("noticeboard_status_finished")."\";");
    }
}
 
// Standardseite mit Erklärung

if(is_member($mybb->settings['noticeboard_allow_groups_access'])) {

if(!$mybb->input['action']) {

    add_breadcrumb("Erklärung");

    eval("\$description = \"".$templates->get("noticeboard_description")."\";");
    eval("\$page = \"".$templates->get("noticeboard")."\";");
    output_page($page);
}

// Übersicht über die freigeschalteten Quests

    if($mybb->input['action'] == "overview") {

        if(is_member($mybb->settings['noticeboard_allow_groups_see'])) {

        add_breadcrumb("Übersicht über die Quests");

        eval("\$none = \"".$templates->get("noticeboard_quest_none")."\";");
        

            $sql = "SELECT * FROM ".TABLE_PREFIX."noticeboard WHERE visible = 1";
            $query = $db->query($sql);
            while($noticeboard = $db->fetch_array($query)) { 

                $none = "";

                $keywords = '<div>'.str_replace(', ', '</div><div>', $noticeboard['keywords']).'</div>';

                
                $skill = '<div>'.str_replace(', ', '</div><div>', $noticeboard['skills']).'</div>';
                $skills = str_replace(
                    array("1", "0"),
                    array(
                    "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                    "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                    ),
                    $skill
                );

                $status = "";

               
                $finished = "";
                

                    if($noticeboard['status'] == "0" && $noticeboard['players'] == "") {
                        if(is_member($mybb->settings['noticeboard_allow_groups_take'])) {
                            $take = "";
                            eval("\$take = \"".$templates->get("noticeboard_quest_take")."\";");
                        }
                        else {
                            $take = "";
                        }
                        eval("\$status = \"".$templates->get("noticeboard_status_free")."\";");

                    }
                    elseif($noticeboard['status'] == "0" && $noticeboard['players'] != "") {
                        eval("\$status = \"".$templates->get("noticeboard_status_taken")."\";");
                        eval("\$take = \"".$templates->get("noticeboard_quest_taken")."\";");
                    }
                    elseif($noticeboard['status'] == "1" && $noticeboard['players'] != "") {
                        eval("\$status = \"".$templates->get("noticeboard_status_finished")."\";");
                        eval("\$finished = \"".$templates->get("noticeboard_quest_finished")."\";");
                    }

                if(is_member($mybb->settings['noticeboard_allow_groups_edit'])) {
                    $edit = "";
                    eval("\$edit .= \"".$templates->get("noticeboard_edit_button")."\";");
                }
                else {
                    $edit = "";
                }

                if(is_member($mybb->settings['noticeboard_allow_groups_lead'])) {
                    $sl_information = "";
                    eval("\$sl_information .= \"".$templates->get("noticeboard_sl_information")."\";");
                }
                else {
                    $sl_information = "";
                }
                eval("\$bit .= \"".$templates->get("noticeboard_quest")."\";");
    
            };  
        }
        else {
                eval("\$bit = \"".$templates->get("noticeboard_no_permission")."\";");
        }
    eval("\$page = \"".$templates->get("noticeboard")."\";");
        output_page($page);
}


// Übersicht über freie Quests


    if($mybb->input['action'] == "free") {

        if(is_member($mybb->settings['noticeboard_allow_groups_see'])) {

        add_breadcrumb("Freie Quests");

        eval("\$none = \"".$templates->get("noticeboard_quest_none")."\";");

            $sql = "SELECT * FROM ".TABLE_PREFIX."noticeboard WHERE visible = 1 && (players IS NULL OR players = '')";
            $query = $db->query($sql);
            while($noticeboard = $db->fetch_array($query)) {
                $none = "";

                $keywords = '<div>'.str_replace(', ', '</div><div>', $noticeboard['keywords']).'</div>';

                $skill = '<div>'.str_replace(', ', '</div><div>', $noticeboard['skills']).'</div>';
                $skills = str_replace(
                    array("0", "1"),
                    array(
                    "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                    "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                    ),
                    $skill
                );
               

                $take = "";
                $finished = "";

                if($noticeboard['players'] == "") {
                    if(is_member($mybb->settings['noticeboard_allow_groups_take'])) {
                        $take = "";
                        eval("\$take = \"".$templates->get("noticeboard_quest_take")."\";");
                    }
                    else {
                        $take = "";
                    }
                }
                else {
                    eval("\$take = \"".$templates->get("noticeboard_quest_taken")."\";");
                }

                if(is_member($mybb->settings['noticeboard_allow_groups_edit'])) {
                    $edit = "";
                    eval("\$edit .= \"".$templates->get("noticeboard_edit_button")."\";");
                }
                else {
                    $edit = "";
                }

                if(is_member($mybb->settings['noticeboard_allow_groups_lead'])) {
                    $sl_information = "";
                    eval("\$sl_information .= \"".$templates->get("noticeboard_sl_information")."\";");
                }
                else {
                    $sl_information = "";
                }
                
                eval("\$bit .= \"".$templates->get("noticeboard_quest")."\";");
            };

        }
        else {
                eval("\$bit = \"".$templates->get("noticeboard_no_permission")."\";");
        }

    eval("\$page = \"".$templates->get("noticeboard")."\";");
        output_page($page);
}

// Übersicht über vergeben Quests


    if($mybb->input['action'] == "taken") {

        add_breadcrumb("Vergebene Quests");

        if(is_member($mybb->settings['noticeboard_allow_groups_see'])) {

        eval("\$none = \"".$templates->get("noticeboard_quest_none")."\";");


            $sql = "SELECT * FROM ".TABLE_PREFIX."noticeboard WHERE visible = 1 AND status = '0' AND players != ''";
            $query = $db->query($sql);
            while($noticeboard = $db->fetch_array($query)) {

                $none = "";

                $keywords = '<div>'.str_replace(', ', '</div><div>', $noticeboard['keywords']).'</div>';

                $skill = '<div>'.str_replace(', ', '</div><div>', $noticeboard['skills']).'</div>';
                $skills = str_replace(
                    array("0", "1"),
                    array(
                    "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                    "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                    ),
                    $skill
                );

                if(is_member($mybb->settings['noticeboard_allow_groups_edit'])) {
                    $edit = "";
                    eval("\$edit .= \"".$templates->get("noticeboard_edit_button")."\";");
                }
                else {
                    $edit = "";
                }

                if(is_member($mybb->settings['noticeboard_allow_groups_lead'])) {
                    $sl_information = "";
                    eval("\$sl_information .= \"".$templates->get("noticeboard_sl_information")."\";");
                }
                else {
                    $sl_information = "";
                }
                
                $take = "";
                $finished = "";
                eval("\$take = \"".$templates->get("noticeboard_quest_taken")."\";");
                eval("\$bit .= \"".$templates->get("noticeboard_quest")."\";");
            };

       }
        else {
                eval("\$bit = \"".$templates->get("noticeboard_no_permission")."\";");
        }
        
    eval("\$page = \"".$templates->get("noticeboard")."\";");
        output_page($page);
}

// Übersicht über erledigte Quests


    if($mybb->input['action'] == "finished") {

        if(is_member($mybb->settings['noticeboard_allow_groups_see'])) {

        add_breadcrumb("Erledigt Quests");

        eval("\$none = \"".$templates->get("noticeboard_quest_none")."\";");

            $sql = "SELECT * FROM ".TABLE_PREFIX."noticeboard WHERE visible = 1 AND status = 1";
            $query = $db->query($sql);
            while($noticeboard = $db->fetch_array($query)) {

                $none = "";

                $keywords = '<div>'.str_replace(', ', '</div><div>', $noticeboard['keywords']).'</div>';

                $skill = '<div>'.str_replace(', ', '</div><div>', $noticeboard['skills']).'</div>';
                $skills = str_replace(
                    array("0", "1"),
                    array(
                    "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                    "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                    ),
                    $skill
                );

                if(is_member($mybb->settings['noticeboard_allow_groups_edit'])) {
                    $edit = "";
                    eval("\$edit .= \"".$templates->get("noticeboard_edit_button")."\";");
                }
                else {
                    $edit = "";
                }

                if(is_member($mybb->settings['noticeboard_allow_groups_lead'])) {
                    $sl_information = "";
                    eval("\$sl_information .= \"".$templates->get("noticeboard_sl_information")."\";");
                }
                else {
                    $sl_information = "";
                }

                $take = "";
                $finished = "";
              
                eval("\$finished.= \"".$templates->get("noticeboard_quest_finished")."\";");
                eval("\$bit .= \"".$templates->get("noticeboard_quest")."\";");
            };

        }
        else {
                eval("\$bit = \"".$templates->get("noticeboard_no_permission")."\";");
        }
        
    eval("\$page = \"".$templates->get("noticeboard")."\";");
        output_page($page);
}

    // Übersicht über die unfertigen Quests


    if($mybb->input['action'] == "pending") {

        add_breadcrumb("Unveröffentlichte Quests");

        if(is_member($mybb->settings['noticeboard_allow_groups_see_all'])) {

        eval("\$none = \"".$templates->get("noticeboard_quest_none")."\";");

            $sql = "SELECT * FROM ".TABLE_PREFIX."noticeboard WHERE visible = 0";
            $query = $db->query($sql);
            while($noticeboard = $db->fetch_array($query)) {

                $none = "";

                $keywords = '<div>'.str_replace(', ', '</div><div>', $noticeboard['keywords']).'</div>';

                $skill = '<div>'.str_replace(', ', '</div><div>', $noticeboard['skills']).'</div>';
                $skills = str_replace(
                    array("0", "1"),
                    array(
                    "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                    "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                    ),
                    $skill
                );

                if(is_member($mybb->settings['noticeboard_allow_groups_edit'])) {
                    $edit = "";
                    eval("\$edit .= \"".$templates->get("noticeboard_edit_button")."\";");
                }
                else {
                    $edit = "";
                }

                if(is_member($mybb->settings['noticeboard_allow_groups_lead'])) {
                    $sl_information = "";
                    eval("\$sl_information .= \"".$templates->get("noticeboard_sl_information")."\";");
                }
                else {
                    $sl_information = "";
                }
                
                eval("\$bit .= \"".$templates->get("noticeboard_quest")."\";");
            };
        
        
        }
        else {
                eval("\$bit = \"".$templates->get("noticeboard_no_permission")."\";");
        }
        
    eval("\$page = \"".$templates->get("noticeboard")."\";");
        output_page($page);
}

// Übersicht über alle Quests


    if($mybb->input['action'] == "all") {

        if(is_member($mybb->settings['noticeboard_allow_groups_see_all'])) {

        add_breadcrumb("Alle Quests");

        eval("\$none = \"".$templates->get("noticeboard_quest_none")."\";");

            $sql = "SELECT * FROM ".TABLE_PREFIX."noticeboard";
            $query = $db->query($sql);
            while($noticeboard = $db->fetch_array($query)) {

                $none = "";

                $keywords = '<div>'.str_replace(', ', '</div><div>', $noticeboard['keywords']).'</div>';

                $skill = '<div>'.str_replace(', ', '</div><div>', $noticeboard['skills']).'</div>';
                $skills = str_replace(
                    array("0", "1"),
                    array(
                    "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                    "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                    ),
                    $skill
                );

                $status = "";
                $take = "";
                $finished = "";

                    if($noticeboard['status'] == "0" && $noticeboard['players'] == "") {
                        eval("\$status = \"".$templates->get("noticeboard_status_free")."\";");

                    }
                    elseif($noticeboard['status'] == "0" && $noticeboard['players'] != "") {
                        eval("\$status = \"".$templates->get("noticeboard_status_taken")."\";");
                        eval("\$take = \"".$templates->get("noticeboard_quest_taken")."\";");
                    }
                    elseif($noticeboard['status'] == "1") {
                        eval("\$status = \"".$templates->get("noticeboard_status_finished")."\";");
                        eval("\$finished = \"".$templates->get("noticeboard_quest_finished")."\";");
                    }

                    if(is_member($mybb->settings['noticeboard_allow_groups_edit'])) {
                        $edit = "";
                        eval("\$edit .= \"".$templates->get("noticeboard_edit_button")."\";");
                    }
                    else {
                        $edit = "";
                    }
    
                    if(is_member($mybb->settings['noticeboard_allow_groups_lead'])) {
                        $sl_information = "";
                        eval("\$sl_information .= \"".$templates->get("noticeboard_sl_information")."\";");
                    }
                    else {
                        $sl_information = "";
                    }
                    $edit = ""; 
                eval("\$edit .= \"".$templates->get("noticeboard_edit_button")."\";");
                eval("\$bit .= \"".$templates->get("noticeboard_quest")."\";");
            };

        
        }
        else {
                eval("\$bit = \"".$templates->get("noticeboard_no_permission")."\";");
        }
        
    eval("\$page = \"".$templates->get("noticeboard")."\";");
        output_page($page);
}



// Quests hinzufügen


    if($mybb->input['action'] == "add") {

        add_breadcrumb ($lang->noticeboard, "noticeboard.php"); 
        add_breadcrumb($lang->noticeboard_add, "noticeboard.php?action=add");

        if(is_member($mybb->settings['noticeboard_allow_groups_add'])) {

            
            if ($mybb->input['submit']) {

                $new_noticeboard = array(
                    "type" => $db->escape_string($mybb->get_input('type')),
                    "title" => $db->escape_string($mybb->get_input('title')),
                    "shortdescription" => $db->escape_string($mybb->get_input('shortdescription')),
                    "quest" => $db->escape_string($mybb->get_input('quest')),
                    "client" => $db->escape_string($mybb->get_input('client')),
                    "keywords" => $db->escape_string($mybb->get_input('keywords')),
                    "skills" => $db->escape_string($mybb->get_input('skills')),
                    "location" => $db->escape_string($mybb->get_input('location')),
                    "lead" => $db->escape_string($mybb->get_input('lead')),
                    "monster" => $db->escape_string($mybb->get_input('monster')),
                    "reward" => $db->escape_string($mybb->get_input('reward')),
                    "level" => $db->escape_string($mybb->get_input('level')),
                    "background" => $db->escape_string($mybb->get_input('background')),
                    "material" => $db->escape_string($mybb->get_input('material')),
                    "maps" => $db->escape_string($mybb->get_input('maps')),
                    "treassure" => $db->escape_string($mybb->get_input('treassure')),
                    "boss" => $db->escape_string($mybb->get_input('boss')),
                    "solution" => $db->escape_string($mybb->get_input('solution')),
                    "visible" => $db->escape_string($mybb->get_input('visible')),
                    "status" => "0",
                );

                if($noticeboard['visible'] == "0") {
                    $checked_visible_0 = "checked";
                }
                elseif($noticeboard['visible'] == "1") {
                    $checked_visible_1 = "checked";
                }

                if($noticeboard['status'] == "0") {
                    $checked_status_0 = "checked";
                }
                elseif($noticeboard['status'] == "1") {
                    $checked_status_1 = "checked";
                }
    
                $db->insert_query("noticeboard", $new_noticeboard);
                $db->query("UPDATE ".TABLE_PREFIX."users SET noticeboard_new ='0'");
                redirect("noticeboard.php?action=overview");
            }
            
        eval("\$page = \"".$templates->get("noticeboard_add")."\";");
        output_page($page);
            die();
    }
    else {
        eval("\$bit = \"".$templates->get("noticeboard_no_permission")."\";");
    }
}


 // Quests bearbeiten


    if($mybb->input['action'] == "edit") {

        add_breadcrumb ($lang->noticeboard, "noticeboard.php"); 
        add_breadcrumb($lang->noticeboard_edit, "noticeboard.php?action=edit");

        if(is_member($mybb->settings['noticeboard_allow_groups_edit'])) {


        $nid =  $mybb->input['nid'];

        $sql = "SELECT * FROM ".TABLE_PREFIX."noticeboard WHERE nid = '".$nid."'";
        $query = $db->query($sql);
        $noticeboard = $db->fetch_array($query);

            $nid = $mybb->input['nid'];
            $title    = $mybb->get_input('title');
            $type     = $mybb->get_input('type');
            $shortdescription = $mybb->get_input('shortdescription');
            $quest    = $mybb->get_input('quest');
            $client   = $mybb->get_input('client');
            $keywords = $mybb->get_input('keywords');
            $skills   = $mybb->get_input('skills');
            $location = $mybb->get_input('location');
            $lead     = $mybb->get_input('lead');
            $leadby   = $mybb->get_input('leadby');
            $reward   = $mybb->get_input('reward');
            $monster   = $mybb->get_input('monster');
            $level    = $mybb->get_input('level');
            $status   = $mybb->get_input('status');
            $background = $mybb->get_input('background');
            $material = $mybb->get_input('material');
            $maps     = $mybb->get_input('maps');
            $treassure = $mybb->get_input('treassure');
            $boss     = $mybb->get_input('boss');
            $solution = $mybb->get_input('solution');
            $players  = $mybb->get_input('players');
            $scene    = $mybb->get_input('scene');
            $visible  = $mybb->get_input('visible');
        
            if ($mybb->input['submit']) {

                $edit_noticeboard = array(
                    "type" => $db->escape_string($mybb->get_input('type')),
                    "title" => $db->escape_string($mybb->get_input('title')),
                    "shortdescription" => $db->escape_string($mybb->get_input('shortdescription')),
                    "quest" => $db->escape_string($mybb->get_input('quest')),
                    "client" => $db->escape_string($mybb->get_input('client')),
                    "keywords" => $db->escape_string($mybb->get_input('keywords')),
                    "skills" => $db->escape_string($mybb->get_input('skills')),
                    "location" => $db->escape_string($mybb->get_input('location')),
                    "lead" => $db->escape_string($mybb->get_input('lead')),
                    "leadby" => $db->escape_string($mybb->get_input('leadby')),
                    "reward" => $db->escape_string($mybb->get_input('reward')),
                    "level" => $db->escape_string($mybb->get_input('level')),
                    "background" => $db->escape_string($mybb->get_input('background')),
                    "material" => $db->escape_string($mybb->get_input('material')),
                    "maps" => $db->escape_string($mybb->get_input('maps')),
                    "treassure" => $db->escape_string($mybb->get_input('treassure')),
                    "boss" => $db->escape_string($mybb->get_input('boss')),
                    "solution" => $db->escape_string($mybb->get_input('solution')),
                    "visible" => $db->escape_string($mybb->get_input('visible')),
                    "players" => $db->escape_string($mybb->get_input('players')),
                    "scene" => $db->escape_string($mybb->get_input('scene')),
                    "status" => $db->escape_string($mybb->get_input('status')),
                );

            $db->update_query("noticeboard", $edit_noticeboard, "nid = '".$nid."'");
            redirect("noticeboard.php?action=overview"); 
        } 

        if($noticeboard['visible'] == "0") {
        $checked_visible_0 = "checked";
        }
        elseif($noticeboard['visible'] == "1") {
            $checked_visible_1 = "checked";
        }

        if($noticeboard['status'] == "0") {
            $checked_status_0 = "checked";
        }
        elseif($noticeboard['status'] == "1") {
            $checked_status_1 = "checked";
        }

        if($mybb->usergroup['cancp'] == 1) {
            eval("\$edit_players = \"".$templates->get("noticeboard_edit_players")."\";");
        }


        eval("\$page = \"".$templates->get("noticeboard_edit")."\";");
        output_page($page);
        die();
    }
    else {
        eval("\$bit = \"".$templates->get("noticeboard_no_permission")."\";");
    }
}

// Quests reservieren
if(is_member($mybb->settings['noticeboard_allow_groups_take'])) {
    if($mybb->input['action'] == "take") {

        $nid =  $mybb->input['nid'];

        $take_noticeboard = array(
                        "players" => $db->escape_string($mybb->get_input('players')),
                        "scene" => $db->escape_string($mybb->get_input('scene')),
            );

            $db->update_query("noticeboard", $take_noticeboard, "nid = '$nid'"); 

        redirect("noticeboard.php?action=taken"); 

    } 
}

// Quests löschen

if(is_member($mybb->settings['noticeboard_allow_groups_edit'])) {
    if($mybb->input['action'] == "delete") {
        $nid = $mybb->input['nid'];

        $db->delete_query("noticeboard", "nid = '$nid'");

        redirect("noticeboard.php?action=all");
    }
}

// Quests als erledigt markieren

$taken = $mybb->input['finished'];
    if($taken){
        $take = array(
			"status" => "1",
        );

        $db->update_query("noticeboard", $take, "nid = '".$taken."'");
        redirect("noticeboard.php?action=quests");
    }

}

// Index Alert bei neuen Quests in der inc/plugins/noticeboard.php

// Wer ist online in der inc/plugins/noticeboard.php
