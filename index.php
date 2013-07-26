<?php

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
    $nw=140; # 140px breit
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
    <div class="row">
      <div class="span3">
        <div class="span6">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Vorschau</th>
                <th>Dateiname</th>
              </tr>
            </thead>
            <tbody>
<?php
if ($h = opendir(__DIR__)) {
	while (false !== ($e = readdir($h))) {
		if ($e[0]!='.' && in_array(pathinfo($e,PATHINFO_EXTENSION),array("jpg","png","gif","jpeg"))) {
			echo "<tr><td><img src=\"index.php?thumbnail=".$e."\" class=img-polaroid alt=Vorschaubild width=140></td><td><a href=\"$e\">$e</a></td></tr>".PHP_EOL;
		} 
	}
} else {
	echo 'Nothing is here yet :(';
}
?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
