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
    $nw=200; # 200px breit
    $nh=floor($h*($nw/$w));
    $new=imagecreatetruecolor($nw,$nh);
    imagecopyresized($new,$img,0,0,0,0,$nw,$nh,$w,$h);
    header("Content-Type: image/jpg");
    imagejpeg($new);
    exit;
  }
}
# Start here with HTML!
?><!DOCTYPE html>
<html><head>

<title><?php echo("Mach mir nen Ordentliches CSS-Layout!"); ?></title>

</head><body>

<table>
<tr><th>Preview</th><th>Filename</th></tr>
<?php
if ($h = opendir(__DIR__)) {
	while (false !== ($e = readdir($h))) {
		if ($e[0]!='.' && in_array(pathinfo($e,PATHINFO_EXTENSION),array("jpg","png","gif","jpeg"))) {
			echo "<tr><td><img src=\"index.php?thumbnail=".$e."\" width=100 /></td><td><a href=\"$e\">$e</a></td></tr>".PHP_EOL;
		} 
	}
} else {
	echo 'Nothing is here yet :(';
}
?>
</table>

</body></html>
