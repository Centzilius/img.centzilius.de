<?php
/* pushen auf git:
git add %filename%
git commit -m 'worked on code' 
git push
*/

if (isset($_GET['thumbnail'])) {
  $fn=$_GET['thumbnail'];
  $fn=str_replace("\\/|<>","",$fn);
  if (file_exists($fn)) {
  var_dump($fn);

  }
}
?>

<table>
<tr><th>Preview</th><th>Filename</th></tr>
<?php
if ($h = opendir(__DIR__.'/images')) {
	while (false !== ($e = readdir($h))) {
		if ($e[0]!='.') {
			echo "<tr><td><img src=\"images/".$e."\" width=100 /></td><td><a href=\"$e\">$e</a></td></tr>".PHP_EOL;
		} 
	}
} else {
	echo 'Nothing is here yet :(';
}
?>
</table>
