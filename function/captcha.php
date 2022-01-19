<?php
// Begin the session
session_name("bakatminat");
session_start();

// Set the content type
//header('Content-type: image/png');
header('Cache-control: no-cache');
require ('captcha/rand.php');

$_SESSION['captcha_id'] = $answer;

// If the session is not present, set the variable to an error message
if(!isset($_SESSION['captcha_id']))
    $str = 'ERROR!';
	
// Create an image from button.png
$image = imagecreatefrompng('captcha/captcha.png');

// Set the font colour
$colour = imagecolorallocate($image, 27, 1, 140);

// Set the font
$font = 'captcha/ariali.ttf';

// Set a random integer for the rotation between -15 and 15 degrees
$rotate = rand(-15, 15);
$posx = rand(5, 10);

// Create an image using our original image and adding the detail
imagettftext($image, 16, $rotate, $posx, 33, $colour, $font,$str);

// Output the image as a png
imagepng($image);
?>