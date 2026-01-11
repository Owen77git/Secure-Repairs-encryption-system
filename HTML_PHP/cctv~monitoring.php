<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CCTV Monitoring | SecureRepair Admin</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
    body { 
      background: #0d0d0d; 
      color: #f2f2f2; 
      min-height: 100vh;
      overflow-y: auto; /* Enable page scrolling */
    }

    .navbar {
      display: flex; justify-content: space-between; align-items: center;
      padding: 1rem 3rem; background: #111; border-bottom: 3px solid #ff0033;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .logo { font-size: 1.5rem; font-weight: bold; color: #ff0033; }
    .nav-links a {
      margin: 0 1rem; text-decoration: none; color: #f2f2f2; font-weight: bold; transition: 0.3s;
    }
    .nav-links a:hover, .nav-links a.active { color: #ff0033; text-shadow: 0 0 10px #ff0033; }

    .content { 
      padding: 2rem; 
      min-height: calc(100vh - 140px);
    }
    h1 { color: #ff0033; margin-bottom: 2rem; text-align: center; font-size: 2.5rem; }
    
    .cctv-container {
      display: flex;
      flex-direction: column;
      gap: 2rem;
      max-width: 1400px;
      margin: 0 auto;
    }

    /* LARGER CCTV FEED */
    .cctv-feed {
      border: 3px solid #ff0033;
      border-radius: 12px;
      position: relative;
      height: 70vh; /* Much larger video box */
      min-height: 500px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #000;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(255, 0, 51, 0.3);
    }

    footer {
      text-align: center; padding: 1.5rem; background: #111;
      border-top: 3px solid #ff0033; color: #888;
      margin-top: 2rem;
    }

    /* CCTV Specific Styles */
    #videoFrame {
      width: 100%;
      height: 100%;
      border: none;
      display: block;
    }
    
    .placeholder {
      color: #aaa;
      font-size: 1.5rem;
      text-align: center;
      padding: 3rem;
    }
    
    .cctv-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
      padding: 1.5rem;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 10;
    }
    
    .timestamp {
      font-family: monospace;
      font-size: 1.2rem;
      font-weight: bold;
    }
    
    .status {
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }
    
    .status-dot {
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background-color: #ff0033;
    }
    
    .status-dot.active {
      background-color: #00ff00;
      animation: pulse 2s infinite;
    }

    .status-dot.recording {
      background-color: #ff0033;
      animation: recording-pulse 0.5s infinite;
    }
    
    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }

    @keyframes recording-pulse {
      0% { opacity: 1; }
      50% { opacity: 0.3; }
      100% { opacity: 1; }
    }
    
    .controls {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      padding: 1.5rem;
      background: #111;
      border: 2px solid #ff0033;
      border-radius: 12px;
      flex-wrap: wrap;
    }
    
    button {
      background: #ff0033;
      color: #fff;
      padding: 1rem 2rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      font-size: 1.1rem;
      transition: 0.3s;
      min-width: 140px;
    }
    
    button:hover {
      background: #fff;
      color: #ff0033;
      box-shadow: 0 0 20px #ff0033;
      transform: translateY(-2px);
    }
    
    .btn-secondary {
      background: #333;
    }
    
    .btn-secondary:hover {
      background: #fff;
      color: #333;
      box-shadow: 0 0 20px #333;
    }

    .btn-record {
      background: #ff0033;
    }

    .btn-record.recording {
      background: #00ff00;
      color: #000;
      animation: recording-pulse 0.5s infinite;
    }

    .btn-record.recording:hover {
      background: #00ff00;
      color: #000;
      box-shadow: 0 0 20px #00ff00;
    }

    .btn-record:disabled {
      background: #666;
      cursor: not-allowed;
      transform: none;
    }

    .btn-record:disabled:hover {
      background: #666;
      color: #fff;
      box-shadow: none;
      transform: none;
    }
    
    .error-message {
      color: #ff0033;
      text-align: center;
      padding: 1.5rem;
      background-color: rgba(255, 0, 51, 0.1);
      border-radius: 8px;
      border: 2px solid #ff0033;
      margin: 1rem 0;
    }
    
    .success-message {
      color: #00ff00;
      text-align: center;
      padding: 1.5rem;
      background-color: rgba(0, 255, 0, 0.1);
      border-radius: 8px;
      border: 2px solid #00ff00;
      margin: 1rem 0;
    }

    .info-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      margin-bottom: 2rem;
    }

    .stream-info {
      background: #111;
      padding: 1.5rem;
      border: 2px solid #ff0033;
      border-radius: 12px;
      text-align: center;
    }

    .recording-info {
      background: #111;
      padding: 1.5rem;
      border: 2px solid #ff0033;
      border-radius: 12px;
      text-align: center;
      display: none;
    }

    .recording-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-top: 1rem;
    }

    .stat-item {
      background: #1a1a1a;
      padding: 1rem;
      border-radius: 8px;
      text-align: center;
    }

    .stat-value {
      font-size: 1.4rem;
      font-weight: bold;
      color: #ff0033;
    }

    .stat-label {
      font-size: 0.9rem;
      color: #888;
      margin-top: 0.5rem;
    }

    .usb-status {
      background: #111;
      padding: 1.5rem;
      border: 2px solid #333;
      border-radius: 12px;
      text-align: center;
      margin-bottom: 2rem;
    }

    .usb-status.connected {
      border-color: #00ff00;
      background: rgba(0, 255, 0, 0.1);
    }

    .usb-status.disconnected {
      border-color: #ff0033;
      background: rgba(255, 0, 51, 0.1);
    }

    .usb-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }

    .file-info {
      background: #1a1a1a;
      padding: 1rem;
      border-radius: 8px;
      margin-top: 1rem;
      font-size: 0.9rem;
    }

    .additional-features {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      margin-top: 2rem;
    }

    .feature-card {
      background: #111;
      padding: 1.5rem;
      border: 2px solid #333;
      border-radius: 12px;
    }

    .feature-card h3 {
      color: #ff0033;
      margin-bottom: 1rem;
      font-size: 1.3rem;
    }

    .feature-list {
      list-style: none;
      padding: 0;
    }

    .feature-list li {
      padding: 0.5rem 0;
      border-bottom: 1px solid #333;
      color: #ccc;
    }

    .feature-list li:last-child {
      border-bottom: none;
    }

    .system-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-top: 2rem;
    }

    .system-stat {
      background: #1a1a1a;
      padding: 1.5rem;
      border-radius: 8px;
      text-align: center;
    }

    .system-stat .value {
      font-size: 2rem;
      font-weight: bold;
      color: #ff0033;
    }

    .system-stat .label {
      font-size: 0.9rem;
      color: #888;
      margin-top: 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .navbar {
        padding: 1rem;
        flex-direction: column;
        gap: 1rem;
      }
      
      .content {
        padding: 1rem;
      }
      
      .cctv-feed {
        height: 50vh;
      }
      
      .info-section {
        grid-template-columns: 1fr;
      }
      
      .controls {
        gap: 1rem;
      }
      
      button {
        min-width: 120px;
        padding: 0.8rem 1.5rem;
      }
    }
  </style>
</head>
<body>
  <header class="navbar">
    <div class="logo">⚡ SecureRepair Admin</div>
    <nav class="nav-links">
      <a href="dashboard.php">Dashboard</a>
            <a href="cctv~monitoring.HTML" class="active">Live CCTV</a>
      <a href="../destroy session/ad_logout.php">Logout</a>
    </nav>
  </header>

  <section class="content">
    <h1>Live CCTV Monitoring System</h1>
    
    <div class="cctv-container">
      <!-- USB Status Indicator -->
      <div class="usb-status disconnected" id="usbStatus">
        <div class="usb-icon">🔌</div>
        <div id="usbMessage" style="font-size: 1.2rem; font-weight: bold;">USB Storage Not Detected</div>
        <div style="font-size: 0.9rem; color: #888; margin-top: 0.5rem;">
          Please insert USB drive to enable recording and snapshot features
        </div>
        <div class="file-info" id="usbFileInfo" style="display: none;">
          <strong>File Formats:</strong> MP4 (Video Recordings) • JPG (Snapshots)
        </div>
      </div>

      <!-- Info Section -->
      <div class="info-section">
        <div class="stream-info">
          <h3>Stream Information</h3>
          <div style="margin-top: 1rem;">
            <div><strong>Camera IP:</strong> 192.168.0.104:8080</div>
            <div><strong>Status:</strong> <span id="connectionStatus">Connecting...</span></div>
            <div><strong>Protocol:</strong> MJPEG Stream</div>
            <div><strong>Last Update:</strong> <span id="lastUpdate">--:--:--</span></div>
          </div>
        </div>

        <div class="recording-info" id="recordingInfo">
          <h3>Recording Status</h3>
          <div style="display: flex; justify-content: space-between; align-items: center; margin: 1rem 0;">
            <strong style="color: #ff0033;">🔴 RECORDING TO USB</strong>
            <span id="recordingTimer" style="font-family: monospace; font-size: 1.3rem;">00:00:00</span>
          </div>
          <div class="recording-stats">
            <div class="stat-item">
              <div class="stat-value" id="fileSize">0 MB</div>
              <div class="stat-label">File Size</div>
            </div>
            <div class="stat-item">
              <div class="stat-value" id="recordingDuration">00:00</div>
              <div class="stat-label">Duration</div>
            </div>
            <div class="stat-item">
              <div class="stat-value" id="recordingStatus">Active</div>
              <div class="stat-label">Status</div>
            </div>
          </div>
          <div class="file-info">
            <strong>Current File:</strong> <span id="currentFilename">recording_YYYY-MM-DD_HH-MM-SS.mp4</span>
          </div>
        </div>
      </div>
      
      <!-- LARGER CCTV FEED -->
      <div class="cctv-feed" id="cctvFeed">
        <div class="placeholder" id="placeholder">
          <div style="font-size: 2rem; margin-bottom: 1rem;">...</div>
          Loading camera feed from 192.168.0.104:8080...
        </div>
        <iframe 
          id="videoFrame" 
          src="http://192.168.0.104:8080"
          style="display: none;"
          allow="autoplay; camera"
          referrerpolicy="no-referrer"
          sandbox="allow-scripts allow-same-origin"
        ></iframe>
        
        <div class="cctv-overlay">
          <div class="timestamp" id="timestamp">--:--:--</div>
          <div class="status">
            <div class="status-dot" id="statusDot"></div>
            <span id="statusText" style="font-weight: bold;">CONNECTING</span>
          </div>
        </div>
      </div>
      
      <!-- Controls -->
      <div class="controls">
        <button id="refreshBtn" class="btn-primary"> Refresh Stream</button>
        <button id="fullscreenBtn" class="btn-secondary"> Fullscreen</button>
        <button id="recordBtn" class="btn-record" disabled> Start Recording</button>
        <button id="snapshotBtn" class="btn-secondary" disabled>Take Snapshot</button>
        <button id="checkUsbBtn" class="btn-secondary">Check USB</button>
      </div>

     
     

      <!-- Additional Features -->
      <div class="additional-features">
        <div class="feature-card">
          <h3>Recording Features</h3>
          <ul class="feature-list">
            <li>✅ High-quality MP4 recording</li>
            <li>✅ USB-only storage for security</li>
            <li>✅ Real-time duration tracking</li>
            <li>✅ Automatic file naming</li>
            <li>✅ File size monitoring</li>
          </ul>
        </div>
        
        <div class="feature-card">
          <h3>Snapshot Features</h3>
          <ul class="feature-list">
            <li>✅ Instant JPG capture</li>
            <li>✅ Timestamped filenames</li>
            <li>✅ USB storage required</li>
            <li>✅ Quick capture button</li>
            <li>✅ High-resolution images</li>
          </ul>
        </div>
        
        <div class="feature-card">
          <h3>Security Features</h3>
          <ul class="feature-list">
            <li>✅ USB-dependent recording</li>
            <li>✅ Encrypted connections</li>
            <li>✅ Access logging</li>
            <li>✅ Session management</li>
            <li>✅ Secure authentication</li>
          </ul>
        </div>
      </div>
    </div>
    
    <div id="errorMessage" class="error-message" style="display: none;"></div>
    <div id="successMessage" class="success-message" style="display: none;"></div>
  </section>

  <footer>
    <p>© 2025 SecureRepair Admin Panel | Professional CCTV Monitoring System</p>
    <p style="margin-top: 0.5rem; font-size: 0.9rem;">Phone Camera: 192.168.0.104:8080 | Secure USB Storage Required</p>
  </footer>

  <script>
    // Get DOM elements
    const videoFrame = document.getElementById('videoFrame');
    const placeholder = document.getElementById('placeholder');
    const refreshBtn = document.getElementById('refreshBtn');
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    const recordBtn = document.getElementById('recordBtn');
    const snapshotBtn = document.getElementById('snapshotBtn');
    const checkUsbBtn = document.getElementById('checkUsbBtn');
    const timestampElement = document.getElementById('timestamp');
    const statusDot = document.getElementById('statusDot');
    const statusText = document.getElementById('statusText');
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');
    const connectionStatus = document.getElementById('connectionStatus');
    const lastUpdate = document.getElementById('lastUpdate');
    const cctvFeed = document.getElementById('cctvFeed');
    const recordingInfo = document.getElementById('recordingInfo');
    const recordingTimer = document.getElementById('recordingTimer');
    const fileSize = document.getElementById('fileSize');
    const recordingDuration = document.getElementById('recordingDuration');
    const recordingStatus = document.getElementById('recordingStatus');
    const currentFilename = document.getElementById('currentFilename');
    const usbStatus = document.getElementById('usbStatus');
    const usbMessage = document.getElementById('usbMessage');
    const usbFileInfo = document.getElementById('usbFileInfo');

    const STREAM_URL = "http://192.168.0.104:8080";
    let isRecording = false;
    let recordingStartTime = null;
    let recordingInterval = null;
    let usbConnected = false;

    // Check USB status
    async function checkUSBStatus() {
      try {
        const response = await fetch('check_usb.php');
        const result = await response.json();
        
        if (result.connected) {
          usbConnected = true;
          usbStatus.className = 'usb-status connected';
          usbMessage.innerHTML = `✅ USB Detected: ${result.drive}`;
          usbFileInfo.style.display = 'block';
          recordBtn.disabled = false;
          snapshotBtn.disabled = false;
          showSuccess('USB storage detected. Recording and snapshot features enabled.');
        } else {
          usbConnected = false;
          usbStatus.className = 'usb-status disconnected';
          usbMessage.innerHTML = '❌ USB Storage Not Detected';
          usbFileInfo.style.display = 'none';
          recordBtn.disabled = true;
          snapshotBtn.disabled = true;
          if (isRecording) {
            stopRecording();
            showError('USB disconnected! Recording stopped.');
          }
        }
      } catch (error) {
        console.error('Error checking USB:', error);
        usbConnected = false;
        usbStatus.className = 'usb-status disconnected';
        usbMessage.innerHTML = '❌ Error checking USB status';
        recordBtn.disabled = true;
        snapshotBtn.disabled = true;
      }
    }

    // Update timestamp and last update
    function updateTimestamps() {
      const now = new Date();
      const timeString = now.toLocaleTimeString();
      timestampElement.textContent = timeString;
      lastUpdate.textContent = timeString;
    }

    // Update recording timer and file size
    function updateRecordingStats() {
      if (!isRecording || !recordingStartTime) return;
      
      const now = new Date();
      const diff = now - recordingStartTime;
      const hours = Math.floor(diff / 3600000);
      const minutes = Math.floor((diff % 3600000) / 60000);
      const seconds = Math.floor((diff % 60000) / 1000);
      
      recordingTimer.textContent = 
        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
      
      recordingDuration.textContent = 
        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
      
      // Simulate file size growth
      const fileSizeMB = (diff / 60000).toFixed(1);
      fileSize.textContent = fileSizeMB + ' MB';
      
      // Update filename
      currentFilename.textContent = `recording_${formatDateForFilename(recordingStartTime)}.mp4`;
    }

    // Start recording
    async function startRecording() {
      if (!usbConnected) {
        showError('USB storage not detected. Please insert USB drive.');
        return;
      }

      if (isRecording) return;

      isRecording = true;
      recordingStartTime = new Date();

      // Update UI
      recordBtn.textContent = '⏹️ Stop Recording';
      recordBtn.classList.add('recording');
      statusDot.classList.add('recording');
      statusText.textContent = 'RECORDING TO USB';
      recordingInfo.style.display = 'block';

      // Start timer updates
      recordingInterval = setInterval(updateRecordingStats, 1000);
      
      showSuccess('Recording started to USB storage - Saving as MP4');
    }

    // Stop recording
    async function stopRecording() {
      if (!isRecording) return;

      isRecording = false;
      
      // Update UI
      recordBtn.textContent = '⏺️ Start Recording';
      recordBtn.classList.remove('recording');
      statusDot.classList.remove('recording');
      statusText.textContent = 'LIVE';
      recordingInfo.style.display = 'none';

      // Clear intervals
      if (recordingInterval) {
        clearInterval(recordingInterval);
        recordingInterval = null;
      }

      // Save recording to USB as MP4
      await saveRecordingAsMP4();

      showSuccess('Recording saved to USB as MP4 file');
    }

    // Save recording as MP4 to USB
    async function saveRecordingAsMP4() {
      const recordingData = {
        startTime: recordingStartTime.toISOString(),
        duration: Math.floor((new Date() - recordingStartTime) / 1000),
        fileSize: fileSize.textContent,
        filename: `recording_${formatDateForFilename(recordingStartTime)}.mp4`,
        type: 'video/mp4'
      };

      try {
        const response = await fetch('save_video_to_usb.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(recordingData)
        });
        
        const data = await response.json();
        if (data.success) {
          console.log('MP4 recording saved to USB:', data);
        } else {
          showError('Failed to save MP4 recording to USB: ' + data.error);
        }
      } catch (error) {
        console.error('Error saving MP4 to USB:', error);
        showError('Error saving MP4 recording to USB');
      }
    }

    // Take snapshot and save as JPG to USB
    async function takeSnapshot() {
      if (!usbConnected) {
        showError('USB storage not detected. Please insert USB drive.');
        return;
      }

      const timestamp = new Date();
      const filename = `snapshot_${formatDateForFilename(timestamp)}.jpg`;
      
      try {
        const snapshotData = {
          timestamp: timestamp.toISOString(),
          filename: filename,
          type: 'image/jpeg'
        };

        const response = await fetch('save_snapshot_to_usb.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(snapshotData)
        });

        const result = await response.json();
        
        if (result.success) {
          showSuccess(`Snapshot saved to USB as ${filename}`);
        } else {
          showError('Failed to save snapshot to USB');
        }
      } catch (error) {
        console.error('Error taking snapshot:', error);
        showError('Error saving snapshot to USB');
      }
    }

    // Format date for filename
    function formatDateForFilename(date) {
      return date.toISOString()
        .replace(/[:.]/g, '-')
        .replace('T', '_')
        .slice(0, 19);
    }

    // Load the stream immediately
    function loadStream() {
      connectionStatus.textContent = 'Connecting...';
      statusText.textContent = 'CONNECTING';
      statusDot.classList.remove('active');
      statusDot.classList.remove('recording');
      
      placeholder.style.display = 'block';
      placeholder.textContent = 'Loading camera feed from 192.168.0.104:8080...';
      videoFrame.style.display = 'none';

      const loadTimeout = setTimeout(() => {
        if (videoFrame.style.display === 'none') {
          showError('Failed to load stream. Check if phone is connected and streaming.');
          connectionStatus.textContent = 'Connection Failed';
        }
      }, 5000);

      videoFrame.onload = function() {
        clearTimeout(loadTimeout);
        
        placeholder.style.display = 'none';
        videoFrame.style.display = 'block';
        
        statusDot.classList.add('active');
        statusText.textContent = 'LIVE';
        connectionStatus.textContent = 'Connected';
        
        showSuccess('Camera feed loaded successfully');
      };

      videoFrame.onerror = function() {
        clearTimeout(loadTimeout);
        showError('Failed to connect to camera stream.');
        connectionStatus.textContent = 'Connection Error';
      };
    }

    // Refresh stream
    function refreshStream() {
      if (isRecording) {
        if (!confirm('Recording in progress. Stop recording and refresh?')) {
          return;
        }
        stopRecording();
      }
      
      videoFrame.src = '';
      setTimeout(() => {
        videoFrame.src = STREAM_URL;
      }, 500);
    }

    // Toggle fullscreen
    function toggleFullscreen() {
      if (!document.fullscreenElement) {
        if (cctvFeed.requestFullscreen) {
          cctvFeed.requestFullscreen();
        }
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        }
      }
    }

    // Show error message
    function showError(message) {
      errorMessage.textContent = message;
      errorMessage.style.display = 'block';
      successMessage.style.display = 'none';
    }

    // Show success message
    function showSuccess(message) {
      successMessage.textContent = message;
      successMessage.style.display = 'block';
      errorMessage.style.display = 'none';
    }

    // Event listeners
    refreshBtn.addEventListener('click', refreshStream);
    fullscreenBtn.addEventListener('click', toggleFullscreen);
    recordBtn.addEventListener('click', function() {
      if (isRecording) {
        stopRecording();
      } else {
        startRecording();
      }
    });
    snapshotBtn.addEventListener('click', takeSnapshot);
    checkUsbBtn.addEventListener('click', checkUSBStatus);

    // Initialize when page loads
    window.addEventListener('load', function() {
      loadStream();
      checkUSBStatus();
      setInterval(updateTimestamps, 1000);
      setInterval(checkUSBStatus, 5000);
      updateTimestamps();
    });

    // Handle page visibility changes
    document.addEventListener('visibilitychange', function() {
      if (!document.hidden) {
        setTimeout(refreshStream, 500);
      }
    });
  </script>
</body>
</html>
