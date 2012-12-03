<?php

switch ($_REQUEST['lang']) {
	case 'de':
	case 'fr':
	case 'en':
	default:
		?>
			<h1>Widgets hinzufügen</h1>

			<p>Alle Widgets können ausgewählt und anhand einer Ziehbewegung mit der Maus in das freie Feld von Rodin gezogen werden.</p>

			<p>Die Anordnung kann frei gewählt und immer wieder neu angepasst werden.</p>

			<p>Jedes Widget ermöglicht die Suche in einer spezifischen Informationsressource. Dabei kann jeweils auch nur lokal in einem einzelnen Widget gesucht werden.</p>

			<p>Das Widget kann minimiert, in einem eigenen Browser-Fenster geöffnet, aktualisiert oder gelöscht werden.</p>

			<p>Mit einem Klick auf das Zahnrad lassen sich die Sucheinstellungen des jeweiligen Widgets einstellen. Die Einstellungen unterscheiden sich von Widget zu Widget.</p>
		<?php
		break;
}

?>


