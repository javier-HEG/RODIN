<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of POSH (Portaneo Open Source Homepage) http://sourceforge.net/projects/posh/.

	POSH is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version

	POSH is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Posh.  If not, see <http://www.gnu.org/licenses/>.
*/
# ***************************************
# POSH Configuration - load sql file in database / generate lang files
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_config_langimport.php";
//includes
require_once("includes.php");
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');

//usage_label
define('LANG', 1<<0);
define('ADMIN', 1<<1);
define('TUTORIAL', 1<<2);
define('INSTALL', 1<<3);
define('PEP', 1<<4);
//typefile
define('JS', 1<<0);
define('HTML', 1<<1);
define('PHP', 1<<2);

/*
        getUsages : return the array of types and begining of the filename
*/
function getUsages()
{
    return Array(
                    LANG => 'lang.',
                    ADMIN => 'admin.lang.',
                    TUTORIAL => 'tutorial.lang.',
                    INSTALL => 'install.lang.',
                    PEP => 'enterprise.'
                );
}
/*
        getTypes : return the array of extensions
*/
function getTypes()
{
    return Array(
                    JS => 'js',
                    PHP => 'php',
                    HTML => 'html'  
                );
}

//Load the sql file and make the queries in the database
if (isset($_POST['submitFileImport']))
{
    $content_dir = 'temp/';
    $tmp_file = $_FILES['fichier']['tmp_name'];
    if (!file_exists($content_dir)) {
        mkdir($content_dir,0777);
    }
    //find the file
    if(!is_uploaded_file($tmp_file)){
        echo "File not found";
        exit();
    }
    $type_file = $_FILES['fichier']['type'];
    //file type
    $name_file = $_FILES['fichier']['name'];
    //file name control
    if( preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $name_file) ){
        echo "File name not valid";
        exit();
    }
    //file upload
    elseif( !move_uploaded_file($tmp_file, $content_dir . $name_file) ){
        echo "Impossible to copy the file in $content_dir";
        exit();
    }

   //put the file content in an array
   $lines = file($content_dir.$name_file); 
   if(!$lines)  {
      echo "Cannot open file $name_file"; 
      exit();
   } 
   // for element of the array, execute the query
   for ($i=0;$i<count($lines);$i++)
   {
      if(!$DB->execute($lines[$i])) 
      { 
         echo "Query ".$lines[$i]." failed"; 
         exit(); 
      } 
   }
   //delete the temp file
   unlink($content_dir.$name_file);
   echo "<br /><center><font color='green'>".lg("lblFileCreationSuccess")."</font><br />
         <a href='frm_config_langimport.php' >".lg("backPrevPage")."</a></center>";
}
//Generate the lang files from the database.
elseif (isset($_POST['submitGenerateFiles']))
{
    //check that there are some labels/messages in the table 'messages'
    $DB->getResults($getNbMessages);
    $nbLabels = $DB->nbResults();
    $DB->freeResults();
    if ($nbLabels==0) {
        echo "<br /><center><font color='red' >".lg("noLanguageEntry")."</font><br />
              <a href='frm_config_langimport.php' >".lg("backPrevPage")."</a></center>";
        exit();
    }
    else {         
    
        $DB->getResults($getAllLanguages);
        while ($row2 = $DB->fetch(0))
        {
            $lang = $row2['lang'];
            //step 1 build the initial hash
            $myh = buildHashbyLang($lang);
            
            //get all the messages in the database
            $DB2->getResults($getAllMessages,$DB2->quote($lang));
            while ($row = $DB2->fetch(0))
            {
                //step 2 update from the database
                $myh = updateHash($lang,$row,$myh);      
            }
            $DB2->freeResults();
            //step 3 generate the files
            generateFiles($myh,$lang);

        } //for each lang
        $DB->freeResults();
        
        echo "<br /><center><font color='green' >".lg("lblFileCreationSuccess")."</font><br />
              <a href='frm_config_langimport.php' >".lg("backPrevPage")."</a></center>";
    }
}
$DB2->close();

/*
        buildHashbyLang : build the hash by language
        return the hash
*/
function buildHashbyLang ($lang) {
    $myh = Array();
    $tabletype = getTypes(); //returns an array of all the different typefile
    $tableusage = getUsages(); //returns an array of all the different typefile
    
    foreach($tableusage as $usage => $filenameformat)
    {
        foreach($tabletype as $type => $ext)
        {
          $myh = buildHashFromFile($lang,$filenameformat,$usage,$type,$ext);        
        }
    }   
    return $myh;
}
/*
        buildHashFromFile : return a hash from a file ( labels => message)
*/
function buildHashFromFile ($lang,$filenameformat,$usage,$type,$ext) {
   
   $chaine=array ();
   $fileToSearch = '../../app/exposh/l10n/'.$lang.'/'.$filenameformat.$ext;

   //if the file exists
   if (file_exists($fileToSearch))
   {
       $lines = file($fileToSearch);    
       //if the file can be opened
       if ($lines)
       {  
           for ($i=0;$i<count($lines);$i++)
           {    
            //    if (ereg('"([^"]+)"=>"([^"]+)"', $lines[$i], $arr)) 
            //    {
            //        $chaine[$lang][$usage][$type][$arr[1]] = $arr[2];     
            //    }  
            //    else if (ereg('"([^"]+)":"([^"]+)"', $lines[$i], $arr))
            //    {
            //        $chaine[$lang][$usage][$type][$arr[1]] = $arr[2];  
            //    }
                          
                if (ereg('\["([^"]+)\"]="([^"]+)"', $lines[$i], $arr))
                {
                    $chaine[$lang][$usage][$type][$arr[1]] = $arr[2]; 
                }              
           }    
       }
   }
   return $chaine;
}
/*
        updateHash : update the hash with the database values
         return the hash updated 
*/
function updateHash ($lang,$row,$myh) {
    $tabletype = getTypes();
    $tableusage = getUsages();

    foreach($tableusage as $usage => $filenameformat)
    {
        foreach($tabletype as $type => $ext )
        {
            $usagedb = $row['usage_label'];
            $typefile = $row['typefile'];
            $label = $row['label'];
            $message = $row['message'];
            $message = str_replace("\n","\\n",$message);
            $message = str_replace("\r","\\n",$message);
            $message = str_replace("\r\n","\\n",$message);

            $myh[$lang][$usage & (int)$usagedb][$type & (int)$typefile][$label]=$message; 
        }
    }   
    return $myh;
}


function getUsagesJava()
{
    return Array(
                    LANG => 'portal.lang.',
                    ADMIN => 'admin.lang.',
                    TUTORIAL => 'tutorial.lang.',
                    INSTALL => 'install.lang.',
                    PEP => 'enterprise.lang.'
                );
}


/*
        Generate the languages files
*/
function generateFiles($myh,$lang)
{
    $tabletype = getTypes();
    $tableusage = getUsages();
    // JAVA FILES GENERATION
    $tableusageJava = getUsagesJava();
    foreach($tableusageJava as $usage => $filenameformat)
    {
        foreach($tabletype as $type => $ext )
        {
            $filename2 = '../../app/exposh/l10n/'.$lang.'/'.$filenameformat.'properties'; 
            $tmpFilename2 = '../../app/exposh/l10n/'.$lang.'/'.$filenameformat. 'tmp.properties';
            if (!empty($myh[$lang][$usage][$type]))  
            {
                $tmp2 = $myh[$lang][$usage][$type];                
                $inF2 = fopen($tmpFilename2,"w");   
                foreach($tmp2 as $label => $message )
                {
                    $context=substr($filenameformat, 0, strlen($filenameformat)-5);  
                    //only type=4 (php,java... labels)
                    if ($type==4){  
                        $message2 = preg_replace('/\"/','\"',$message);
                        fputs($inF2,'posh.'.$context.$label.'='.$message2."\n"); 
                    }
                }                  
                fclose($inF2);
                if (!copy($tmpFilename2, $filename2)) {    
                    echo "The copy of the file $tmpname failed...\n";   
                }
                else    {   unlink($tmpFilename2);  }
            }
        }
    }
    
    
 
    foreach($tableusage as $usage => $filenameformat)
    {
        foreach($tabletype as $type => $ext )
        {
            $filename = '../../app/exposh/l10n/'.$lang.'/'.$filenameformat.$ext;    
            $tmpFilename = '../../app/exposh/l10n/'.$lang.'/'.$filenameformat. 'tmp.' .$ext;
        
            if (!empty($myh[$lang][$usage][$type]))  
            {
                $tmp = $myh[$lang][$usage][$type];
                $headers = "/* File generated by script since posh 2.0.2 */";  
                //open the file (created if not existing)
                $inF = fopen($tmpFilename,"w");
                
                switch($type)
                {
                    case '1':
                    {
                        fputs($inF,$headers."\n");
                          if ($filenameformat!='enterprise.')
                          fputs($inF,'var lang=new Array;');
                        break;
                    }
                    case '4':
                    {
                        fputs($inF,"<?php \n");
                        fputs($inF,$headers);
                        break;
                    }
                }
                
                //file content
                fputs($inF,"\n");
                foreach($tmp as $label => $message )
                {
                    switch($type)
                    {
                        //javascript
                        case '1':{
                            $message2 = preg_replace('/\"/','\"',$message);
                            fputs($inF,'lang["'.$label.'"]="'.$message2.'";'."\n");
                            break;}
                        //php
                        case '4':{
                            $message2 = preg_replace('/\"/','\"',$message);
                            fputs($inF,'$GLOBALS[\'lgMap\']["'.$label.'"]="'.$message2.'";'."\n");
                            break;} 
                    }
                }  
                
                //file footer
                switch($type)
                {
                    case '1':
                    {
                        if ($filenameformat!='enterprise.') {
                            fputs($inF,"\n");
                            fputs($inF,"var __lang='".$lang."';");
                        }
                        break;
                    }
                    case '4':
                    {
                        if ($filenameformat=='enterprise.') {
                            fputs($inF,"\n");
                            fputs($inF,"?>");
                        }
                        break;
                    }
                } 
                 
                //copy the footer from the .txt files in /l10n/ for files which are not entreprise
                if ($filenameformat!='enterprise.')
                {
                    $lines = file('../l10n/lg'.$ext.'.txt'); 
                    if ($lines)
                    {
                        fputs($inF,"\n");
                        for ($i=0;$i<count($lines);$i++)
                        {
                            $lines[$i] = ereg_replace("(\r\n|\r|\n)","\n",$lines[$i]);
                            fputs($inF,$lines[$i]);
                        }
                    }
                }
                
                //close the file
                fclose($inF);
                if (!copy($tmpFilename, $filename)) {
                    echo "The copy of the file $tmpname failed...\n";
                }
                else{
                unlink($tmpFilename);
                }
            }             
        }
    }
}
?>