<?php
require_once(__DIR__ . "/config.php");
$_GET['action'] = str_replace("\\/|<>","", $_GET['action']);
$_GET['filename'] = str_replace("\\/|<>","", $_GET['filename']);

function getImageList($dir) {
	$images = array();
	foreach (array_reverse(scandir($dir, SCANDIR_SORT_NONE)) as $i => $value) {
		if (in_array(pathinfo($value, PATHINFO_EXTENSION), array("jpg","png","gif","jpeg"))) {
			$size = getimagesize(IMAGE_ROOT . $value);
			array_push($images, array(
										"filename" => $value,
										"path" => RELATIVE_IMAGE_ROOT . $value,
										"thumbnail" => RELATIVE_SCRIPT_ROOT . "api.php?action=genThumbnail&filename=" . $value,
										"width" => $size[0],
										"height" => $size[1],
									));
		}
	}
	header("Content-type: application/json");
	print(json_encode($images, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	exit;
}

function genDefaultPicture() {
	header("Content-Type: image/gif");
	echo base64_decode("R0lGODlhAQABAIAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
	exit;
}

function genThumbnail($fn, $imagewidth) {
	if (!file_exists($fn)) { genDefaultPicture(); }
	$meta = stat($fn);
	header("ETag: " . md5($meta["ino"]));
	header("Last-Modified: " . gmdate("D, d M Y H:i:s", $meta["mtime"]) . ' GMT');
	header("Expires: " . gmdate('D, d M Y H:i:s', $meta["mtime"]+60*60) . ' GMT'); # Erst nach ner Stunde wieder neu fragen
	header("Cache-Control: public");
	if (strtotime(@$_SERVER['HTTP_IF_MODIFIED_SINCE']) == $meta["mtime"] || @$_SERVER['HTTP_IF_NONE_MATCH'] == md5($meta["ino"])) {
		header("HTTP/1.1 304 Not Modified"); # Nutze deinen Cache
		exit;
	}
	$type = pathinfo($fn, PATHINFO_EXTENSION);
	$createfunc = "imagecreatefrom" . ($type=="jpg"?"jpeg":$type);
	if (!function_exists($createfunc)) { genDefaultPicture(); }
	$img = $createfunc($fn);
	$w = imagesx($img);
	$h = imagesy($img);
	$nw = $imagewidth;
	$nh = floor($h*($nw/$w));
	$new = imagecreatetruecolor($nw, $nh);
	$color = imagecolorallocatealpha($new, 0, 0, 0, 127);
	imagefill($new, 0, 0, $color);
	imagesavealpha($new, true);
	imagecopyresampled($new, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
	header("Content-Type: image/png");
	imagepng($new);
	exit;
}

switch($_GET['action']) {
	case "genThumbnail":
		genThumbnail(IMAGE_ROOT . $_GET['filename'], IMAGE_WIDTH);
		break;
	case "getImages":
		getImageList(IMAGE_ROOT);
		break;
	case "upload":
		// TODO
		break;
	default:
		// TODO
}