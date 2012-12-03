<?php

switch ($_REQUEST['lang']) {
	case 'de':
	case 'fr':
	case 'en':
	default:
		?>
			<h1>Tag-Cloud</h1>

			<p>Jeder verwendete Suchbegriff wird automatisch der Tag-Cloud hinzugefügt. Dabei können die Tags in zwei verschiedenen Arten angezeigt werden.</p>

			<ol>
				<li>Generelle Tag-Cloud, die Begriffe sind alphabetisch geordnet. Je grösser der Begriff dabei ist, desto öfter wurde nach ihm gesucht.</li>
				<li>Chronik, welche die Begriffe in zeitlicher Suchabfolge aufzeigt.</li>
			</ol>
		<?
		break;
}

?>


