<?php
// Script para generar imágenes de marcador de posición dinámicamente
header('Content-Type: image/png');

// Obtener parámetros
$width = isset($_GET['width']) ? (int)$_GET['width'] : 400;
$height = isset($_GET['height']) ? (int)$_GET['height'] : 300;
$text = isset($_GET['text']) ? $_GET['text'] : 'Imagen no disponible';

// Limitar dimensiones por seguridad
$width = min(max($width, 50), 1200);
$height = min(max($height, 50), 1200);

// Crear imagen
$image = imagecreatetruecolor($width, $height);

// Colores
$bg_color = imagecolorallocate($image, 240, 240, 240);
$text_color = imagecolorallocate($image, 50, 50, 50);
$border_color = imagecolorallocate($image, 200, 200, 200);
$accent_color = imagecolorallocate($image, 252, 186, 0); // Color dorado FCBA00

// Rellenar el fondo
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Dibujar un borde
imagerectangle($image, 0, 0, $width - 1, $height - 1, $border_color);

// Dibujar una casa simple si hay espacio suficiente
if ($width >= 200 && $height >= 150) {
    $house_x = $width / 2;
    $house_y = $height / 2 - 30;
    $house_width = min(200, $width * 0.6);
    $house_height = min(150, $height * 0.5);

    // Cuerpo de la casa
    imagefilledrectangle(
        $image, 
        $house_x - $house_width/2, 
        $house_y, 
        $house_x + $house_width/2, 
        $house_y + $house_height, 
        $accent_color
    );

    // Techo de la casa
    $points = [
        $house_x - $house_width/2 - 20, $house_y,
        $house_x, $house_y - 70,
        $house_x + $house_width/2 + 20, $house_y
    ];
    imagefilledpolygon($image, $points, 3, $text_color);

    // Puerta
    imagefilledrectangle(
        $image, 
        $house_x - 30, 
        $house_y + 70, 
        $house_x + 30, 
        $house_y + $house_height, 
        $text_color
    );

    // Ventanas
    imagefilledrectangle(
        $image, 
        $house_x - 70, 
        $house_y + 30, 
        $house_x - 20, 
        $house_y + 80, 
        $bg_color
    );

    imagefilledrectangle(
        $image, 
        $house_x + 20, 
        $house_y + 30, 
        $house_x + 70, 
        $house_y + 80, 
        $bg_color
    );
}

// Texto
$font_size = 5;
$text_width = imagefontwidth($font_size) * strlen($text);
$text_x = ($width - $text_width) / 2;
$text_y = $height - 30;
imagestring($image, $font_size, $text_x, $text_y, $text, $text_color);

// Generar la imagen
imagepng($image);
imagedestroy($image);
?>

