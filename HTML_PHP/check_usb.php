<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

function checkUSB() {
    // Method 1: Check mounted drives on Windows
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $drives = array('D:', 'E:', 'F:', 'G:', 'H:', 'I:', 'J:', 'K:');
        foreach ($drives as $drive) {
            if (is_dir($drive . '\\') && is_writable($drive . '\\')) {
                // Check if it's removable (you might need more sophisticated detection)
                return ['connected' => true, 'drive' => $drive];
            }
        }
    }
    // Method 2: Check mounted drives on Linux
    else {
        $mounts = file('/proc/mounts');
        foreach ($mounts as $mount) {
            if (strpos($mount, '/media/') !== false || strpos($mount, '/mnt/') !== false) {
                $parts = explode(' ', $mount);
                $mountPoint = $parts[1];
                if (is_writable($mountPoint)) {
                    return ['connected' => true, 'drive' => $mountPoint];
                }
            }
        }
    }
    
    return ['connected' => false];
}

$result = checkUSB();
echo json_encode($result);
?>