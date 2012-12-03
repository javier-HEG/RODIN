<?php

switch ($_REQUEST['lang']) {
	case 'de':
	case 'fr':
	case 'en':
	default:
		?>
			<h1>Visualisierung</h1>

			<p>Jeder Begriff der Ontologien kann anhand des Rechts vorhandenen Logos visualisiert werden, wobei ein Netzwerk des Begriffes und dessen Relationen angezeigt werden.</p>

			<p><b>Doppelklick auf einen Knoten:</b> die Visualisierung wird neugestartet und das Netzwerk sowie die Relationen des ausgewählten Begriffs angezeigt.</p>

			<p><b>Rechtsklick auf einen Begriff:</b>  entweder als Suchverfeinerung zu den Breadcrumbs hinzufügen oder zur Suche in den ontologischen Fassetten weiterverwenden.</p>
		<?php
		break;
}

?>


