<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$input = json_decode(file_get_contents('php://input'), true);
$streamUrl = $input['stream_url'] ?? 'http://192.168.0.101:8080/video';
$usbDrive = $input['usb_drive'] ?? '';

// Get USB path
function getUSBPath($drive = '') {
    if (!empty($drive)) {
        $usbPath = $drive . (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '\\' : '/') . 'SecureRepair_Recordings/Snapshots/';
        if (!is_dir($usbPath)) {
            mkdir($usbPath, 0777, true);
        }
        return $usbPath;
    }
    return false;
}

$usbPath = getUSBPath($usbDrive);

if (!$usbPath) {
    echo json_encode(['success' => false, 'error' => 'USB not found']);
    exit;
}

$filename = 'snapshot_' . date('Y-m-d_H-i-s') . '.jpg';
$filepath = $usbPath . $filename;

// Use FFmpeg to capture single frame
$ffmpegCommand = "ffmpeg -i \"{$streamUrl}\" -ss 00:00:01 -frames:v 1 -q:v 2 \"{$filepath}\" -y";
exec($ffmpegCommand);

if (file_exists($filepath)) {
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'file_size' => round(filesize($filepath) / 1024, 2) . ' KB',
        'message' => 'Actual JPG snapshot saved to USB'
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to capture snapshot']);
}
?>