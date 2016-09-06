<?php
header('Content-Type: text/html; charset=utf-8');
echo "<form id='data' method='post'>";
echo "<button form='data' type='submit' name='button' value='1'>Сортировать фото</button>";
echo "</form>";
set_error_handler("warning_handler", E_WARNING);





function warning_handler($errno, $errstr) { 
 echo $errstr."<br/>";
}

function GetListFiles($folder,&$all_files){
    $fp=opendir($folder);
    while($cv_file=readdir($fp)) {
        if(is_file($folder."/".$cv_file)) {
            $all_files[]=$folder."/".$cv_file;
        }elseif($cv_file!="." && $cv_file!=".." && is_dir($folder."/".$cv_file)){
            GetListFiles($folder."/".$cv_file,$all_files);
        }
    }
    closedir($fp);
}


$Month_r = array( 
1 => "январь", 
2 => "февраль", 
3 => "март", 
4 => "апрель", 
5 => "май", 
6 => "июнь", 
7 => "июль", 
8 => "август", 
9 => "сентябрь", 
10 => "октябрь", 
11 => "ноябрь", 
12 => "декабрь"); 

if (isset($_POST['button'])) {

    $all_files=array();
    GetListFiles("/var/www/html/sort_photo/unsorted",$all_files);
    foreach ($all_files as $file){
        try{
        $exif = exif_read_data ($file,'IFD0');
        $size = filesize($file);
        $fname = basename($file);
        $filename = explode('.',$fname)[0];
        $ext = explode('.',$fname)[count(explode('.',$fname))-1];
        if ((strtolower($ext)=="jpg")||(strtolower($ext)=="mpg")||(strtolower($ext)=="mov")||(strtolower($ext)=="wmv")||(strtolower($ext)=="avi")||(strtolower($ext)=="gif")||(strtolower($ext)=="tif")||(strtolower($ext)=="png")){
            
        } else {
            continue;
        }
        } catch (Exception $e) {
             echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
        }
        if ($exif==false){
            $datetime = date("d_m_Y___H_i_s", filectime($file));
        } else {
            if (isset($exif["DateTime"])) {
                $arr = explode(' ',$exif["DateTime"]);
                $arr[0] = str_replace(":","-",$arr[0]);
                $arr[0] = str_replace(".","-",$arr[0]);
                $time = strtotime($arr[0]." ".$arr[1]);
                $datetime = date("d_m_Y___H_i_s",  $time);
                $year = (int)date("Y",$time);
                $month = $Month_r[(int)date("m",$time)];
            }
        }
        if ($year<2002) {
            $imagename = $filename."_".$size.".".$ext;
            if (!file_exists(getcwd()."/sorted/without_date/".$imagename)) {
                if (!copy($file, getcwd()."/sorted/without_date/".$imagename)) {
                    echo "Не удалось скопировать $file в ".getcwd()."/sorted/without_date/".$imagename."\n";
                }
            }
        } else {
            $imagename = $datetime."_".$size.".".$ext;
            if (!file_exists(getcwd()."/sorted/".(string)$year."/".(string)$month."/".$imagename)){
                if (!file_exists(getcwd()."/sorted/".(string)$year)){
                    mkdir(getcwd()."/sorted/".(string)$year);
                }
                if (!file_exists(getcwd()."/sorted/".(string)$year."/".(string)$month)){
                    mkdir(getcwd()."/sorted/".(string)$year."/".(string)$month);
                }
                if (!copy($file, getcwd()."/sorted/".(string)$year."/".(string)$month."/".$imagename)) {
                    echo "Не удалось скопировать $file в ".getcwd()."/sorted/".(string)$year."/".(string)$month."/".$imagename."\n";
                }              
            }
        }
        
        
        
    }
    
}
echo getcwd();
restore_error_handler();