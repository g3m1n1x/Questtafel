<?php

define('IN_MYBB', 1);
require_once './global.php';

global $db, $cache, $mybb, $lang, $templates, $theme, $header, $headerinclude, $footer;

$lang->load('questboard');

// ########### Seiten aufbauen ####################

// Allgemeine Seite

add_breadcrumb("Questtafel", "questboard.php");

// ### NAVIGATION

// CP nur für Gruppen mit Rechten sichtbar
if(is_member($mybb->settings['questboard_allow_groups_see_all'])) {
    eval("\$questboard_cp = \"". $templates->get("questboard_navigation_cp")."\";");
}
else {
    $questboard_cp = "";
}

eval("\$navigation = \"".$templates->get("questboard_navigation")."\";");


// Usernamen für Questersteller aufbauen

$uid = $mybb->user['uid'];

$username = format_name($user['username'], $user['usergroup'], $user['displaygroup']);

// ### FUNKTIONEN UND CO. ###
 
// Standardseite mit Erklärung

if(is_member($mybb->settings['questboard_allow_groups_access'])) {

if(!$mybb->input['action']) {

    add_breadcrumb("Erklärung");

    eval("\$description = \"".$templates->get("questboard_description")."\";");
    eval("\$page = \"".$templates->get("questboard")."\";");
    output_page($page);
}

// Übersicht über freie Quests

    if($mybb->input['action'] == "free") {

        if(is_member($mybb->settings['questboard_allow_groups_see'])) {
            
        add_breadcrumb("Freie Quests");
        
        eval("\$none = \"".$templates->get("questboard_quest_none")."\";");

            $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE visible = 1 AND status = 'free' AND (players IS NULL OR players = '') ORDER BY nid DESC";
            $query = $db->query($sql);
            eval("\$questtype = \"<h1>Freie Quests</h1>\";");
            while($questboard = $db->fetch_array($query)) {
                $questboard['reusable_text'] = $questboard['reusable'] ? 'Mehrmals bespielbar' : 'Nur einmal bespielbar';
                $none = "";

                $keywords = '<div>'.str_replace(', ', '</div><div>', $questboard['keywords']).'</div>';

                $skill = '<div>'.str_replace(', ', '</div><div>', $questboard['skills']).'</div>';
                $skills = str_replace(
                    array("1", "0"),
                    array(
                    "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                    "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                    ),
                    $skill
                );
               
                $take = "";
                $finished = "";

                if($questboard['players'] == "") {
                    if(is_member($mybb->settings['questboard_allow_groups_take'])) {
                        $take = "";
                        eval("\$take = \"".$templates->get("questboard_quest_take")."\";");
                    }
                    else {
                        $take = "";
                    }
                }
                else {
                    eval("\$take = \"".$templates->get("questboard_quest_taken")."\";");
                }

                if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
                    $edit = "";
                    eval("\$edit .= \"".$templates->get("questboard_edit_button")."\";");
                }
                else {
                    $edit = "";
                }

                if(is_member($mybb->settings['questboard_allow_groups_lead'])) {
                    $sl_information = "";
                    eval("\$sl_information .= \"".$templates->get("questboard_sl_information")."\";");
                }
                else {
                    $sl_information = "";
                }
                
                eval("\$bit .= \"".$templates->get("questboard_quest")."\";");
            };

        }
        else {
                eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
        }

    eval("\$page = \"".$templates->get("questboard")."\";");
        output_page($page);
}

//TODO: Code optimieren und kürzen! Im besten Fall, falls es geht, mit einer For-Schleife für alle Questarten

// Übersicht über Allgemeine Quests
if($mybb->input['action'] == "allgemein") {
    if(is_member($mybb->settings['questboard_allow_groups_see'])) {
    add_breadcrumb("Allgemeine Quests");
    eval("\$none = \"".$templates->get("questboard_quest_none")."\";");
        $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE visible = 1 && (players IS NULL OR players = '') && type = 'Allgemeine Quest' ORDER BY nid DESC";
        $query = $db->query($sql);
        eval("\$questtype = \"<h1>Allgemeine Quests</h1>\";");
        while($questboard = $db->fetch_array($query)) {
            $none = "";
    
            $keywords = '<div>'.str_replace(', ', '</div><div>', $questboard['keywords']).'</div>';
    
            $skill = '<div>'.str_replace(', ', '</div><div>', $questboard['skills']).'</div>';
            $skills = str_replace(
                array("1", "0"),
                array(
                "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                ),
                $skill
            );
           
    
            $take = "";
            $finished = "";
    
            if($questboard['players'] == "") {
                if(is_member($mybb->settings['questboard_allow_groups_take'])) {
                    $take = "";
                    eval("\$take = \"".$templates->get("questboard_quest_take")."\";");
                }
                else {
                    $take = "";
                }
            }
            else {
                eval("\$take = \"".$templates->get("questboard_quest_taken")."\";");
            }
    
            if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
                $edit = "";
                eval("\$edit .= \"".$templates->get("questboard_edit_button")."\";");
            }
            else {
                $edit = "";
            }
    
            if(is_member($mybb->settings['questboard_allow_groups_lead'])) {
                $sl_information = "";
                eval("\$sl_information .= \"".$templates->get("questboard_sl_information")."\";");
            }
            else {
                $sl_information = "";
            }
            
            eval("\$bit .= \"".$templates->get("questboard_quest")."\";");
        };
    }
    else {
            eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
    }
eval("\$page = \"".$templates->get("questboard")."\";");
    output_page($page);
}
// Übersicht über Specialquests
if($mybb->input['action'] == "special") {
    if(is_member($mybb->settings['questboard_allow_groups_see'])) {
    add_breadcrumb("Specialquests");
    eval("\$none = \"".$templates->get("questboard_quest_none")."\";");
        $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE visible = 1 && (players IS NULL OR players = '') && type = 'Specialquest' ORDER BY nid DESC";
        $query = $db->query($sql);
        eval("\$questtype = \"<h1>Specialquests</h1>\";");
        while($questboard = $db->fetch_array($query)) {
            $none = "";
    
            $keywords = '<div>'.str_replace(', ', '</div><div>', $questboard['keywords']).'</div>';
    
            $skill = '<div>'.str_replace(', ', '</div><div>', $questboard['skills']).'</div>';
            $skills = str_replace(
                array("1", "0"),
                array(
                "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                ),
                $skill
            );
           
    
            $take = "";
            $finished = "";
    
            if($questboard['players'] == "") {
                if(is_member($mybb->settings['questboard_allow_groups_take'])) {
                    $take = "";
                    eval("\$take = \"".$templates->get("questboard_quest_take")."\";");
                }
                else {
                    $take = "";
                }
            }
            else {
                eval("\$take = \"".$templates->get("questboard_quest_taken")."\";");
            }
    
            if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
                $edit = "";
                eval("\$edit .= \"".$templates->get("questboard_edit_button")."\";");
            }
            else {
                $edit = "";
            }
    
            if(is_member($mybb->settings['questboard_allow_groups_lead'])) {
                $sl_information = "";
                eval("\$sl_information .= \"".$templates->get("questboard_sl_information")."\";");
            }
            else {
                $sl_information = "";
            }
            
            eval("\$bit .= \"".$templates->get("questboard_quest")."\";");
        };
    }
    else {
            eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
    }
eval("\$page = \"".$templates->get("questboard")."\";");
    output_page($page);
}
// Übersicht über Singlequests
if($mybb->input['action'] == "single") {
    if(is_member($mybb->settings['questboard_allow_groups_see'])) {
    add_breadcrumb("Singlequests");
    eval("\$none = \"".$templates->get("questboard_quest_none")."\";");
        $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE visible = 1 && (players IS NULL OR players = '') && type = 'Singlequest' ORDER BY nid DESC";
        $query = $db->query($sql);
        eval("\$questtype = \"<h1>Singlequests</h1>\";");
        while($questboard = $db->fetch_array($query)) {
            $none = "";
    
            $keywords = '<div>'.str_replace(', ', '</div><div>', $questboard['keywords']).'</div>';
    
            $skill = '<div>'.str_replace(', ', '</div><div>', $questboard['skills']).'</div>';
            $skills = str_replace(
                array("1", "0"),
                array(
                "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                ),
                $skill
            );
           
    
            $take = "";
            $finished = "";
    
            if($questboard['players'] == "") {
                if(is_member($mybb->settings['questboard_allow_groups_take'])) {
                    $take = "";
                    eval("\$take = \"".$templates->get("questboard_quest_take")."\";");
                }
                else {
                    $take = "";
                }
            }
            else {
                eval("\$take = \"".$templates->get("questboard_quest_taken")."\";");
            }
    
            if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
                $edit = "";
                eval("\$edit .= \"".$templates->get("questboard_edit_button")."\";");
            }
            else {
                $edit = "";
            }
    
            if(is_member($mybb->settings['questboard_allow_groups_lead'])) {
                $sl_information = "";
                eval("\$sl_information .= \"".$templates->get("questboard_sl_information")."\";");
            }
            else {
                $sl_information = "";
            }
            
            eval("\$bit .= \"".$templates->get("questboard_quest")."\";");
        };
    }
    else {
            eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
    }
eval("\$page = \"".$templates->get("questboard")."\";");
    output_page($page);
}

// Übersicht über Berufsbezogene Quests
if($mybb->input['action'] == "berufsbezogen") {
    if(is_member($mybb->settings['questboard_allow_groups_see'])) {
    add_breadcrumb("Berufsbezogene Quests");
    eval("\$none = \"".$templates->get("questboard_quest_none")."\";");
        $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE visible = 1 && (players IS NULL OR players = '') && type = 'Berufsbezogene Quest' ORDER BY nid DESC";
        $query = $db->query($sql);
        eval("\$questtype = \"<h1>Berufsbezogene Quests</h1>\";");
        while($questboard = $db->fetch_array($query)) {
            $none = "";
    
            $keywords = '<div>'.str_replace(', ', '</div><div>', $questboard['keywords']).'</div>';
    
            $skill = '<div>'.str_replace(', ', '</div><div>', $questboard['skills']).'</div>';
            $skills = str_replace(
                array("1", "0"),
                array(
                "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                ),
                $skill
            );
           
    
            $take = "";
            $finished = "";
    
            if($questboard['players'] == "") {
                if(is_member($mybb->settings['questboard_allow_groups_take'])) {
                    $take = "";
                    eval("\$take = \"".$templates->get("questboard_quest_take")."\";");
                }
                else {
                    $take = "";
                }
            }
            else {
                eval("\$take = \"".$templates->get("questboard_quest_taken")."\";");
            }
    
            if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
                $edit = "";
                eval("\$edit .= \"".$templates->get("questboard_edit_button")."\";");
            }
            else {
                $edit = "";
            }
    
            if(is_member($mybb->settings['questboard_allow_groups_lead'])) {
                $sl_information = "";
                eval("\$sl_information .= \"".$templates->get("questboard_sl_information")."\";");
            }
            else {
                $sl_information = "";
            }
            
            eval("\$bit .= \"".$templates->get("questboard_quest")."\";");
        };
    }
    else {
            eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
    }
eval("\$page = \"".$templates->get("questboard")."\";");
    output_page($page);
}

// Übersicht über Bespielte Quests

    if($mybb->input['action'] == "taken") {

        add_breadcrumb("Bespielte Quests");

        if(is_member($mybb->settings['questboard_allow_groups_see'])) {

        eval("\$none = \"".$templates->get("questboard_quest_none")."\";");


            $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE visible = 1 AND status = 'free' AND players IS NOT NULL";
            $query = $db->query($sql);
            eval("\$questtype = \"<h1>Bespielte Quests</h1>\";");
            while($questboard = $db->fetch_array($query)) {

                $none = "";

                $keywords = '<div>'.str_replace(', ', '</div><div>', $questboard['keywords']).'</div>';

                $skill = '<div>'.str_replace(', ', '</div><div>', $questboard['skills']).'</div>';
                $skills = str_replace(
                    array("1", "0"),
                    array(
                    "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                    "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                    ),
                    $skill
                );

                if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
                    $edit = "";
                    eval("\$edit .= \"".$templates->get("questboard_edit_button")."\";");
                }
                else {
                    $edit = "";
                }

                if(is_member($mybb->settings['questboard_allow_groups_lead'])) {
                    $sl_information = "";
                    eval("\$sl_information .= \"".$templates->get("questboard_sl_information")."\";");
                }
                else {
                    $sl_information = "";
                }
                
                $take = "";
                $finished = "";
                eval("\$take = \"".$templates->get("questboard_quest_taken")."\";");
                eval("\$bit .= \"".$templates->get("questboard_quest")."\";");
            };

       }
        else {
                eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
        }
        
    eval("\$page = \"".$templates->get("questboard")."\";");
        output_page($page);
}

// Übersicht über Auszuwertende Quests

if($mybb->input['action'] == "inEvaluation") {

    if(is_member($mybb->settings['questboard_allow_groups_see'])) {

    add_breadcrumb("Auszuwertende Quests");

    eval("\$none = \"".$templates->get("questboard_quest_none")."\";");

        $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE visible = 1 AND status = 'inEvaluation'";
        $query = $db->query($sql);
        eval("\$questtype = \"<h1>Auszuwertende Quests</h1>\";");
        while($questboard = $db->fetch_array($query)) {

            $none = "";

            $keywords = '<div>'.str_replace(', ', '</div><div>', $questboard['keywords']).'</div>';

            $skill = '<div>'.str_replace(', ', '</div><div>', $questboard['skills']).'</div>';
            $skills = str_replace(
                array("1", "0"),
                array(
                "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                ),
                $skill
            );

            if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
                $edit = "";
                eval("\$edit .= \"".$templates->get("questboard_edit_button")."\";");
            }
            else {
                $edit = "";
            }

            if(is_member($mybb->settings['questboard_allow_groups_lead'])) {
                $sl_information = "";
                eval("\$sl_information .= \"".$templates->get("questboard_sl_information")."\";");
            }
            else {
                $sl_information = "";
            }

            $take = "";
            $finished = "";
          
            eval("\$finished.= \"".$templates->get("questboard_quest_in_evaluation")."\";");
            eval("\$bit .= \"".$templates->get("questboard_quest")."\";");
        };

    }
    else {
            eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
    }
    
eval("\$page = \"".$templates->get("questboard")."\";");
    output_page($page);
}

// Übersicht über ausgewertete Quests

    if($mybb->input['action'] == "finished") {

        if(is_member($mybb->settings['questboard_allow_groups_see'])) {

        add_breadcrumb("Ausgewertete Quests");

        eval("\$none = \"".$templates->get("questboard_quest_none")."\";");

            $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE visible = 1 AND status = 'finished'";
            $query = $db->query($sql);
            eval("\$questtype = \"<h1>Ausgewertete Quests</h1>\";");
            while($questboard = $db->fetch_array($query)) {

                $none = "";

                $keywords = '<div>'.str_replace(', ', '</div><div>', $questboard['keywords']).'</div>';

                $skill = '<div>'.str_replace(', ', '</div><div>', $questboard['skills']).'</div>';
                $skills = str_replace(
                    array("1", "0"),
                    array(
                    "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                    "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                    ),
                    $skill
                );

                if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
                    $edit = "";
                    eval("\$edit .= \"".$templates->get("questboard_edit_button")."\";");
                }
                else {
                    $edit = "";
                }

                if(is_member($mybb->settings['questboard_allow_groups_lead'])) {
                    $sl_information = "";
                    eval("\$sl_information .= \"".$templates->get("questboard_sl_information")."\";");
                }
                else {
                    $sl_information = "";
                }

                $take = "";
                $finished = "";
              
                eval("\$finished.= \"".$templates->get("questboard_quest_finished")."\";");
                eval("\$bit .= \"".$templates->get("questboard_quest")."\";");
            };

        }
        else {
                eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
        }
        
    eval("\$page = \"".$templates->get("questboard")."\";");
        output_page($page);
}

    // Übersicht über die unfertigen Quests

    if($mybb->input['action'] == "pending") {

        add_breadcrumb("Unveröffentlichte Quests");

        if(is_member($mybb->settings['questboard_allow_groups_see_all'])) {

        eval("\$none = \"".$templates->get("questboard_quest_none")."\";");

            $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE visible = 0";
            $query = $db->query($sql);
            eval("\$questtype = \"<h1>Unveröffentlichte Quests</h1>\";");
            while($questboard = $db->fetch_array($query)) {

                $none = "";

                $keywords = '<div>'.str_replace(', ', '</div><div>', $questboard['keywords']).'</div>';

                $skill = '<div>'.str_replace(', ', '</div><div>', $questboard['skills']).'</div>';
                $skills = str_replace(
                    array("1", "0"),
                    array(
                    "<i class=\"fa-solid fa-hand-point-up\" title=\"von Nachteil\" style=\"color: var(--text);\"></i>", 
                    "<i class=\"fa-solid fa-ban\" title=\"verboten\" style=\"color: var(--text);\"></i>"
                    ),
                    $skill
                );

                if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
                    $edit = "";
                    eval("\$edit .= \"".$templates->get("questboard_edit_button")."\";");
                }
                else {
                    $edit = "";
                }

                if(is_member($mybb->settings['questboard_allow_groups_lead'])) {
                    $sl_information = "";
                    eval("\$sl_information .= \"".$templates->get("questboard_sl_information")."\";");
                }
                else {
                    $sl_information = "";
                }
                
                eval("\$bit .= \"".$templates->get("questboard_quest")."\";");
            };
        
        
        }
        else {
                eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
        }
        
    eval("\$page = \"".$templates->get("questboard")."\";");
        output_page($page);
}

// Quests hinzufügen

    if($mybb->input['action'] == "add") {

        add_breadcrumb ($lang->questboard, "questboard.php"); 
        add_breadcrumb($lang->questboard_add, "questboard.php?action=add");

        if(is_member($mybb->settings['questboard_allow_groups_add'])) {

            
            if ($mybb->input['submit']) {

                $new_questboard = array(
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
                    "treasure" => $db->escape_string($mybb->get_input('treasure')),
                    "boss" => $db->escape_string($mybb->get_input('boss')),
                    "solution" => $db->escape_string($mybb->get_input('solution')),
                    "visible" => $db->escape_string($mybb->get_input('visible')),
                    "reusable" => $db->escape_string($mybb->get_input('reusable')),
                    "status" => "free",
                );

                if($questboard['visible'] == "0") {
                    $checked_visible_0 = "checked";
                }
                elseif($questboard['visible'] == "1") {
                    $checked_visible_1 = "checked";
                }

                if($questboard['reusable'] == "0") {
                    $checked_reusable_0 = "checked";
                }
                elseif($questboard['reusable'] == "1") {
                    $checked_reusable_1 = "checked";
                }

                if($questboard['status'] == "free") {
                    $checked_status_0 = "checked";
                }
                elseif($questboard['status'] == "finished") {
                    $checked_status_1 = "checked";
                }
    
                $db->insert_query("questboard", $new_questboard);
                $db->query("UPDATE ".TABLE_PREFIX."users SET questboard_new ='0'");
                redirect("questboard.php?action=free", "Die Quest wurde erfolgreich erstellt.");
            }
            
        eval("\$page = \"".$templates->get("questboard_add")."\";");
        output_page($page);
            die();
    }
    else {
        eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
    }
}


 // Quests bearbeiten


    if($mybb->input['action'] == "edit") {

        add_breadcrumb ($lang->questboard, "questboard.php"); 
        add_breadcrumb($lang->questboard_edit, "questboard.php?action=edit");

        if(is_member($mybb->settings['questboard_allow_groups_edit'])) {


        $nid =  $mybb->input['nid'];

        $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE nid = '".$nid."'";
        $query = $db->query($sql);
        $questboard = $db->fetch_array($query);

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
            $treasure = $mybb->get_input('treasure');
            $boss     = $mybb->get_input('boss');
            $solution = $mybb->get_input('solution');
            $players  = $mybb->get_input('players');
            $scene    = $mybb->get_input('scene');
            $visible  = $mybb->get_input('visible');
            $reusable  = $mybb->get_input('reusable');
        
            if ($mybb->input['submit']) {

                $edit_questboard = array(
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
                    "treasure" => $db->escape_string($mybb->get_input('treasure')),
                    "boss" => $db->escape_string($mybb->get_input('boss')),
                    "solution" => $db->escape_string($mybb->get_input('solution')),
                    "visible" => $db->escape_string($mybb->get_input('visible')),
                    "reusable" => $db->escape_string($mybb->get_input('reusable')),
                    "scene" => $db->escape_string($mybb->get_input('scene')),
                    "status" => $db->escape_string($mybb->get_input('status')),
                );

            $db->update_query("questboard", $edit_questboard, "nid = '".$nid."'");
            redirect("questboard.php", "Die Quest wurde erfolgreich bearbeitet."); 
        } 

        if($questboard['visible'] == "0") {
        $checked_visible_0 = "checked";
        }
        elseif($questboard['visible'] == "1") {
            $checked_visible_1 = "checked";
        }

        
        if($questboard['reusable'] == "0") {
            $checked_reusable_0 = "checked";
        }
        elseif($questboard['reusable'] == "1") {
                $checked_reusable_1 = "checked";
        }

        if($questboard['status'] == "free" || $questboard['status'] == "inEvaluation") {
            $checked_status_0 = "checked";
        }
        elseif($questboard['status'] == "finished") {
            $checked_status_1 = "checked";
        }

        if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
            eval("\$player_info = \"".$templates->get("questboard_player_info_field")."\";");
        }

        eval("\$page = \"".$templates->get("questboard_edit")."\";");
        output_page($page);
        die();
    }
    else {
        eval("\$bit = \"".$templates->get("questboard_no_permission")."\";");
    }
}

// Quest annehmen
if(is_member($mybb->settings['questboard_allow_groups_take'])) {
    if($mybb->input['action'] == "take") {

        $nid =  $mybb->input['nid'];
        $characters = $_POST['characters'] ?? [];

        if(!$questboard){
            $questboard = [];
        }

        if (!empty($characters)) {
            $processedCharacters = [];
            
            foreach ($characters as $character) {
                // Benutzername bereinigen
                $user = trim($character['user'] ?? '');
                
                // Überspringe Einträge ohne Benutzername
                if (empty($user)) {
                    continue;
                }
                
                // Checkbox-Werte prüfen und in Integer umwandeln
                $xp = !empty($character['xp']) ? 1 : 0;
                $hp = !empty($character['hp']) ? 1 : 0;
                $fifty = !empty($character['fifty']) ? 1 : 0;
        
                // Kombiniere die Daten zu einem Eintrag
                $processedCharacters[] = [
                    'user' => $user,
                    'xp' => $xp,
                    'hp' => $hp,
                    'fifty' => $fifty
                ];
            }
        
            // JSON-encode die bereinigten Daten
            $playersData = json_encode($processedCharacters, JSON_UNESCAPED_UNICODE);
        } else {
            $playersData = ''; // Leeres Feld, wenn keine Daten vorhanden sind
        }
        
        // Szene-Feld auslesen und bereinigen
        $scene = $mybb->get_input('scene');
        
        $sql = "SELECT * FROM ".TABLE_PREFIX."questboard WHERE nid = '{$nid}'";
        $query = $db->query($sql);
        $existingQuest = $db->fetch_array($query);

        $updated_data = [
            "players" => $db->escape_string($playersData),
            "scene"   => $db->escape_string($scene),
        ];

        $questdata = [
            "title" => $existingQuest['title'] ?? "n/a", 
            "visible" => $existingQuest['visible'] ?? 1,
            "reusable" => $existingQuest['reusable'] ?? 1,
            "status" => $existingQuest['status'] ?? "free",
            "scene" => $existingQuest['scene'] ?? "n/a",
            "quest" => $existingQuest['quest'] ?? "n/a",
            "shortdescription" => $existingQuest['shortdescription'] ?? "n/a",
            "type" => $existingQuest['type'] ?? "n/a",
            "client" => $existingQuest['client'] ?? "n/a",
            "skills" => $existingQuest['skills'] ?? "n/a",
            "monster" => $existingQuest['monster'] ?? "n/a",
            "location" => $existingQuest['location'] ?? "n/a",
            "level" => $existingQuest['level'] ?? "n/a",
            "lead" => $existingQuest['lead'] ?? "n/a",
            "leadby" => $existingQuest['leadby'] ?? "n/a",
            "reward" => $existingQuest['reward'] ?? "n/a",
            "keywords" => $existingQuest['keywords'] ?? "n/a",
            "background" => $existingQuest['background'] ?? "n/a",
            "material" => $existingQuest['material'] ?? "n/a",
            "boss" => $existingQuest['boss'] ?? "n/a",
            "maps" => $existingQuest['maps'] ?? "n/a",
            "treasure" => $existingQuest['tressure'] ?? "n/a",
            "solution" => $existingQuest['solution'] ?? "n/a",
        ];

        $filtered_data = array_merge($questdata, $updated_data);

        if ($existingQuest) {
            // Quest existiert bereits
            if ($existingQuest['reusable'] == "0") {
                $db->update_query("questboard", $updated_data, "nid = '{$nid}'");
            } elseif ($existingQuest['reusable'] == "1") {
                // Neue Eintragung für Mehrfach-Quests
                $db->insert_query("questboard", $filtered_data);
            }
        } else {
            // Fallback: Wenn die Quest nicht existiert
            $db->insert_query("questboard", array_merge($updated_data, ["nid" => $db->escape_string($nid)]));
        }
        if(is_member($mybb->settings['questboard_allow_groups_lead'])){
            $db->query("UPDATE ".TABLE_PREFIX."users SET questboard_new_registration ='0'");
        }
        redirect("questboard.php?action=taken", "Du hast dich erfolgreich zu dieser Quest angemeldet.");
    } 
}

// Quests löschen

if(is_member($mybb->settings['questboard_allow_groups_edit'])) {
    if($mybb->input['action'] == "delete") {
        $nid = $mybb->input['nid'];

        $db->delete_query("questboard", "nid = '$nid'");

        redirect("questboard.php", "Die Quest wurde erfolgreich gelöscht.");
    }
}

// Quests zur Auswertung freigeben
if ($mybb->input['action'] == "evaluate") {
    $nid = $mybb->input['nid']; // Quest-ID abrufen und sicherstellen, dass es eine Zahl ist

    if ($nid > 0) {
        $evaluate = array(
            "status" => "inEvaluation",
        );

        // Status in der Datenbank aktualisieren
        $db->update_query("questboard", $evaluate, "nid = '{$nid}'");

        //Trigger Alert für Spielleiter
        if(is_member($mybb->settings['questboard_allow_groups_lead'])){
            $db->query("UPDATE ".TABLE_PREFIX."users SET questboard_quest_evaluation ='0'");
        }

        // Umleitung mit Erfolgsnachricht
        redirect("questboard.php", "Die Quest wurde erfolgreich zur Auswertung freigegeben.");
    } else {
        // Fehler, wenn keine gültige Quest-ID übergeben wurde
        redirect("questboard.php", "Fehler: Ungültige Quest-ID.");
    }
}

//Alert für "neue Quest" wurde weggeklickt
if ($mybb->get_input('action') == 'questboard_read') {

    $this_user = intval($mybb->user['uid']);

    $as_uid = intval($mybb->user['as_uid']);
    $read = $mybb->input['read'];
    if ($read) {
        if($as_uid == 0){
            $db->query("UPDATE ".TABLE_PREFIX."users SET questboard_new = 1  WHERE (as_uid = $this_user) OR (uid = $this_user)");
        } elseif($as_uid != 0){
            $db->query("UPDATE ".TABLE_PREFIX."users SET questboard_new = 1  WHERE (as_uid = $as_uid) OR (uid = $this_user) OR (uid = $as_uid)");
        }
        redirect('index.php', "Als gelesen markiert");
    }
}

//Alert für "neue Questanmeldung" wurde weggeklickt
if ($mybb->get_input('action') == 'questboard_registration_read') {

    $this_user = intval($mybb->user['uid']);

    $as_uid = intval($mybb->user['as_uid']);
    $read = $mybb->input['read'];
    if ($read) {
        if($as_uid == 0){
            $db->query("UPDATE ".TABLE_PREFIX."users SET questboard_new_registration = 1  WHERE (as_uid = $this_user) OR (uid = $this_user)");
        } elseif($as_uid != 0){
            $db->query("UPDATE ".TABLE_PREFIX."users SET questboard_new_registration = 1  WHERE (as_uid = $as_uid) OR (uid = $this_user) OR (uid = $as_uid)");
        }
        redirect("index.php");
    }
}

//Alert für "neue Quest zur Auswertung freigegeben" wurde weggeklickt
if ($mybb->get_input('action') == 'questboard_evaluation_read') {

    $this_user = intval($mybb->user['uid']);

    $as_uid = intval($mybb->user['as_uid']);
    $read = $mybb->input['read'];
    if ($read) {
        if($as_uid == 0){
            $db->query("UPDATE ".TABLE_PREFIX."users SET questboard_quest_evaluation = 1  WHERE (as_uid = $this_user) OR (uid = $this_user)");
        } elseif($as_uid != 0){
            $db->query("UPDATE ".TABLE_PREFIX."users SET questboard_quest_evaluation = 1  WHERE (as_uid = $as_uid) OR (uid = $this_user) OR (uid = $as_uid)");
        }
        redirect("index.php");
    }
}

// Quests als erledigt markieren
$taken = $mybb->input['finished'];
    if($taken){
        $take = array(
			"status" => "finished",
        );

        $db->update_query("questboard", $take, "nid = '".$taken."'");
        redirect("questboard.php?action=finished", "Die Quest wurde erfolgreich als erledigt markiert. Du wirst nun zu den erledigten Quests weitergeleitet.");
    }

}

// Index Alert bei neuen Quests in der inc/plugins/questboard.php
// Wer ist online in der inc/plugins/questboard.php