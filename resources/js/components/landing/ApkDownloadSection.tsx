import { Button } from "@/components/ui/button";

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { QRCode } from "@/components/ui/qr-code";
import {
  SmartphoneIcon,
  DownloadIcon,
  QrCodeIcon
} from "lucide-react";
import { motion } from "framer-motion";
import { useState } from "react";



export function ApkDownloadSection() {
  const [showQR, setShowQR] = useState(false);



  const handleDirectInstall = () => {
    // This will trigger the PWA install prompt if available
    if ('serviceWorker' in navigator) {
      // Check if PWA install prompt is available
      window.dispatchEvent(new Event('beforeinstallprompt'));
    }
  };

  const handleDirectApkDownload = async () => {
    // GitHub raw download URL for the APK
    const githubApkUrl = 'https://github.com/yukazakiri/dsms-philex-v3/raw/master/public/downloads/dsms-philex.apk';

    // Show download confirmation
    const confirmed = window.confirm(
      `Download DSMS Philex Mobile App?\n\n` +
      `• Latest version from GitHub\n` +
      `• Offline access to your applications\n` +
      `• Push notifications for updates\n` +
      `• Native mobile experience\n\n` +
      `Note: Enable "Install from unknown sources" in Android settings to install.`
    );

    if (confirmed) {
      try {
        // Track download
        localStorage.setItem('dsms_app_download_date', new Date().toISOString());
        localStorage.setItem('dsms_app_download_source', 'github');

        // Direct download from GitHub
        window.location.href = githubApkUrl;

        // Show success message after a short delay
        setTimeout(() => {
          alert('Download started! Check your downloads folder and enable "Install from unknown sources" if prompted.');
        }, 1000);

      } catch (error) {
        console.error('APK download error:', error);
        // Fallback: open GitHub releases page
        window.open('https://github.com/yukazakiri/dsms-philex-v3/tree/master/public/downloads', '_blank');
      }
    }
  };

  return (
    <section className="py-4 sm:py-8 lg:py-12 bg-gradient-to-br from-primary/5 via-background to-secondary/5 min-h-screen flex items-center">
      <div className="container mx-auto px-3 sm:px-4 lg:px-6 w-full">
        <div className="max-w-md sm:max-w-lg lg:max-w-2xl mx-auto">
          {/* Mobile-First Header */}
          <motion.div
            className="text-center mb-6 sm:mb-8"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <div className="mb-3 sm:mb-4">
              <span className="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-primary/10 text-primary border border-primary/20">
                📱 Mobile App
              </span>
            </div>
            <h2 className="text-xl sm:text-2xl lg:text-3xl font-bold mb-2 sm:mb-3">
              DSMS Philex Mobile
            </h2>
            <p className="text-sm sm:text-base text-muted-foreground px-2">
              Download directly from GitHub • Latest version
            </p>
          </motion.div>

          {/* Main Download Card - Mobile First */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className="w-full"
          >
            <Card className="border-2 border-primary/20 shadow-2xl bg-gradient-to-br from-white to-primary/5 overflow-hidden">
              <CardHeader className="text-center pb-4 sm:pb-6 bg-gradient-to-br from-primary/5 to-transparent">
                <div className="mx-auto w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-primary to-primary/80 rounded-3xl flex items-center justify-center mb-4 shadow-lg">
                  <SmartphoneIcon className="h-10 w-10 sm:h-12 sm:w-12 text-white" />
                </div>
                <CardTitle className="text-lg sm:text-xl lg:text-2xl font-bold">
                  📱 Download APK
                </CardTitle>
                <CardDescription className="text-sm sm:text-base text-muted-foreground">
                  Latest version from GitHub repository
                </CardDescription>
              </CardHeader>

              <CardContent className="space-y-4 sm:space-y-6 p-4 sm:p-6">
                {/* Hero Download Button */}
                <Button
                  onClick={handleDirectApkDownload}
                  className="w-full h-16 sm:h-18 lg:h-20 text-lg sm:text-xl font-bold bg-gradient-to-r from-primary via-primary to-primary/90 hover:from-primary/90 hover:via-primary/90 hover:to-primary/80 text-white shadow-xl transform hover:scale-[1.02] transition-all duration-300 rounded-2xl"
                >
                  <div className="flex items-center justify-center gap-3 sm:gap-4">
                    <DownloadIcon className="h-7 w-7 sm:h-8 sm:w-8" />
                    <div className="text-center">
                      <div className="font-bold">Download Now</div>
                      <div className="text-xs sm:text-sm opacity-90 font-normal">
                        From GitHub • ~13 MB
                      </div>
                    </div>
                  </div>
                </Button>

                {/* Quick Info Cards */}
                <div className="grid grid-cols-2 gap-2 sm:gap-3">
                  <div className="bg-green-50 border border-green-200 rounded-xl p-3 sm:p-4 text-center">
                    <div className="text-green-600 font-semibold text-sm sm:text-base">✓ Safe</div>
                    <div className="text-xs sm:text-sm text-green-700">From GitHub</div>
                  </div>
                  <div className="bg-blue-50 border border-blue-200 rounded-xl p-3 sm:p-4 text-center">
                    <div className="text-blue-600 font-semibold text-sm sm:text-base">📱 Native</div>
                    <div className="text-xs sm:text-sm text-blue-700">Android App</div>
                  </div>
                </div>

                {/* Secondary Actions - Mobile Optimized */}
                <div className="space-y-3">
                  <div className="grid grid-cols-2 gap-3">
                    {/* PWA Install */}
                    <Button
                      onClick={handleDirectInstall}
                      variant="outline"
                      className="h-14 sm:h-16 border-2 hover:border-primary/50 hover:bg-primary/5 rounded-xl"
                    >
                      <div className="flex flex-col items-center gap-1 sm:gap-2">
                        <div className="w-8 h-8 sm:w-10 sm:h-10 bg-primary/10 rounded-full flex items-center justify-center">
                          <SmartphoneIcon className="h-4 w-4 sm:h-5 sm:w-5 text-primary" />
                        </div>
                        <span className="text-xs sm:text-sm font-medium">Install PWA</span>
                      </div>
                    </Button>

                    {/* QR Code Toggle */}
                    <Button
                      onClick={() => setShowQR(!showQR)}
                      variant="outline"
                      className="h-14 sm:h-16 border-2 hover:border-blue-500/50 hover:bg-blue-50 rounded-xl"
                    >
                      <div className="flex flex-col items-center gap-1 sm:gap-2">
                        <div className="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-full flex items-center justify-center">
                          <QrCodeIcon className="h-4 w-4 sm:h-5 sm:w-5 text-blue-600" />
                        </div>
                        <span className="text-xs sm:text-sm font-medium">QR Code</span>
                      </div>
                    </Button>
                  </div>
                </div>



                {/* QR Code Section - Mobile Optimized */}
                {showQR && (
                  <motion.div
                    initial={{ opacity: 0, height: 0 }}
                    animate={{ opacity: 1, height: "auto" }}
                    exit={{ opacity: 0, height: 0 }}
                    transition={{ duration: 0.3 }}
                    className="border-t-2 border-dashed border-primary/20 pt-4 sm:pt-6"
                  >
                    <div className="text-center space-y-4">
                      <h4 className="font-semibold text-base sm:text-lg">📱 Scan to Download</h4>
                      <div className="bg-white p-4 sm:p-6 rounded-2xl shadow-lg inline-block border-2 border-gray-100">
                        <QRCode
                          value="https://github.com/yukazakiri/dsms-philex-v3/raw/master/public/downloads/dsms-philex.apk"
                          size={window.innerWidth < 640 ? 150 : 180}
                          className="mx-auto"
                        />
                      </div>
                      <div className="space-y-1">
                        <p className="text-sm sm:text-base text-muted-foreground">
                          Scan with your phone camera
                        </p>
                        <p className="text-xs text-muted-foreground opacity-75">
                          Direct download from GitHub
                        </p>
                      </div>
                    </div>
                  </motion.div>
                )}
              </CardContent>
            </Card>
          </motion.div>

          {/* Mobile-First Features Section */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.4 }}
            className="mt-6 sm:mt-8"
          >
            <Card className="bg-gradient-to-br from-muted/30 to-background border border-muted/50">
              <CardHeader className="pb-3 sm:pb-4">
                <CardTitle className="text-base sm:text-lg text-center">✨ Why Download?</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-2 gap-3 sm:gap-4">
                  <div className="text-center p-3 sm:p-4 bg-white rounded-xl border border-muted/30">
                    <div className="text-2xl mb-1 sm:mb-2">📱</div>
                    <div className="text-xs sm:text-sm font-medium">Native App</div>
                    <div className="text-xs text-muted-foreground">Fast & smooth</div>
                  </div>
                  <div className="text-center p-3 sm:p-4 bg-white rounded-xl border border-muted/30">
                    <div className="text-2xl mb-1 sm:mb-2">🔔</div>
                    <div className="text-xs sm:text-sm font-medium">Notifications</div>
                    <div className="text-xs text-muted-foreground">Stay updated</div>
                  </div>
                  <div className="text-center p-3 sm:p-4 bg-white rounded-xl border border-muted/30">
                    <div className="text-2xl mb-1 sm:mb-2">📴</div>
                    <div className="text-xs sm:text-sm font-medium">Offline Mode</div>
                    <div className="text-xs text-muted-foreground">Works anywhere</div>
                  </div>
                  <div className="text-center p-3 sm:p-4 bg-white rounded-xl border border-muted/30">
                    <div className="text-2xl mb-1 sm:mb-2">🔒</div>
                    <div className="text-xs sm:text-sm font-medium">Secure</div>
                    <div className="text-xs text-muted-foreground">Safe & private</div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </motion.div>

          {/* Installation Note */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.6, delay: 0.6 }}
            className="mt-4 sm:mt-6 text-center"
          >
            <p className="text-xs sm:text-sm text-muted-foreground px-4">
              💡 <strong>Tip:</strong> Enable "Install from unknown sources" in Android settings to install the APK
            </p>
          </motion.div>
        </div>
      </div>
    </section>
  );
}
