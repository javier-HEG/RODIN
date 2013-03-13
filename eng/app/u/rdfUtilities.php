<?php

/**
 * RDF utilities.php
 * @author Fabio Ricci - WebDMS GmbH
 * for HEG
 * fabio.ricci@ggaweb.ch
 */

$filename = "app/root.php";
for ($x = 1, $updir = ''; $x <= 10; $x++, $updir .= "../") {
	if (file_exists("$updir$filename")) {
    $sroot_loaded=true;
		include_once("$updir$filename");
		break;
	}
}



?>