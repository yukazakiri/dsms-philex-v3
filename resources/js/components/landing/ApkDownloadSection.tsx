import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { QRCode } from "@/components/ui/qr-code";
import {
  SmartphoneIcon,
  DownloadIcon,
  ShieldCheckIcon,
  WifiOffIcon,
  BellIcon,
  StarIcon,
  ExternalLinkIcon,
  QrCodeIcon
} from "lucide-react";
import { motion } from "framer-motion";
import { useState } from "react";

interface FeatureCardProps {
  icon: React.ReactNode;
  title: string;
  description: string;
  delay: number;
}

function FeatureCard({ icon, title, description, delay }: FeatureCardProps) {
  return (
    <motion.div
      className="flex items-start space-x-3"
      initial={{ opacity: 0, x: -20 }}
      animate={{ opacity: 1, x: 0 }}
      transition={{ duration: 0.5, delay: delay }}
    >
      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
        {icon}
      </div>
      <div>
        <h4 className="font-semibold">{title}</h4>
        <p className="text-sm text-muted-foreground">{description}</p>
      </div>
    </motion.div>
  );
}

export function ApkDownloadSection() {
  const [showQR, setShowQR] = useState(false);

  const handlePWABuilderDownload = () => {
    // Open PWABuilder in a new tab with the current domain
    const currentDomain = window.location.origin;
    const pwaBuilderUrl = `https://www.pwabuilder.com/?site=${encodeURIComponent(currentDomain)}`;
    window.open(pwaBuilderUrl, '_blank');
  };

  const handleDirectInstall = () => {
    // This will trigger the PWA install prompt if available
    if ('serviceWorker' in navigator) {
      // Check if PWA install prompt is available
      window.dispatchEvent(new Event('beforeinstallprompt'));
    }
  };

  const handleDirectApkDownload = async () => {
    try {
      // Check if APK exists
      const response = await fetch('/downloads/apk-info.json');
      if (response.ok) {
        const apkInfo = await response.json();

        // Show download confirmation
        const confirmed = window.confirm(
          `Download DSMS Philex APK?\n\n` +
          `Version: ${apkInfo.version}\n` +
          `Size: ${apkInfo.size}\n` +
          `Compatible with Android 5.0+\n\n` +
          `Note: You'll need to enable "Install from unknown sources" in Android settings.`
        );

        if (confirmed) {
          // Direct APK download
          const apkUrl = '/downloads/dsms-philex.apk';
          const link = document.createElement('a');
          link.href = apkUrl;
          link.download = 'dsms-philex.apk';
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        }
      } else {
        // Fallback to download page
        window.open('/downloads/', '_blank');
      }
    } catch (error) {
      console.error('APK download error:', error);
      // Fallback to download page
      window.open('/downloads/', '_blank');
    }
  };

  return (
    <section className="py-16 bg-gradient-to-br from-background to-muted/20">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="mx-auto max-w-6xl">
          {/* Section Header */}
          <motion.div
            className="text-center mb-12"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <Badge variant="outline" className="mb-4 px-4 py-1.5">
              <SmartphoneIcon className="mr-2 h-4 w-4" />
              Mobile App Available
            </Badge>
            <h2 className="text-3xl font-bold mb-4">
              Get the DSMS Philex App
            </h2>
            <p className="text-lg text-muted-foreground max-w-2xl mx-auto">
              Download our Progressive Web App for the best mobile experience. 
              Access your scholarships, submit applications, and stay connected - even offline.
            </p>
          </motion.div>

          <div className="grid lg:grid-cols-2 gap-8 items-center">
            {/* Download Options */}
            <motion.div
              initial={{ opacity: 0, x: -30 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
            >
              <Card className="border-2 border-primary/20 shadow-lg">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <DownloadIcon className="h-5 w-5 text-primary" />
                    Download Options
                  </CardTitle>
                  <CardDescription>
                    Choose your preferred installation method
                  </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  {/* PWA Install Button */}
                  <Button 
                    onClick={handleDirectInstall}
                    className="w-full justify-start h-auto p-4"
                    variant="outline"
                  >
                    <div className="flex items-center gap-3">
                      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-primary-foreground">
                        <SmartphoneIcon className="h-5 w-5" />
                      </div>
                      <div className="text-left">
                        <div className="font-semibold">Install as App</div>
                        <div className="text-sm text-muted-foreground">
                          Add to home screen (Recommended)
                        </div>
                      </div>
                    </div>
                  </Button>

                  {/* Direct APK Download */}
                  <Button
                    onClick={handleDirectApkDownload}
                    className="w-full justify-start h-auto p-4"
                    variant="outline"
                  >
                    <div className="flex items-center gap-3">
                      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600">
                        <DownloadIcon className="h-5 w-5" />
                      </div>
                      <div className="text-left">
                        <div className="font-semibold">Download APK</div>
                        <div className="text-sm text-muted-foreground">
                          Direct APK download (Android)
                        </div>
                      </div>
                    </div>
                  </Button>

                  {/* APK Generation Button */}
                  <Button
                    onClick={handlePWABuilderDownload}
                    className="w-full justify-start h-auto p-4"
                    variant="outline"
                  >
                    <div className="flex items-center gap-3">
                      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                        <ExternalLinkIcon className="h-5 w-5" />
                      </div>
                      <div className="text-left">
                        <div className="font-semibold">Generate APK</div>
                        <div className="text-sm text-muted-foreground">
                          Create APK with PWABuilder
                        </div>
                      </div>
                      <ExternalLinkIcon className="h-4 w-4 ml-auto" />
                    </div>
                  </Button>

                  {/* QR Code Option */}
                  <Button 
                    onClick={() => setShowQR(!showQR)}
                    className="w-full justify-start h-auto p-4"
                    variant="outline"
                  >
                    <div className="flex items-center gap-3">
                      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                        <QrCodeIcon className="h-5 w-5" />
                      </div>
                      <div className="text-left">
                        <div className="font-semibold">QR Code</div>
                        <div className="text-sm text-muted-foreground">
                          Scan to install on mobile
                        </div>
                      </div>
                    </div>
                  </Button>

                  {/* QR Code Display */}
                  {showQR && (
                    <motion.div
                      className="p-4 bg-muted rounded-lg text-center"
                      initial={{ opacity: 0, height: 0 }}
                      animate={{ opacity: 1, height: "auto" }}
                      transition={{ duration: 0.3 }}
                    >
                      <QRCode
                        value="https://philexscholar.koamishin.org/downloads/"
                        size={128}
                        className="mx-auto mb-2"
                      />
                      <p className="text-xs text-muted-foreground mt-1">
                        Scan to access download page
                      </p>
                      <p className="text-xs text-muted-foreground opacity-75">
                        philexscholar.koamishin.org/downloads/
                      </p>
                    </motion.div>
                  )}
                </CardContent>
              </Card>
            </motion.div>

            {/* App Features */}
            <motion.div
              className="space-y-6"
              initial={{ opacity: 0, x: 30 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.4 }}
            >
              <div>
                <h3 className="text-xl font-semibold mb-4">App Features</h3>
                <div className="space-y-4">
                  <FeatureCard
                    icon={<WifiOffIcon className="h-5 w-5" />}
                    title="Offline Access"
                    description="View your applications and documents even without internet"
                    delay={0.6}
                  />
                  <FeatureCard
                    icon={<BellIcon className="h-5 w-5" />}
                    title="Push Notifications"
                    description="Get instant updates on your application status"
                    delay={0.7}
                  />
                  <FeatureCard
                    icon={<ShieldCheckIcon className="h-5 w-5" />}
                    title="Secure & Fast"
                    description="Bank-level security with lightning-fast performance"
                    delay={0.8}
                  />
                  <FeatureCard
                    icon={<StarIcon className="h-5 w-5" />}
                    title="Native Experience"
                    description="Feels like a native app with smooth animations"
                    delay={0.9}
                  />
                </div>
              </div>

              {/* Installation Instructions */}
              <Card className="bg-muted/50">
                <CardHeader>
                  <CardTitle className="text-lg">How to Install</CardTitle>
                </CardHeader>
                <CardContent>
                  <ol className="text-sm space-y-2 text-muted-foreground">
                    <li className="flex items-start gap-2">
                      <span className="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-bold">1</span>
                      Click "Install as App" or look for the install prompt in your browser
                    </li>
                    <li className="flex items-start gap-2">
                      <span className="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-bold">2</span>
                      Confirm the installation when prompted
                    </li>
                    <li className="flex items-start gap-2">
                      <span className="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-bold">3</span>
                      Find the app icon on your home screen or app drawer
                    </li>
                  </ol>
                </CardContent>
              </Card>
            </motion.div>
          </div>
        </div>
      </div>
    </section>
  );
}
