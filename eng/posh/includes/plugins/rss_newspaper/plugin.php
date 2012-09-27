<?php
/*
 * name: RSS Newspaper
 * description: generate newspapers based on selected feeds. FPDF library needs to be installed on your server. Imaginated and sponsored by Groupe La Poste.
 * dependencies: no
 * author: Portaneo sponsored by La Poste
 * url: http://www.laposte.com
 */
 
register_hook('userinterface_header','loadRssNewspaperJavascript',5,0);
register_hook('install_rss_newspaper/plugin','rssNewspaper_install');
register_hook('uninstall_rss_newspaper/plugin','rssNewspaper_uninstall');
 
function loadRssNewspaperJavascript(){
	echo "<script type='text/javascript' src='../includes/plugins/rss_newspaper/includes/javascript.js?v=1.0.0' ></script>";
	echo "<script type='text/javascript' src='../includes/plugins/rss_newspaper/l10n/".__LANG."/lang.js?v=1.0.0' ></script>";
}
function rssNewspaper_install(){
	$sql = "SHOW TABLES LIKE 'rssnewspaper_%'";
	global $DB;
	$tables = $DB->select(FETCH_OBJECT,$sql);
	
	if (empty($tables)){
		$sql = "CREATE TABLE `rssnewspaper` (`id` mediumint(8) unsigned NOT NULL auto_increment,`title` varchar(60) collate utf8_unicode_ci NOT NULL default '',`description` text collate utf8_unicode_ci NOT NULL,`author_id` mediumint(8) unsigned NOT NULL default '0',`header_img` varchar(150) collate utf8_unicode_ci NOT NULL default '',`status` char(1) collate utf8_unicode_ci NOT NULL default '',PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$DB->execute($sql);
		$sql = "CREATE TABLE `rssnewspaper_feeds` (`newspaper_id` mediumint(8) unsigned NOT NULL default '0',`feed_id` mediumint(8) unsigned NOT NULL default '0',`latest_read_id` int(10) unsigned NOT NULL default '0',PRIMARY KEY  (`newspaper_id`,`feed_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$DB->execute($sql);
		$sql = "CREATE TABLE `rssnewspaper_keywords` (`newspaper_id` mediumint(8) unsigned NOT NULL default '0',`kw_id` mediumint(8) unsigned NOT NULL default '0') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$DB->execute($sql);
		$sql = "CREATE TABLE `rssnewspaper_publication` (`id` mediumint(8) unsigned NOT NULL auto_increment,`newspaper_id` mediumint(8) unsigned NOT NULL default '0',`pubdate` date NOT NULL default '0000-00-00',`filename` varchar(100) collate utf8_unicode_ci NOT NULL default '',`access` tinyint(3) unsigned NOT NULL default '0',`status` char(1) collate utf8_unicode_ci NOT NULL default '',PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
		$DB->execute($sql);
		$sql = "CREATE TABLE `rssnewspaper_publication_article` (`publication_id` mediumint(8) unsigned NOT NULL default '0',`title` varchar(100) collate utf8_unicode_ci NOT NULL default '',`pubdate` date NOT NULL default '0000-00-00',`feed` varchar(100) collate utf8_unicode_ci NOT NULL default '',`body` text collate utf8_unicode_ci NOT NULL,`link` varchar(100) collate utf8_unicode_ci NOT NULL default '',`page_nb` tinyint(3) unsigned NOT NULL default '0',`x` smallint(5) unsigned NOT NULL default '0',`y` smallint(5) unsigned NOT NULL default '0',`width` smallint(5) unsigned NOT NULL default '0',`img` varchar(100) collate utf8_unicode_ci NOT NULL default '',`imgx` smallint(5) unsigned NOT NULL default '0',`imgy` smallint(5) unsigned NOT NULL default '0',`imgwidth` smallint(5) unsigned NOT NULL default '0',`imgheight` smallint(5) unsigned NOT NULL default '0') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$DB->execute($sql);
		$sql = "CREATE TABLE `rssnewspaper_publication_page` (`publication_id` mediumint(8) unsigned NOT NULL default '0',`page_nb` tinyint(3) unsigned NOT NULL default '0',`layout` tinyint(3) unsigned NOT NULL default '0',PRIMARY KEY  (`publication_id`,`page_nb`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$DB->execute($sql);
		$sql = "INSERT INTO applications (title,description,creation_date,icon,action) VALUES ('Journal RSS','Transformez vos portails en journal RSS papier',CURRENT_DATE,'../includes/plugins/rss_newspaper/images/menu_icon.gif','rssNewspaper.open()')";
		$DB->execute($sql);
	}
	// $sql = "SELECT id FROM adm_headlinks WHERE label='rssNewspaper'";
	// $links = $DB->select(FETCH_OBJECT,$sql);
	// if (empty($links)){
		// $sql = "SELECT id FROM adm_headlinks WHERE label='myportaneo'";
		// $DB->getResults($sql);
		// if ($DB->nbResults()==0){
			// $DB->freeResults();
			// $sql = "SELECT MAX(id) AS maxid FROM adm_headlinks ";
			// $DB->getResults($sql);
			// $row=$DB->fetch(0);
			// $linkId=$row["maxid"]+1;
			// $DB->freeResults();
			// $sublinkId=0;
		// } else {
			// $row=$DB->fetch(0);
			// $linkId=$row["id"];
			// $DB->freeResults();
			// $sql = "SELECT MAX(seq) AS maxseq FROM adm_headlinks WHERE id=".$linkId." ";
			// $DB->getResults($sql);
			// $row=$DB->fetch(0);
			// $sublinkId=$row["maxseq"]+1;
			// $DB->freeResults();
		// }
		// $sql = "INSERT INTO adm_headlinks (id,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES(".$linkId.",'rssNewspaper','','','../includes/plugins/rss_newspaper/images/menu_icon.gif','rssNewspaper.open()','O',".$sublinkId.",1,1,0) ";
	$sql = "SELECT id FROM applications WHERE title='rssNewspaper'";
	$links = $DB->select(FETCH_OBJECT,$sql);
	if (empty($links)){
		$sql = "INSERT INTO applications (title,description,creation_date,icon,action) VALUES ('rssNewspaper','display and print your page as real newpaper',CURRENT_DATE,'../includes/plugins/rss_newspaper/images/menu_icon.gif','rssNewspaper.open()')";
		$DB->execute($sql);
	}
}
function rssNewspaper_uninstall(){
	// do not suppress table, they can contain important information !!
	global $DB;
	//$sql = "DELETE FROM adm_headlinks WHERE label='rssNewspaper' ";
	$sql = "DELETE FROM applications WHERE title='rssNewspaper' ";
	$DB->execute($sql);
}
?>