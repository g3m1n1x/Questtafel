<h1>Questtafel</h1>

Dieses Plugin ist ein Quest-Plugin, das dem klassischen Auftragsprinzip aus Videospielen und Pen & Paper-RPGs nachempfunden ist. Das Team kann Quests erstellen, die verschiedene Informationsfelder haben, einige davon sind nur für das Team sichtbar. User können Teilnehmende und den Link zur Szene direkt auf der Übersichtsseite der Quests einfügen, um eine Quest anzunehmen. Dabei wird zwischen Quests, die mehrfach bespielbar sind und Quests, die nur ein einziges Mal bespielbar sind, unterschieden. 
Die Quests werden abhängig von ihrem Status in die Kategorien "Frei", "Bespielt", "Zur Auswertung freigegeben" und "Ausgewertet" unterteilt. Außerdem gibt es einen Bereich für die Spielleitung, in dem alle noch nicht freigeschalteten Quests zu finden sind. So können Quests vorbereitet und später für die User freigeben werden.

<h2>Voraussetzungen</h2>

Das Plugin setzt keine anderen Plugins oder Erweiterungen voraus. Es sind allerdings <b>FontAwesome Icons</b> eingebaut. Diese können über die Templates ganz einfach ausgetauscht werden.

<h2>Funktionen</h2>

Das Plugin erstellt eine extra Seite, die auf <b>/questboard.php</b> zu erreichen ist. Dort gibt es ein Menü, das freie, bespielte, auszuwertende und ausgewertete Quests anzeigt. Außerdem sehen Admins unveröffentlichte Quests und die Möglichkeit, Quests hinzuzufügen.

Die Quests kommen mit folgenden Feldern:

<ul>
<li>sichtbar/unsichtbar</li>
<li>einmalig bespielbar/mehrfach bespielbar</li>
<li>Typ (Select)</li>
<li>Auftragstitel</li>
<li>Kurzbeschreibung</li>
<li>Ausführliche Beschreibung (zu erreichen, wenn man auf "Mehr" klickt)</li>
<li>Auftraggeber*in</li>
<li>Keywords (Icons für "verboten" und "von Nachteil" erscheinen, wenn man den Keywords ein Schlüsselzeichen vorsetzt)</li>
<li>Spielort</li>
<li>geleitete Quest (Select)</li>
<li>Belohnung</li>
<li>Schwierigkeit (Select)</li>
<li>Monster</li>
<li>erledigt/nicht erledigt</li>

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
Wenn eine Quest von Spielenden angenommen wurde, erhält die Spielleitung auf dem Index einen <b>Alert</b>, der sich wegklicken lässt.
Wenn eine Quest von Spielenden zur Auswertung freigegeben wurde, erhält die Spielleitung auf dem Index einen <b>Alert</b>, der sich wegklicken lässt.

User, die Teil einer bereits angenommenen Quest sind, können die Quest über einen Link auf der Questübersicht zur Auswertung freigeben.

Admins können Quests <b>bearbeiten und löschen</b>. Entsprechende Optionen erscheinen auf den Quest-Karten. User können diese Optionen nicht sehen. Wenn eine Szene angenommen und anschließend zur Auswertung freigegeben wurde, können sie sie nach der Auswertung außerdem <b>als erledigt markieren</b>. Die Quests verschieben sich jeweils in die passende Kategorie. 

Auf der Startseite der Questtafel gibt es Platz für eine <b>Erklärung</b>, die über eine separate Template eingefügt werden kann.

<h2>Einstellungsmöglichkeiten</h2>

Im ACP kann eingestellt werden:

<ul><li>Welche Gruppen dürfen die Questtafel sehen?</li>
<li>Welche Gruppen dürfen Quests sehen?</li>
<li>Welche Gruppen dürfen nicht freigegebene Quests sehen?</li>
<li>Welche Gruppen dürfen Quests erstellen?</li>
<li>Welche Gruppen dürfen Quests annehmen?</li>
<li>Welche Gruppen dürfen Quests als erledigt markieren?</li></ul>

<h2>Variablen</h2>

Für den Alert auf dem Index werden die Variablen

[php]{$questboard_new}[/php]
[php]{$questboard_new_registration}[/php]
[php]{$questboard_quest_evaluation}[/php]

in die <b>header.tpl</b> eingefügt. Die Variablen können überall sonst auf dem Index eingefügt werden.

<h2>Templates</h2>

Folgende Templates werden bei der Installation erstellt und sind in der Templategruppe <b>Questboard</b> zu finden:

<ul><li>questboard</li>
<li>questboard_add</li>
<li>questboard_alert</li>
<li>questboard_description</li>
<li>questboard_edit</li>
<li>questboard_edit_button</li>
<li>questboard_edit_players</li>
<li>questboard_navigation</li>
<li>questboard_navigation_cp</li>
<li>questboard_no_permission</li>
<li>questboard_quest</li>
<li>questboard_quest_finished</li>
<li>questboard_quest_none</li>
<li>questboard_quest_sl</li>
<li>questboard_quest_sl_nope</li>
<li>questboard_quest_take</li>
<li>questboard_quest_taken</li>
<li>questboard_sl_information</li>
<li>questboard_status_finished</li>
<li>questboard_status_free</li>
<li>questboard_status_taken</li></ul>

<h2>CSS</h2>

Für das Plugin wird ein eigenes CSS-Sheet in jedem Design angelegt:

<ul><li>questboard.css</li></ul>

<h2>Datenbanktabellen</h2>

Für das Plugin wird folgende Tabelle angelegt:

<ul><li>questboard</li></ul>

Die Tabelle wird gelöscht, wenn das Plugin <b>deinstalliert</b> wird.


<h2>Nutzungsregeln & Support</h2>

Das Plugin darf freigenutzt und für eure eigenen Zwecke angepasst werden. Bitte entfernt nicht meinen Nick oder den Link zu meinem Profil und erwähnt in euren üblichen Credits, dass das Plugin von mir ist und woher ihr es habt, sodass es auch andere leicht finden können.
Bitte bietet das Plugin nicht irgendwo zum Download an ohne mein Wissen und gebt es nicht als euer eigenes aus. Wenn ihr es erweitern oder umschreiben und dann anderen anbieten wollt, setzt euch mit mir in Verbindung. Ich bin grundsätzlich für sowas zu haben, aber ich möchte, dass man mit mir redet und ich bin neugierig über eure Ideen.

Bei Fragen oder Problemen meldet euch bitte in dieser Topic, sodass andere die Lösungen ebenfalls sehen können. Ich werde das Plugin erweitern und bei Feldern auch updaten, ich habe schon einige Pläne. Vorschläge sind mir sehr willkommen, aber ich bitte um Geduld sowohl zu den Updates als auch beim Support :D
