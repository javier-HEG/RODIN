<?php
/**
 * To use add :: require_once("tests/TomaNota.php"); :: somewhere in root.php
 * @author Javier
 */
class TomaNota {
	public static function vaciar() {
		$filename = $_SERVER['DOCUMENT_ROOT'] . "/rodin/debugging.txt";
		$h=fopen($filename,"w");
		fclose($h);
	}
	
	public static function deEsto($source, $message) {
    	$filename = $_SERVER['DOCUMENT_ROOT'] . "/rodin/debugging.txt";
    	
        $h=fopen($filename,"a");
		fwrite($h,'[' . basename($source) . '] ' . $message . "\n");
		fclose($h);
    }
}

?>