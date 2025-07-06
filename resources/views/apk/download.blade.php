<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download DSMS Philex APK</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logo {
            width: 80px;
            height: 80px;
            background: #667eea;
            border-radius: 20px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .download-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .download-btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
            text-align: left;
        }
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        .info-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .info-value {
            color: #666;
            font-size: 14px;
        }
        .features {
            text-align: left;
            margin-top: 30px;
        }
        .features h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 8px 0;
            color: #666;
            position: relative;
            padding-left: 25px;
        }
        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }
        .note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
            color: #856404;
        }
        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">DS</div>
        <h1>DSMS Philex</h1>
        <p class="subtitle">Digital Scholarship Management System</p>
        
        @if($apkInfo && $apkInfo->available)
            <a href="{{ $apkInfo->downloadUrl }}" class="download-btn">
                📱 Download APK ({{ $apkInfo->size }})
            </a>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Version</div>
                    <div class="info-value">{{ $apkInfo->version }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Size</div>
                    <div class="info-value">{{ $apkInfo->size }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Android Version</div>
                    <div class="info-value">{{ $apkInfo->minAndroidVersion }}+</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Last Updated</div>
                    <div class="info-value">{{ $apkInfo->buildTime }}</div>
                </div>
            </div>
            
            <div class="features">
                <h3>App Features</h3>
                <ul class="feature-list">
                    <li>Offline access to your applications</li>
                    <li>Push notifications for updates</li>
                    <li>Secure document upload</li>
                    <li>Real-time application tracking</li>
                    <li>Auto-update functionality</li>
                    <li>Native mobile experience</li>
                </ul>
            </div>
            
            <div class="note">
                <strong>Installation Note:</strong> You may need to enable "Install from unknown sources" 
                in your Android settings to install this APK. This is normal for apps not downloaded 
                from the Google Play Store.
            </div>
        @else
            <p style="color: #dc3545; font-weight: 600;">APK is currently not available. Please try again later.</p>
        @endif
    </div>
</body>
</html>
