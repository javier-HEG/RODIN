<?php

switch ($_REQUEST['lang']) {
	case 'de':
	case 'fr':
	case 'en':
	default:
		?>
			<h1>Ontologische Fassetten</h1>

			<p>Rodin verfügt zurzeit über zwei Ontologien, nämlich <b>DBPedia</b> und den <b>Standardthesaurus Wirtschaft (STW)</b>.</p>

			<p>Jeder Suchbegriff von Rodin wird automatisch auch in den integrierten Ontologien gesucht. Dabei werden die übergeordneten, untergeordneten und verwandten Begriffe des gesuchten Wortes, sofern vorhanden, aufgelistet.</p>

			<p>Indem man auf <b>einen Begriff mit der rechten Maustaste klickt</b>, kann jeder Begriff entweder</p>

			<ol>
				<li>1. selbst in den <b>Ontologien</b> gesucht oder</li>
				<li>für eine <b>Suchverfeinerung</b> weiter verwendet werden.</li>
			</ol>
		<?php
		break;
}

?>


