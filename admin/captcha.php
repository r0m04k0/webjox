<?php
session_start();
 
$string = '';
 
for ($i = 0; $i < 5; $i++) {
    // this numbers refer to numbers of the ascii table (lower case)
    $string .= chr(rand(97, 122));
}
 
$_SESSION['code'] = $string;
 
$dir = 'fonts/';
 
$image = imagecreatetruecolor(100,50);
$black = imagecolorallocate($image, 0, 0, 0);
$color = imagecolorallocate($image, rand(0, 200), rand(0, 200), rand(0, 200)); 
$white = imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255));

 
imagefilledrectangle($image,0,0,100,50,$white);
$font = dirname(__FILE__) . '/ariali.ttf';
imagettftext ($image, 20, 0, 5, 40, $color, $font, $string);

for ($i = 0; $i < 15; $i++) {
    $linecolor = imagecolorallocate($image, rand(0, 200), rand(0, 200), rand(0, 200));
    imageline($image, rand(0, 100), rand(0, 100), rand(0, 100), rand(0, 100), $linecolor);
}


header("Content-type: image/png");
imagepng($image);
?>