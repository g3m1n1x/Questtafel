<h1>Anschlagstafel</h1>

Dieses Plugin ist ein Quest-Plugin, das dem klassischen Auftragsprinzip aus Videospielen und Pen & Paper-RPGs nachempfunden ist. Das Team kann Aufgaben erstellen, die verschiedene Informationsfelder haben, einige davon sind nur für das Team sichtbar. User können sich und die Szene, in der sie den Auftrag spielen wollen, eintragen. Die Aufträge werden in einer Übersicht ausgegeben als frei, angenommen und fertig. Außerdem gibt es einen Bereich für Admins, in dem alle noch nicht freigeschalteten Aufträge sind. So könnt ihr Aufträge vorbereiten und später für die User freigeben.

<h2>Voraussetzungen</h2>

Das Plugin setzt keine anderen Plugins oder Erweiterungen voraus. Es sind allerdings <b>FontAwesome Icons</b> eingebaut. Solltet ihr solche nicht nutzen oder eine Version nutzen, die nicht mit der übereinstimmt, die verbaut ist, könnt ihr die Icons einfach in den Templates austauschen.

<h2>Funktionen</h2>

Das Plugin erstellt eine extra Seite, die auf <b>/noticeboard.php</b> zu erreichen ist. Dort gibt es ein Menü, das freie, vergebene und erledigte Quests anzeigt. Außerdem sehen Admins nicht freigegebene Quests, alle Quests und die Möglichkeit, Quests hinzuzufügen.

Die Quests kommen mit folgenden Feldern:

<ul><li>Typ (Select)</li>
<li>Auftragstitel</li>
<li>Kurzbeschreibung</li>
<li>Ausführliche Beschreibung (zu erreichen, wenn man auf "Mehr" klickt)</li>
<li>Auftraggeber*in</li>
<li>Keywords (Icons für "verboten" und "von Nachteil" erscheinen, wenn man den Keywords ein Schlüsselzeichen vorsetzt)</li>
<li>Fähigkeiten</li>
<li>Spielort</li>
<li>geleitete Quest (Select)</li>
<li>Belohnung</li>
<li>Schwierigkeit (Select)</li>
<li>Monster</li>
<li>sichtbar/unsichtbar</li>

Nur für die Spielleitung sichtbar:

<li>Hintergrund</li>
<li>Material</li>
<li>Karten</li>
<li>Schatz</li>
<li>Endgegner</li>
<li>Rätsel</li></ul>

Die Select-Felder haben bereits vorgefertigte Antwortmöglichkeiten. Wenn ihr andere oder mehr wollt, könnt ihr in den Templates die entsprechenden Optionen einfügen. Ihr könnt in den Templates auch alle Felder umbenennen und für andere Zwecke einsetzen.

<img src="https://imgur.com/NdKwbDy.png">

Wenn eine neue Quest freigegeben wurde, erhalten alle User auf dem Index einen <b>Alert</b>, der sich wegklicken lässt.

Admins können Quests <b>bearbeiten und löschen</b>. Entsprechende Optionen erscheinen auf den Quest-Karten. User können diese Optionen nicht sehen. 
Admins können außerdem <b>bearbeiten, wer die Quests angenommen hat und welche Szene dazugehört</b>, wenn sie auf "editieren" klicken. Wenn eine Szene angenommen wurde, können sie sie außerdem <b>als erledigt markieren</b>. Die Szenen verschieben sich jeweils in die passende Kategorie. 

Auf der Startseite der Auftragstafel gibt es Platz für eine <b>Erklärung</b>, die über eine separate Template eingefügt werden kann.

<h2>Einstellungsmöglichkeiten</h2>

Im ACP kann eingestellt werden:

<ul><li>Welche Gruppen dürfen die Anschlagstafel sehen?</li>
<li>Welche Gruppen dürfen Aufträge sehen?</li>
<li>Welche Gruppen dürfen nicht freigegebene Aufträge sehen?</li>
<li>Welche Gruppen dürfen Aufträge erstellen?</li>
<li>Welche Gruppen dürfen Aufträge annehmen?</li>
<li>Welche Gruppen dürfen Aufträge als erledigt markieren?</li></ul>

<h2>Variablen</h2>

Für den Alert auf dem Index wird die Variable

[php]{$noticeboard_new}[/php]

in die <b>header.tpl</b> eingefügt. Die Variable kann überall sonst auf dem Index eingefügt werden.

<h2>Templates</h2>

Folgende Templates werden bei der Installation erstellt und sind in der Templategruppe <b>Auftragstafel</b> zu finden:

<ul><li>noticeboard</li>
<li>noticeboard_add</li>
<li>noticeboard_alert</li>
<li>noticeboard_description</li>
<li>noticeboard_edit</li>
<li>noticeboard_edit_button</li>
<li>noticeboard_edit_players</li>
<li>noticeboard_navigation</li>
<li>noticeboard_navigation_cp</li>
<li>noticeboard_no_permission</li>
<li>noticeboard_quest</li>
<li>noticeboard_quest_finished</li>
<li>noticeboard_quest_none</li>
<li>noticeboard_quest_sl</li>
<li>noticeboard_quest_sl_nope</li>
<li>noticeboard_quest_take</li>
<li>noticeboard_quest_taken</li>
<li>noticeboard_sl_information</li>
<li>noticeboard_status_finished</li>
<li>noticeboard_status_free</li>
<li>noticeboard_status_taken</li></ul>

<h2>CSS</h2>

Für das Plugin wird ein eigenes CSS-Sheet in jedem Design angelegt:

<ul><li>noticeboard.css</li></ul>

<h2>Datenbanktabellen</h2>

Für das Plugin wird folgende Tabelle angelegt:

<ul><li>noticeboard</li></ul>

Die Tabelle wird gelöscht, wenn das Plugin <b>deinstalliert</b> wird.


<h2>Nutzungsregeln & Support</h2>

Das Plugin darf freigenutzt und für eure eigenen Zwecke angepasst werden. Bitte entfernt nicht meinen Nick oder den Link zu meinem Profil und erwähnt in euren üblichen Credits, dass das Plugin von mir ist und woher ihr es habt, sodass es auch andere leicht finden können.
Bitte bietet das Plugin nicht irgendwo zum download an ohne mein Wissen und gebt es nicht als euer eigenes aus. Wenn ihr es erweitern oder umschreiben und dann anderen anbieten wollt, setzt euch mit mir in Verbindung. Ich bin grundsätzlich für sowas zu haben, aber ich möchte, dass man mit mir redet und ich bin neugierig über eure Ideen.

Bei Fragen oder Problemen meldet euch bitte in dieser Topic, sodass andere die Lösungen ebenfalls sehen können. Ich werde das Plugin erweitern und bei Feldern auch updaten, ich habe schon einige Pläne. Vorschläge sind mir sehr willkommen, aber ich bitte um Geduld sowohl zu den Updates als auch beim Support :D
