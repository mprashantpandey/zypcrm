<?php

return [
    'image' => [
        'max_kb' => 4096,
        'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
    ],
    'document' => [
        'max_kb' => 10240,
        'extensions' => ['pdf', 'jpg', 'jpeg', 'png'],
        'mime_types' => ['application/pdf', 'image/jpeg', 'image/png'],
    ],
];
