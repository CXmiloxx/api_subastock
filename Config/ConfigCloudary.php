<?php
require __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dkokax0rl',
        'api_key' => '163189793294151',
        'api_secret' => 'vUnPULQr6w4bfTIIt8STlCDAd6U',
    ],
    'url' => [
        'secure' => true
    ]
]);

?>