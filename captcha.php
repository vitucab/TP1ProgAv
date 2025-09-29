<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$length = 5;
$characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$maxIndex = strlen($characters) - 1;
$code = '';
for ($i = 0; $i < $length; $i++) {
    $code .= $characters[random_int(0, $maxIndex)];
}

$_SESSION['captcha'] = $code;

$width = 160;
$height = 50;

$image = imagecreatetruecolor($width, $height);
if ($image === false) {
    http_response_code(500);
    exit;
}

$background = imagecolorallocate($image, 240, 244, 255);
$textColor = imagecolorallocate($image, 40, 60, 120);
$noiseColor = imagecolorallocate($image, 180, 200, 240);

imagefill($image, 0, 0, $background);

for ($i = 0; $i < 8; $i++) {
    imageline(
        $image,
        random_int(0, $width),
        random_int(0, $height),
        random_int(0, $width),
        random_int(0, $height),
        $noiseColor
    );
}

$fontSize = 5;
$textBoxWidth = imagefontwidth($fontSize) * strlen($code);
$textBoxHeight = imagefontheight($fontSize);
$x = (int) (($width - $textBoxWidth) / 2);
$y = (int) (($height - $textBoxHeight) / 2);
imagestring($image, $fontSize, $x, $y, $code, $textColor);

header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

imagepng($image);
imagedestroy($image);
