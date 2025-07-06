<?php
/**
 * Direct APK Download Script
 * This bypasses Laravel routing to serve the APK file directly
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the APK file path
$apkPath = __DIR__ . '/../storage/app/public/apk/dsms-philex.apk';

// Check if file exists
if (!file_exists($apkPath)) {
    http_response_code(404);
    die('APK file not found');
}

// Check if file is readable
if (!is_readable($apkPath)) {
    http_response_code(403);
    die('APK file not accessible');
}

// Get file info
$fileSize = filesize($apkPath);
$fileName = 'dsms-philex-v1.0.0.apk';

// Set headers for APK download
header('Content-Type: application/vnd.android.package-archive');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Length: ' . $fileSize);
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('Accept-Ranges: bytes');

// Clear any output buffers
if (ob_get_level()) {
    ob_end_clean();
}

// Read and output the file
$handle = fopen($apkPath, 'rb');
if ($handle === false) {
    http_response_code(500);
    die('Cannot open APK file');
}

// Output file in chunks to handle large files
while (!feof($handle)) {
    $chunk = fread($handle, 8192); // 8KB chunks
    if ($chunk === false) {
        break;
    }
    echo $chunk;
    flush();
}

fclose($handle);
exit;
