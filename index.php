<?php
/*
This page is made by Nero and Centzilius using Twitter Bootstrap available at twitter.github.io/bootstrap
Thanks to Nero and nilsding for the PHP Code
Thanks to pixeldesu for the idea
*/
define("IMAGE_WIDTH",200);
define("PAGE_IMAGES",5);

if (isset($_GET['thumbnail'])) {
  $fn=$_GET['thumbnail'];
  $fn=str_replace("\\/|<>","",$fn);
  if (file_exists($fn)) {
    $meta=stat($fn);

    header("ETag: ".md5($meta["ino"]));
    header("Last-Modified: ".gmdate('D, d M Y H:i:s',$meta["mtime"]).' GMT');
    header("Expires: ".gmdate('D, d M Y H:i:s',$meta["mtime"]+60*60).' GMT'); # Erst nach ner Stunde wieder neu fragen
    header("Cache-Control: public");
    if (strtotime(@$_SERVER['HTTP_IF_MODIFIED_SINCE'])==$meta["mtime"] || @$_SERVER['HTTP_IF_NONE_MATCH']==md5($meta["ino"])) {
      header("HTTP/1.1 304 Not Modified"); # use that file from your cache, browser. We dont want to generate it again.
      exit;
    }
    $type=pathinfo($fn,PATHINFO_EXTENSION);
    $createfunc="imagecreatefrom".($type=="jpg"?"jpeg":$type);
    if (!function_exists($createfunc)) { # this is not an usable image, echo default image
      header("Content-Type: image/gif");
      echo base64_decode("R0lGODlhAQABAIAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
      exit;
    }
    $img=$createfunc($fn);
    $w=imagesx($img);
    $h=imagesy($img);
    $nw=IMAGE_WIDTH;
    $nh=floor($h*($nw/$w));
    $new=imagecreatetruecolor($nw,$nh);
    #imagealphablending($new, false);  
    $color = imagecolorallocatealpha($new, 0, 0, 0, 127);
    imagefill($new, 0, 0, $color);
    imagesavealpha($new, true);
    imagecopyresized($new,$img,0,0,0,0,$nw,$nh,$w,$h);
    header("Content-Type: image/png");
    imagepng($new);
    exit;
  }
}
# Start here with HTML!
?><!DOCTYPE html>
<html>
<head>
  <title>centzilius.de - Bilder</title>
  <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">
  <script src="http://code.jquery.com/jquery.js"></script>
  <script src="bootstrap/js/bootstrap.js"></script>
  <style type="text/css">
    body {
      padding-top: 60px;
    }
  </style>
</head>
<body>
  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a class="brand" href="https://centzilius.de">centzilius.de</a>
        <div class="nav-collapse collapse">
          <ul class="nav">
            <li><a href="https://ask.centzilius.de">Frag den Cent</a></li>
            <li class="active"><a href="#">Bilder</a></li>
            <li><a href="https://actioncraft.de">ActionCraft</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Vorschau</th>
          <th>Dateiname</th>
        </tr>
      </thead>
      <tbody>
<?php

$page=(isset($_GET['page'])?((int)$_GET['page']):1);

if ($h = opendir(__DIR__)) {
  $files=array();
  while (false !== ($e = readdir($h))) {
    if ($e[0]!='.' && in_array(pathinfo($e,PATHINFO_EXTENSION),array("jpg","png","gif","jpeg"))) {
      $files[$e]=filemtime($e);
    } 
  }
  arsort($files);
  $files=array_keys($files);
  $start=($page-1)*PAGE_IMAGES;
  for ($i=$start;$i<$start+PAGE_IMAGES; $i++) {
    if (!isset($files[$i])) break;
    $e=$files[$i];
    echo "<tr><td><a href=\"$e\"><img src=\"index.php?thumbnail=".$e."\" class=img-polaroid alt=Vorschau></a></td><td><a href=\"$e\">$e</a></td></tr>".PHP_EOL;
  }
} ?>
      </tbody>
    </table>
    <div class="pagination pagination-centered">
    <ul>
<?php
  if ($page>1) echo('<li><a href="?page='.($page-1).'">&laquo;</a></li>'.PHP_EOL);
  for ($j=$page-3;$j<=$page+3; $j++) {
    if ($j>0 && ($j-1)*PAGE_IMAGES<count($files)) echo('<li>'.($j==$page?'<span>'.$j.'</span>':'<a href="?page='.$j.'">'.$j.'</a>').'</li>'.PHP_EOL);
  }  
  if ($i<count($files)) echo('<li><a href="?page='.($page+1).'">&raquo;</a></li>'.PHP_EOL);
?>
    </ul>
    </div>
  </div>
</body>
</html>
