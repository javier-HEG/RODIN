<?php
$GLOBALS['lgMap']=array(
"day0"=>"Sunday",
"day1"=>"Monday",
"day2"=>"Tuesday",
"day3"=>"Wednesday",
"day4"=>"Thurday",
"day5"=>"Friday",
"day6"=>"Saturday",
"month01"=>"january",
"month02"=>"february",
"month03"=>"march",
"month04"=>"april",
"month05"=>"may",
"month06"=>"june",
"month07"=>"july",
"month08"=>"august",
"month09"=>"september",
"month10"=>"october",
"month11"=>"november",
"month12"=>"december",
"index"=>"Index"
);

function lg(){
	$str=func_get_arg(0);
	$ret=(!empty($GLOBALS['lgMap'][$str]))?$GLOBALS['lgMap'][$str]:$str;
	for ($i=1;$i<func_num_args();$i++){$arg=func_get_arg($i);$ret=str_replace("#".$i,$arg,$ret);}
	return $ret;
}
?>
