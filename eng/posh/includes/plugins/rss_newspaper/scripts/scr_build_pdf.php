<?php

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/scr_build_pdf.php";
//includes
require_once('includes.php');
require_once('../includes/fpdf/fpdf.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');

if ( !function_exists('htmlspecialchars_decode') )
{
	function htmlspecialchars_decode($text)
	{
		$text=strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
		return $text;
	}
}
function suppressSpecialChars($text)
{
	$text=htmlspecialchars_decode($text);
	$text=htmlspecialchars_decode($text);
	$text=str_replace("&#8217;","'",$text);
	$text=str_replace("&#8220;",'"',$text);
	$text=str_replace("&#8221;",'"',$text);
	$text=str_replace("&#8230;","...",$text);
	$text=str_replace("&#8211;","-",$text);
	$text=str_replace("&agrave;","à",$text);
	$text=str_replace("&eacute;","é",$text);
	$text=str_replace("&egrave;","è",$text);
	$text=str_replace("&ecirc;","ê",$text);
	$text=str_replace("&acirc;","â",$text);
	$text=str_replace("&ccedil;","ç",$text);
	$text=str_replace("&ocirc;","ô",$text);
	$text=str_replace("&ucirc;","û",$text);
	$text=str_replace("&ugrage;","ù",$text);
	$text=str_replace("&icirc;","î",$text);
	$text=str_replace("<br />","\r\n",$text);
	return $text;
}
// compute day date
function newspaperGetDate()
{
	$retDate=lg('day'.date("w")).' '.date("d").' '.lg('month'.date("m")).' '.date("Y");
	return $retDate;
}

$xmlfile=new xmlFile();

$xmlfile->header();

$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
$pkey="";
srand((double)microtime()*1000000);
for($i=0;$i<10;$i++) $pkey.= $str[rand()%62];
$file=$pkey.".pdf";

$pdfFile = "archive/".$file;

$DB->getResults("SELECT a.id,b.long_name,a.title,a.header_img FROM rssnewspaper AS a,users AS b,rssnewspaper_publication AS c WHERE a.author_id=b.id AND b.id=%u AND c.newspaper_id=a.id AND c.id=%u",$DB->escape($_SESSION['user_id']),$DB->escape($_POST["id"]));
if ($DB->nbResults()==0) exit("user is not the owner of the newspaper");//Security : check the user is the owner of the newspaper
$row=$DB->fetch(0);
$npId=$row["id"];
$npAuthor=utf8_decode(suppressSpecialChars($row["long_name"]));
$npTitle=utf8_decode(suppressSpecialChars($row["title"]));
$npHeader=$row["header_img"];
$DB->freeResults();

/*
$pdf=new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','',7);
//$pdf->SetX($_POST["x0"]);
//$pdf->SetY($_POST["y0"]);
//$pdf->MultiCell($_POST["w0"],$_POST["h0"],$_POST["b0"],1,0,"L");

$pdf->SetX(0);
$pdf->SetY(0);
$pdf->MultiCell(200,240,"toto\n\nCeci est un test de création d'article pour voir s'il est possible d'avoir l'article sur plusieurs lignes et voir ce que cela peut donner ! ",1,0,"L");
*/
class PDF extends FPDF
{
	function Header()
	{

	}
	function Footer()
	{
		Global $npAuthor,$npTitle;

		//Positionnement à 1,5 cm du bas
		$this->SetY(-15);
		//Arial italique 8
		$this->SetFont('Arial','I',8);
		//Couleur du texte en gris
		$this->SetTextColor(128);
		//Numéro de page
		$this->Cell(0,10,$npAuthor.' - '.$npTitle.' - '.date("d/m/Y").' - Page '.$this->PageNo(),0,0,'C');
	}
	function showArticle($position,$title,$pubdate,$author,$body,$link,$x,$y,$width,$img,$imgx,$imgy,$imgwidth,$imgheight)
	{
		$bigSize=($position==1?15:12);
		$middleSize=($position==1?12:10);
		$middleSizeDecal=($position==1?5:4.5);
		//Justified text
		$this->SetY($y);
		$this->SetFont('Times','B',$bigSize);
		$this->SetX($x);
		$this->MultiCell($width,5,$title);
		// Author
		$this->ln();
		$this->SetFont('Times','',$middleSize);
		//$this->SetTextColor(128);
		$this->SetX($x);
		$this->MultiCell($width,$middleSizeDecal,$author." | ".$pubdate);
		//image
		$this->ln();
		//$this->ln();
		if ($img!=""){
			$this->Image($img,$imgx,$imgy+7,$imgwidth,$imgheight);
		}
		// body
		$this->SetFont('Times','',$middleSize);
		$this->SetTextColor(0);
		if ($img!="")
		{
			$this->SetY($imgy+$imgheight+7);
		}
		$this->SetX($x);
		$this->MultiCell($width,$middleSizeDecal,$body);
		//link
		$bottomY=$this->GetY();
		// separation line
		$linePositionY=$bottomY;
		$this->Line($x,$linePositionY+2,$x+$width,$linePositionY+2);

		$this->link($x,$y,$width,($bottomY-$y),$link);
	}
	function AcceptPageBreak()
	{
		return false;
	}
}

$pdf=new PDF();
$pdf->SetMargins(5,5,5);
$pdf->SetTitle("RSS newspaper");
$pdf->SetAuthor('Portaneo');

$pdf->AddPage();
$page=1;
$lineDrawn=false;
//header
if ($npHeader=="")
{
	$pdf->SetY(5);
	$pdf->SetFont('Times','',35);
	$pdf->SetX(10);
	$pdf->Cell(0,30,$npTitle,0,0,'C');
}
else
{
	$pdf->Image("../upload/".$npHeader,5,5);
}

//build index
$top=0;
$index="\r\n";
$DB->getResults("SELECT a.title,a.page_nb,a.y FROM rssnewspaper_publication_article AS a, rssnewspaper AS b, rssnewspaper_publication AS c WHERE c.id=%u AND a.publication_id=c.id AND c.newspaper_id=b.id AND b.author_id=%u ORDER BY a.page_nb ASC,a.y ASC,a.x ASC",$DB->escape($_POST["id"]),$DB->escape($_SESSION['user_id']));
if ($DB->nbResults()>0)
{
	$top=0;
	$pageNb=0;
	$lineNb=0;
	while ($row=$DB->fetch(0))
	{
		if ($lineNb<35)
		{
			if ($top==0) $top=$row["y"];
			if ($row["page_nb"]>$pageNb)
			{
				$index.="\r\nPage ".$row["page_nb"]."\r\n";
				$pageNb=$row["page_nb"];
			}
			$index.=substr(utf8_decode(suppressSpecialChars($row["title"])),0,37)."\r\n";
			$lineNb++;
		}
	}
} else {$top=30;}
$DB->freeResults();

//subheader
$pdf->Line(5,$top-12,200,$top-12);
$pdf->SetY($top-10);
$pdf->SetFont('Times','B',13);
$pdf->SetX(0);
$pdf->MultiCell(140,3,$npTitle,0,'C');
//$pdf->Cell(0,30,$npTitle,0,0,'C');
$pdf->SetY($top-10);
$pdf->SetFont('Times','B',10);
$pdf->SetX(140);
$pdf->MultiCell(50,4,utf8_decode(suppressSpecialChars(newspaperGetDate())),0,'R');
//$pdf->Cell(0,30,utf8_decode(suppressSpecialChars(newspaperGetDate())),0,0,'C');

//display index
$pdf->setY($top);
$pdf->SetX(6);
$pdf->SetFont('Times','B',10);
$pdf->MultiCell(63,4,lg("index"));
//$pdf->Cell(0,30,lg("index"),0,0,'C');
$pdf->SetX(6);
$pdf->SetFont('Times','',9);
$pdf->MultiCell(63,4,$index);

//articles
$pos=1;	
$DB->getResults("SELECT a.title,UNIX_TIMESTAMP(a.pubdate) AS pdate,a.feed,a.body,a.link,a.page_nb,a.x,a.y,a.width,a.img,a.imgx,a.imgy,a.imgwidth,a.imgheight FROM rssnewspaper_publication_article AS a, rssnewspaper AS b, rssnewspaper_publication AS c WHERE c.id=%u AND a.publication_id=c.id AND c.newspaper_id=b.id AND b.author_id=%u ORDER BY a.page_nb ASC,a.y ASC,a.x ASC",$DB->escape($_POST["id"]),$DB->escape($_SESSION['user_id']));
if ($DB->nbResults()>0)
{
	while ($row=$DB->fetch(0))
	{
		if (!$lineDrawn)
		{
			//draw separation line
			$pdf->Line(5,$row["y"]-5,200,$row["y"]-5);
			$lineDrawn=true;
		}
		if ($row["page_nb"]>$page)
		{
			$pdf->AddPage();
			$page=$row["page_nb"];
		}
		
		$pdf->showArticle($pos,utf8_decode(suppressSpecialChars($row["title"])),date("d/m/Y",$row["pdate"]),utf8_decode($row["feed"]),utf8_decode(suppressSpecialChars($row["body"])),$row["link"],$row["x"],$row["y"],$row["width"],$row["img"],$row["imgx"],$row["imgy"],$row["imgwidth"],$row["imgheight"]);

		$pos++;
	}
}
$DB->freeResults();

$chk=$pdf->Output("../".$pdfFile,"F");

//set the articles as published
$rows=$DB->select(FETCH_ARRAY,"SELECT a.feed_id, MAX(b.id) AS lastid FROM rssnewspaper_feeds AS a,feed_articles AS b WHERE a.newspaper_id=%u AND a.feed_id=b.feed_id GROUP BY a.feed_id ",$npId);
foreach ($rows as $row)
{
	$DB->execute("UPDATE rssnewspaper_feeds SET latest_read_id=%u WHERE feed_id=%u AND newspaper_id=%u",$row["lastid"],$row["feed_id"],$npId);
}
//add the new filename
$DB->execute("UPDATE rssnewspaper_publication SET filename='%s' WHERE id=%u",$file,$DB->escape($_POST["id"]));

$xmlfile->status($chk);
$xmlfile->returnData($pdfFile);

$xmlfile->footer();

$DB->close();
?>