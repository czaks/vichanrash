<?php
session_start();

$n = '';
for ($i = 1; $i <= 4; $i++) {
	$n .= chr(mt_rand(ord('a'), ord('z')));
}
$_SESSION['test'] = $n;

$im = imagecreatetruecolor(40,18);
imagestring($im, 5, 0, 0, $n, $c = imagecolorallocate($im, 255, 255, 255));
header("Content-type: image/png");
imagepng($im);
imagedestroy($im);
?>