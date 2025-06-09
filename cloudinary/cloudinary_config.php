<?php
require_once __DIR__ . '/vendor/autoload.php';

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dx78jwu6q',
        'api_key'    => '329885487958288',
        'api_secret' => 'eE5YJoPi1hLJwtEOy16_oDmf6hE'
    ],
    'url' => [
        'secure' => true
    ]
]);
?>
