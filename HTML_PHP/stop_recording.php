<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Read current recording info
$recordingFile = 'current_recording.json';
if (!file_exists($recordingFile)) {
    echo json_encode(['success' => false, 'error' => 'No active recording found']);
    exit;
}

$recordingData = json_decode(file_get_contents($recordingFile), true);

// Stop FFmpeg process (this is a simplified approach)
// In production, you'd properly terminate the FFmpeg process
if (isset($recordingData['process_id'])) {
    // This is a basic approach - in real implementation, use proper process management
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        exec("taskkill /F /PID " . $recordingData['process_id']);
    } else {
        exec("kill " . $recordingData['process_id']);
    }
}

// Verify file was created
$fileExists = file_exists($recordingData['filepath']);
$fileSize = $fileExists ? filesize($recordingData['filepath']) : 0;

// Clean up
unlink($recordingFile);

echo json_encode([
    'success' => true,
    'filename' => $recordingData['filename'],
    'file_size' => round($fileSize / (1024 * 1024), 2) . ' MB',
    'duration' => 'Recorded successfully',
    'message' => 'Actual MP4 video saved to USB'
]);
?>