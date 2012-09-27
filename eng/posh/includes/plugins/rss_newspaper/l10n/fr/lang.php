<?php
$GLOBALS['lgMap']=array(
"day0"=>"Dimanche",
"day1"=>"Lundi",
"day2"=>"Mardi",
"day3"=>"Mercredi",
"day4"=>"Jeudi",
"day5"=>"Vendredi",
"day6"=>"Samedi",
"month01"=>"janvier",
"month02"=>"février",
"month03"=>"mars",
"month04"=>"avril",
"month05"=>"mai",
"month06"=>"juin",
"month07"=>"juillet",
"month08"=>"août",
"month09"=>"septembre",
"month10"=>"octobre",
"month11"=>"novembre",
"month12"=>"décembre",
"index"=>"Sommaire"
);

function lg(){
	$str=func_get_arg(0);
	$ret=(!empty($GLOBALS['lgMap'][$str]))?$GLOBALS['lgMap'][$str]:$str;
	for ($i=1;$i<func_num_args();$i++){$arg=func_get_arg($i);$ret=str_replace("#".$i,$arg,$ret);}
	return $ret;
}
?>
