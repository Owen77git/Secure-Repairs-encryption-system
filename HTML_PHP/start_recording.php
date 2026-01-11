<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$input = json_decode(file_get_contents('php://input'), true);
$streamUrl = $input['stream_url'] ?? 'http://192.168.0.101:8080/video';
$usbDrive = $input['usb_drive'] ?? '';

// Get USB path
function getUSBPath($drive = '') {
    if (!empty($drive)) {
        $usbPath = $drive . (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '\\' : '/') . 'SecureRepair_Recordings/Videos/';
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

$filename = 'recording_' . date('Y-m-d_H-i-s') . '.mp4';
$filepath = $usbPath . $filename;

// Start FFmpeg process to capture stream
$ffmpegCommand = "ffmpeg -i \"{$streamUrl}\" -t 3600 -c copy \"{$filepath}\" -y";
$process = popen($ffmpegCommand . " 2>&1", 'r');

// Store process info for later stopping
$recordingData = [
    'filename' => $filename,
    'filepath' => $filepath,
    'process_id' => getmypid(),
    'start_time' => date('Y-m-d H:i:s'),
    'stream_url' => $streamUrl
];

file_put_contents('current_recording.json', json_encode($recordingData));

echo json_encode([
    'success' => true, 
    'filename' => $filename,
    'message' => 'Recording started - Saving actual MP4 video'
]);
?>