<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\ProductImage;

$client = require __DIR__ . '/bootstrap.php';

$imageId = $argv[1] ?? 'IMAGE_ID_HERE';

$img = ProductImage::getById($client, $imageId);
var_export($img ? $img->toArray() : null);
echo PHP_EOL;
