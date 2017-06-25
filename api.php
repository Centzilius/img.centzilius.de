<?php
require_once(__DIR__ . "/config.php");

$param = Array();
switch($_SERVER['REQUEST_METHOD']) {
	case "GET":
		$param["action"] = str_replace("\\/|<>","", $_GET['action']);
		$param["filename"] = str_replace("\\/|<>","", $_GET['filename']);
		$param["key"] = str_replace("\\/|<>","", $_GET["key"]);
		break;
	case "POST":
		$param["action"] = str_replace("\\/|<>","", $_POST['action']);
		$param["filename"] = str_replace("\\/|<>","", $_POST['filename']);
		$param["key"] = str_replace("\\/|<>","", $_POST["key"]);
		$param["file"] = $_FILES["file"];
		break;
	default:
		http_response_code(405);
		die();
}

function getImageList() {
	$files = array();
	foreach (scandir(IMAGE_ROOT, SCANDIR_SORT_NONE) as $value) {
		if (in_array(strtolower(pathinfo($value, PATHINFO_EXTENSION)), array("jpg","png","gif","jpeg"))) {
			$files[$value] = filemtime(IMAGE_ROOT . $value);
		}
	}
	arsort($files);
	$images = array();
	foreach ($files as $file => $date) {
		$size = getimagesize(IMAGE_ROOT . $file);
		array_push($images, array(
			"filename" => $file,
			"path" => RELATIVE_IMAGE_ROOT . $file,
			"thumbnail" => RELATIVE_SCRIPT_ROOT . "api.php?action=genThumbnail&filename=" . $file,
			"width" => $size[0],
			"height" => $size[1],
			"time" => $date
			));
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
	$type = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
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

function uploadPicture($file) {
	if (!in_array($file["type"], array("image/png", "image/gif", "image/jpeg"))) {
		header("Content-type: application/json");
		print(json_encode(array("success"=>false, "error"=>"Invalid Filetype"), JSON_PRETTY_PRINT));
		exit();
	}
	if (move_uploaded_file($file["tmp_name"], IMAGE_ROOT.$file["name"])) {
		header("Content-type: application/json");
		print(json_encode(array("success"=>true, "error"=>null, "response"=>array("url"=>"https://" . $_SERVER['HTTP_HOST'] . RELATIVE_IMAGE_ROOT . $file["name"]))));
		exit();
	} else {
		header("Content-type: application/json");
		print(json_encode(array("success"=>false, "error"=>"Uploading error", "response"=>null), JSON_PRETTY_PRINT));
		exit();
	}
}

switch($param['action']) {
	case "genThumbnail":
		genThumbnail(IMAGE_ROOT . $param['filename'], IMAGE_WIDTH);
		break;
	case "getImages":
		getImageList();
		break;
	case "upload":
		if (!password_verify($param["key"], API_KEY)) {
			header("Content-type: application/json");
			print(json_encode(array("success"=>false, "error"=>"API Key Invalid"), JSON_PRETTY_PRINT));
			exit();
		}
		uploadPicture($param["file"]);
		break;
	default:
		// TODO
}