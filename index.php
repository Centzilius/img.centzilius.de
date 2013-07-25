<?php
if ($h = opendir('./')) {
	while (false !== ($e = readdir($h))) {
		if ($e != '.' /* && $e != '..'*/) {
			echo "<a href=\"$e\">$e</a>";
		} 
	}
} else {
	echo 'Nothing is here yet :(';
}
?>
