# 🤖 Android Studio APK Build Guide

## 📋 Overview

This guide will help you install Android Studio and build a real APK for your DSMS Philex application using Capacitor and Android Studio tools.

## 🚀 Quick Start

### Step 1: Install Android Studio

```bash
# Run the automated setup script
./scripts/setup-android-studio.sh
```

This script will:
- ✅ Install Java (OpenJDK 17) if needed
- ✅ Download Android Studio
- ✅ Extract and set up Android Studio
- ✅ Configure environment variables
- ✅ Create desktop shortcut
- ✅ Install required dependencies

### Step 2: Complete Android Studio Setup

1. **Start Android Studio**:
   ```bash
   ~/Android/android-studio/bin/studio.sh
   ```
   Or use the desktop shortcut

2. **Complete Setup Wizard**:
   - Accept licenses
   - Choose "Standard" installation
   - Download Android SDK (this may take 15-30 minutes)

3. **Install Required Components**:
   - Android SDK Platform (API 34 recommended)
   - Android SDK Build-Tools
   - Android SDK Platform-Tools
   - Android SDK Command-line Tools

### Step 3: Build Your APK

```bash
# Run the build script
./scripts/build-apk-android-studio.sh
```

Choose from these options:
1. **Debug APK** - Quick build for testing
2. **Release APK** - Optimized for distribution
3. **Open in Android Studio** - Manual build with GUI
4. **Create signing keystore** - For release builds
5. **Clean build cache** - Fix build issues

## 📱 Build Options Explained

### Debug APK (Recommended for Testing)
- ✅ **Fast build** - Usually takes 2-5 minutes
- ✅ **No signing required** - Uses debug certificate
- ✅ **Good for testing** - Install and test immediately
- ⚠️ **Not for distribution** - Debug builds only

**Command**: Choose option 1 in build script

### Release APK (For Distribution)
- ✅ **Optimized** - Smaller size, better performance
- ✅ **Production ready** - Can be distributed to users
- ⚠️ **Requires signing** - Need to create keystore first
- ⏳ **Longer build time** - May take 5-15 minutes

**Command**: Choose option 2 in build script

## 🔐 APK Signing (For Release Builds)

### Create Keystore
```bash
./scripts/build-apk-android-studio.sh
# Choose option 4: Create signing keystore
```

You'll need to provide:
- Keystore password
- Key alias (e.g., "dsms-philex")
- Key password
- Your name and organization details

**⚠️ Important**: Keep your keystore file and passwords safe! You'll need them for future app updates.

## 🛠️ Manual Build in Android Studio

### Option 1: Using Build Script
```bash
./scripts/build-apk-android-studio.sh
# Choose option 3: Open in Android Studio
```

### Option 2: Manual Steps
1. **Open Android Studio**
2. **Open Project**: Select `your-project/android` folder
3. **Wait for Gradle Sync** (may take a few minutes)
4. **Build APK**:
   - Go to **Build** → **Build Bundle(s) / APK(s)** → **Build APK(s)**
   - Wait for build to complete
   - APK will be in `app/build/outputs/apk/`

## 📂 APK Locations

After successful build:
- **Debug APK**: `android/app/build/outputs/apk/debug/app-debug.apk`
- **Release APK**: `android/app/build/outputs/apk/release/app-release.apk`
- **Downloaded APK**: `public/downloads/dsms-philex.apk`

## 🔧 Troubleshooting

### Common Issues

#### 1. "SDK location not found"
```bash
# Set environment variables
export ANDROID_HOME="$HOME/Android/Sdk"
export ANDROID_SDK_ROOT="$ANDROID_HOME"
source ~/.bashrc
```

#### 2. "Gradle build failed"
```bash
# Clean and rebuild
cd android
./gradlew clean
./gradlew assembleDebug
```

#### 3. "Java version issues"
```bash
# Check Java version
java -version
# Should be Java 11 or higher
```

#### 4. "Permission denied"
```bash
# Make gradlew executable
chmod +x android/gradlew
```

### Build Requirements

- **Java**: OpenJDK 11 or higher
- **Android SDK**: API 22+ (minimum), API 34 (target)
- **Build Tools**: Latest version
- **RAM**: 8GB+ recommended
- **Storage**: 10GB+ free space

## 📊 APK Information

Your built APK will include:
- **App Name**: DSMS Philex
- **Package ID**: com.philex.dsms
- **Version**: 1.0.0
- **Min SDK**: Android 5.1 (API 22)
- **Target SDK**: Android 14 (API 34)
- **Architecture**: Universal (ARM + x86)
- **Framework**: Capacitor + React + Laravel

## 🎯 Next Steps After Building

### 1. Test Your APK
```bash
# Install on Android device
adb install public/downloads/dsms-philex.apk

# Or transfer to device and install manually
```

### 2. Distribute Your APK
- Upload to your website (already configured)
- Share download link: `https://philexscholar.koamishin.org/downloads/dsms-philex.apk`
- Consider Google Play Store for wider distribution

### 3. Update APK Info
The build script automatically updates `public/downloads/apk-info.json` with:
- File size and build information
- Build date and type
- Installation instructions

## 🚀 Advanced Options

### Custom Build Configuration

Edit `android/app/build.gradle` for:
- App version and version code
- Minimum SDK version
- Target SDK version
- App signing configuration
- ProGuard/R8 optimization

### Capacitor Configuration

Edit `capacitor.config.ts` for:
- App ID and name
- Server URL
- Plugin configuration
- Platform-specific settings

## 📞 Support

If you encounter issues:

1. **Check Android Studio logs** in the IDE
2. **Run with verbose output**:
   ```bash
   cd android
   ./gradlew assembleDebug --info
   ```
3. **Check Capacitor documentation**: https://capacitorjs.com/docs/android
4. **Verify Android SDK installation** in Android Studio settings

## 🎉 Success!

Once built successfully, your APK will be:
- ✅ **Real Android package** (no more parsing errors!)
- ✅ **Installable on any Android device**
- ✅ **Contains your full web application**
- ✅ **Works offline** with PWA features
- ✅ **Professional appearance** with proper icons

Your users can now download and install a native Android app that wraps your Laravel web application!
