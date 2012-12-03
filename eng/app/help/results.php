<?php

switch ($_REQUEST['lang']) {
	case 'de':
	case 'fr':
	case 'en':
	default:
		?>
			<h1>Suchresultate</h1>
			
			<p>Mit einem Klick auf die Lupe, kann jedes Suchresultat für weitere Begriffe gefiltert werden. Diese können einerseits zur <b>Suchverfeinerung</b>, andererseits  zur <b>weiteren Suche in ontologischen Facetten verwendet</b> werden.</p>

			<p>Bei einem Klick auf die Nummer des Resultates (links) wird ein neues Browser-Fenster mit dem Resultat geöffnet.</p>
		<?php
		break;
}

?>


