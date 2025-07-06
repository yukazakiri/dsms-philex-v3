<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ApkController extends Controller
{
    /**
     * Get APK information
     */
    public function getApkInfo()
    {
        $apkPath = 'apk/dsms-philex.apk';
        
        if (!Storage::disk('public')->exists($apkPath)) {
            return response()->json([
                'error' => 'APK file not found',
                'available' => false
            ], 404);
        }

        $apkSize = Storage::disk('public')->size($apkPath);
        $lastModified = Storage::disk('public')->lastModified($apkPath);
        
        return response()->json([
            'available' => true,
            'version' => config('app.apk_version', '1.0.0'),
            'versionCode' => config('app.apk_version_code', 1),
            'size' => $this->formatBytes($apkSize),
            'sizeBytes' => $apkSize,
            'lastUpdated' => Carbon::createFromTimestamp($lastModified)->toISOString(),
            'downloadUrl' => route('apk.download'),
            'checkUpdateUrl' => route('apk.check-update'),
            'minAndroidVersion' => '5.0',
            'targetSdk' => 30,
            'buildTime' => Carbon::createFromTimestamp($lastModified)->format('g:i:s A'),
            'fileSize' => $apkSize,
            'fileSizeFormatted' => $this->formatBytes($apkSize),
            'features' => [
                'offline_access' => true,
                'push_notifications' => true,
                'auto_updates' => true,
                'secure_storage' => true
            ]
        ]);
    }

    /**
     * Download APK file
     */
    public function downloadApk()
    {
        $apkPath = 'apk/dsms-philex.apk';

        if (!Storage::disk('public')->exists($apkPath)) {
            abort(404, 'APK file not found');
        }

        $filePath = Storage::disk('public')->path($apkPath);
        $fileName = 'dsms-philex-v' . config('app.apk_version', '1.0.0') . '.apk';

        // Ensure the file exists and is readable
        if (!file_exists($filePath) || !is_readable($filePath)) {
            abort(404, 'APK file not accessible');
        }

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => filesize($filePath),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Check for APK updates
     */
    public function checkUpdate(Request $request)
    {
        $currentVersion = $request->input('version', '0.0.0');
        $currentVersionCode = $request->input('versionCode', 0);
        
        $latestVersion = config('app.apk_version', '1.0.0');
        $latestVersionCode = config('app.apk_version_code', 1);
        
        $hasUpdate = version_compare($latestVersion, $currentVersion, '>') || 
                    $latestVersionCode > $currentVersionCode;

        $response = [
            'hasUpdate' => $hasUpdate,
            'currentVersion' => $currentVersion,
            'latestVersion' => $latestVersion,
            'currentVersionCode' => $currentVersionCode,
            'latestVersionCode' => $latestVersionCode,
        ];

        if ($hasUpdate) {
            $apkInfo = $this->getApkInfo()->getData();
            $response = array_merge($response, [
                'downloadUrl' => route('apk.download'),
                'updateInfo' => $apkInfo,
                'releaseNotes' => $this->getReleaseNotes($latestVersion),
                'isForced' => $this->isForceUpdate($currentVersionCode, $latestVersionCode),
                'updateSize' => $apkInfo->sizeBytes ?? 0
            ]);
        }

        return response()->json($response);
    }

    /**
     * Upload new APK (admin only)
     */
    public function uploadApk(Request $request)
    {
        $request->validate([
            'apk_file' => 'required|file|mimes:apk|max:50000', // 50MB max
            'version' => 'required|string',
            'version_code' => 'required|integer',
            'release_notes' => 'nullable|string'
        ]);

        $apkFile = $request->file('apk_file');
        $version = $request->input('version');
        $versionCode = $request->input('version_code');
        $releaseNotes = $request->input('release_notes', '');

        // Store the APK file
        $apkPath = $apkFile->storeAs('apk', 'dsms-philex.apk', 'public');

        // Update configuration (you might want to store this in database instead)
        $this->updateApkConfig($version, $versionCode, $releaseNotes);

        // Copy to public downloads folder for backward compatibility
        $publicPath = public_path('downloads/dsms-philex.apk');
        File::copy(Storage::disk('public')->path($apkPath), $publicPath);

        return response()->json([
            'success' => true,
            'message' => 'APK uploaded successfully',
            'version' => $version,
            'versionCode' => $versionCode,
            'path' => $apkPath
        ]);
    }

    /**
     * Get APK download page
     */
    public function downloadPage()
    {
        $apkInfo = $this->getApkInfo()->getData();
        
        return view('apk.download', [
            'apkInfo' => $apkInfo
        ]);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get release notes for a version
     */
    private function getReleaseNotes($version)
    {
        // You can store release notes in database or config
        $releaseNotes = config('app.release_notes', []);
        
        return $releaseNotes[$version] ?? [
            'Bug fixes and performance improvements',
            'Enhanced security features',
            'Improved user interface'
        ];
    }

    /**
     * Check if update is forced
     */
    private function isForceUpdate($currentVersionCode, $latestVersionCode)
    {
        // Force update if version is more than 3 versions behind
        return ($latestVersionCode - $currentVersionCode) > 3;
    }

    /**
     * Update APK configuration
     */
    private function updateApkConfig($version, $versionCode, $releaseNotes)
    {
        // In a real application, you'd store this in database
        // For now, we'll use a simple config approach
        $configPath = config_path('apk.php');
        
        $config = [
            'version' => $version,
            'version_code' => $versionCode,
            'release_notes' => [
                $version => explode("\n", $releaseNotes)
            ],
            'updated_at' => now()->toISOString()
        ];

        File::put($configPath, '<?php return ' . var_export($config, true) . ';');
    }
}
