<?php

switch ($_REQUEST['lang']) {
	case 'de':
	case 'fr':
	case 'en':
	default:
		?>
			<h1>FAQ</h1>

			<ol>
				<li>
					<b>Wie kann ich Widgets hinzufügen?</b><br />
					Das Widget kann innerhalb der Widget-Box links mit dem Mauspfeil ausgewählt und in die freie Fläche rechts gezogen werden.
				</li>
				<li>
					<b>Wie kann ich die Widgets-Einstellungen verändern?</b><br />
					Durck Klicken auf das Zahnrad neben dem Suchfeld können die gewünschten Veränderungen eingegeben und dann gespeichert werden.
				</li>
				<li>
					<b>Wie kann ich suchen?</b><br />
					Sobald Widgets auf die Fläche hinzugefügt worden sind, kann entweder mit Hilfe des Hauptsuchfeldes in allen Widgets gleichzeitig, oder anhand des internen Suchfeldes eines Widgets lokal eine Suche durchgeführt werden.
				</li>
				<li>
					<b>Wie kann ich ein einzelnes Resultat öffnen?</b><br />
			  		Links neben jedem Resultat befindet sich eine Nummer. Klickt man auf die Nummer, so öffnet sich das jeweilige Resultat direkt in einem neuen Browserfenster.
			  	</li>
			  	<li>
			  		<b>Wie kann ich die Resultate nach Begriffen filtern?</b><br />
					Die Lupe links neben dem Resultat erlaubt das Resultat nach Begriffen zu filtern, welche dann weiterverwendet werden können.
				</li>
			  	<li>
					<b>Wie kann ich die Suche verfeinern?</b><br />
					Jeder Begriff kann anhand eines Rechtsklicks « zu den Breadcrumbs » (unter das Hauptsuchfeld) hinzugefügt werden und erlaubt es so, die Suche zu verfeinern.
				</li>
				<li>
					<b>Was kann ich einen Begriff zu den Ontologien hinzufügen?</b><br />
					Jeder Begriff im Hauptsuchfeld wird direkt auch in den Ontologien gebrowst. Man kann jedoch auch entscheiden, einen Suchbegriff direkt im Suchfeld der Ontologien einzugeben. Weiter können alle Begriffe der Resultate mit einem Rechtsklick und der Aktion « Begriff in den ontologischen Fassetten suchen » in den Ontologien gesucht werden.
				</li>
				<li>
					<b>Wie kann ich einen Begriff visualisieren?</b><br />
					Neben jedem Begriff innerhalb der Ontologien gibt es ein kleines Logo, das die Visualisierung des jeweiligen Begriffes erlaubt.
				</li>
				<li>
					<b>Kann ich die Visualisierung anpassen?</b><br />
					Jeder Knoten in der Visualisierung kann anhand eines Doppelklicks als Hauptknoten weiterverwendet werden.
				</li>
				<li>
					<b>Wie kann ich auf meine vorhergehenden Suchen zurückgreifen?</b><br />
					Dafür steht die Tag-Cloud unten links zur Verfügung. Es gibt zwei Anzeigemöglichkeiten. Gewöhnliche Tag-Cloud , wobei die Begriffe alphabetisch angezeigt werden. Je grösser das Wort ist, desto öfter wird nach ihm gesucht. Chronik, welche die Begriffe in der Reihenfolge ihrer Suche anzeigt.
				</li>
			</ol>
		<?
		break;
}

?>
