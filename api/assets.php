<?php

require __DIR__ . '/common.php';
// Listing (GET) is public and read-only: it only reveals filenames of assets
// that already live under the publicly-servable backgrounds/ and
// assets/icons/ folders, which the dashboard needs (e.g. for random/rotating
// backgrounds) even for unauthenticated visitors. Uploads still require auth.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    require_auth();
}

$allowed = [
    'background' => ROOT . '/backgrounds',
    'icon'       => ROOT . '/assets/icons'
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $all = [];

    $allowedBackgroundExtensions = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
        'svg',
        'mp4',
        'webm'
    ];

    foreach ($allowed as $kind => $dir) {

        foreach (array_diff(scandir($dir) ?: [], ['.','..']) as $file) {

            if (!is_file($dir . '/' . $file)) {
                continue;
            }

            if ($kind === 'background') {

                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowedBackgroundExtensions, true)) {
                    continue;
                }
            }

            $all[] = [
                'kind' => $kind,
                'name' => $file,
                'path' => ($kind === 'background'
                    ? 'backgrounds/'
                    : 'assets/icons/')
                    . $file
            ];
        }
    }

    json_out(['assets' => $all]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['error' => 'Method'], 405);
}

$kind = $_POST['kind'] ?? '';

if (!isset($allowed[$kind]) || empty($_FILES['file'])) {
    json_out(['error' => 'Invalid upload'], 400);
}

$f = $_FILES['file'];

if ($f['error'] !== UPLOAD_ERR_OK) {
    json_out(['error' => 'Upload failed'], 400);
}

$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

$ok = $kind === 'background'
    ? ['jpg','jpeg','png','webp','gif','svg','mp4','webm']
    : ['jpg','jpeg','png','webp','gif','svg','ico'];

if (!in_array($ext, $ok, true)) {
    json_out(['error' => 'File type not allowed'], 400);
}

$name = preg_replace(
    '/[^a-zA-Z0-9._-]/',
    '_',
    basename($f['name'])
);

if (!move_uploaded_file(
        $f['tmp_name'],
        $allowed[$kind] . '/' . $name
    )) {
    json_out(['error' => 'Destination is not writable'], 500);
}

json_out([
    'ok'   => true,
    'path' => ($kind === 'background'
        ? 'backgrounds/'
        : 'assets/icons/')
        . $name
]);