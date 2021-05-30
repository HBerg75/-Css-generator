<?php

 

function option()
{
  

    $shortopts  = "";
    $shortopts .= "s:";  // Valeur requise
    $shortopts .= "i:"; // Valeur requise
    $shortopts .= "r"; // Ces options n'acceptent pas de valeur

    $longopts  = array(
    "output-image:",     // Valeur requise
    "output-style:",    // Valeur requise
    "recursive",        // Ces options n'acceptent pas de valeur
    
    );
    return getopt($shortopts, $longopts);

    
}


$option = option();
$finalresult = gestion_option($option);
$dir_name = end($argv);

if ($finalresult[2] == true) {
    recursive_scan_folder($dir_name, $array_png);
}
else {
    scan_folder($dir_name);
}

function scan_folder($dir_name)
{

    if (is_dir($dir_name) && $dir_open = opendir($dir_name)) {

        while (false !== ($entry = readdir($dir_open))) {
            if (($entry != '.' && $entry != '..') && (preg_match('/.png/', $entry))) {
                 $array_png[] = $dir_name . '/' . $entry;
            }
        }
             closedir($dir_open);  
    }
}
    
    
    

function recursive_scan_folder($dir_name, &$array_png)
{
    global $array_png;
    if (is_dir($dir_name) && $dir_open = opendir($dir_name)) {
        while (false !== ($entry = readdir($dir_open))) {
            if ($entry != '.' && $entry != '..') {
                if (preg_match('/.png/', $entry)) {
                    $array_png[] = $dir_name . '/' . $entry;
                    
                }

                    recursive_scan_folder($dir_name . '/' . $entry, $array_png); 
            }
        }  
        
        closedir($dir_open);  
    }
      return $array_png;
}




 

function image_vide($array_png, $finalresult)
{
    global $array_png;
    $height_total = 0;
    $width_total = 0;
    foreach ($array_png as $value) {
        $img_width = imagesx(imagecreatefrompng($value));
        $width_total = $width_total + ($img_width);

        $img_height = imagesy(imagecreatefrompng($value));
        if ($img_height > $height_total) {
            $height_total = ($img_height);
        }
    }
    $width = 0;
    $height = 0;
    $img = imagecreatetruecolor($width_total, $height_total);
    $background = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $background);
    imagealphablending($img, false);
    imagesavealpha($img, true);
    foreach ($array_png as $value) {
        $image = imagecreatefrompng($value);
        $largeur_source = imagesx($image);
        $hauteur_source = imagesy($image);
        imagecopy($img, $image, $width, 0, 0, 0, $largeur_source, $hauteur_source);
        $width = $width + ($largeur_source);

  
    }
    imagepng($img, $finalresult[0] . ".png");
    imagedestroy($img);
}

image_vide($array_png, $finalresult);


function generate_css_files($array_png, $finalresult)
{
    
    $files = fopen($finalresult[1] . ".css", "w+");
    static $incr = 1;
    $hauteur = 0;
    foreach ($array_png as $value) {
        $height = imagesy(imagecreatefrompng($value));
        $width = imagesx(imagecreatefrompng($value));

         fwrite(
            $files, ".picture" . $incr ."
    {
      background: url('test.png');
      background-repeat: no-repeat;
      display: inline-block;
      background-position: " . $hauteur . "px 0px;
      width:" . $width . "px;
      height:" . $height . "px;
    }\n"
        );
        $incr += 1;
        $hauteur -= $width;
    }

}
generate_css_files($array_png, $finalresult);

function gestion_option($option)
{

    
    $result = ["sprite", "style", false];
    if (array_key_exists("i", $option)) {
        $result[0] = $option["i"];
    }
    else if (array_key_exists("output-image", $option)) {
 
        $result[0] = $option["output-image"];
    }
    if (array_key_exists("s", $option)) {
        $result[1] = $option["s"];
    }
    else if (array_key_exists("output-style", $option)) {
 
        $result[1] = $option["output-style"];
    }
    if (array_key_exists("r", $option)) {
        $result[2] = true;
    }
    else if (array_key_exists("recusrsive", $option)) {
 
        $result[2] = true;
    }

         return $result;
}