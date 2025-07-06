import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
  SmartphoneIcon,
  DownloadIcon,
  MonitorIcon,
  AppleIcon,
  CheckCircleIcon,
  ClockIcon,
  ExternalLinkIcon,
  ShieldCheckIcon,
  ZapIcon,
  BellIcon,
  WifiOffIcon
} from "lucide-react";
import { motion } from "framer-motion";

export function ApkDownloadSection() {
  const handleAndroidDownload = () => {
    const githubApkUrl = 'https://github.com/yukazakiri/dsms-philex-v3/raw/master/public/downloads/dsms-philex.apk';

    // Track download
    localStorage.setItem('dsms_app_download_date', new Date().toISOString());
    localStorage.setItem('dsms_app_download_source', 'github');

    // Direct download
    window.location.href = githubApkUrl;
  };

  const handleWebAccess = () => {
    window.open('https://philexscholar.koamishin.org', '_blank');
  };

  return (
    <div className="bg-muted/50 py-16 md:py-24">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <motion.div
          className="text-center mb-12"
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
        >
          <h2 className="text-3xl md:text-4xl font-bold tracking-tight mb-4">
            Access DSMS Philex
          </h2>
          <p className="text-muted-foreground max-w-2xl mx-auto">
            Get the scholarship management system on your preferred platform. Android is in beta, iOS coming soon, and desktop users can access the full web experience.
          </p>
        </motion.div>

        {/* Platform Options */}
        <div className="grid gap-6 md:grid-cols-3 max-w-5xl mx-auto mb-12">
          {/* Android Card */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.1 }}
          >
            <Card className="h-full border-2 hover:border-primary/20 transition-all duration-300 hover:shadow-lg">
              <CardHeader className="text-center pb-4">
                <div className="mx-auto w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                  <SmartphoneIcon className="h-8 w-8 text-primary" />
                </div>
                <div className="flex items-center justify-center gap-2 mb-2">
                  <CardTitle className="text-xl">Android</CardTitle>
                  <Badge className="bg-orange-100 text-orange-700 border-orange-200">
                    Beta
                  </Badge>
                </div>
                <CardDescription>
                  Native mobile application for Android devices
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <Button
                  onClick={handleAndroidDownload}
                  className="w-full"
                  size="lg"
                >
                  <DownloadIcon className="h-5 w-5 mr-2" />
                  Download APK
                </Button>
                <div className="flex items-center justify-center gap-2 text-sm text-muted-foreground">
                  <CheckCircleIcon className="h-4 w-4 text-primary" />
                  Available now
                </div>
              </CardContent>
            </Card>
          </motion.div>

          {/* iOS Card */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.2 }}
          >
            <Card className="h-full border-2 opacity-60">
              <CardHeader className="text-center pb-4">
                <div className="mx-auto w-16 h-16 bg-muted rounded-full flex items-center justify-center mb-4">
                  <AppleIcon className="h-8 w-8 text-muted-foreground" />
                </div>
                <div className="flex items-center justify-center gap-2 mb-2">
                  <CardTitle className="text-xl text-muted-foreground">iOS</CardTitle>
                  <Badge variant="outline" className="border-muted-foreground/30 text-muted-foreground">
                    Coming Soon
                  </Badge>
                </div>
                <CardDescription>
                  iPhone & iPad application in development
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <Button
                  disabled
                  className="w-full"
                  size="lg"
                  variant="outline"
                >
                  <ClockIcon className="h-5 w-5 mr-2" />
                  In Development
                </Button>
                <div className="flex items-center justify-center gap-2 text-sm text-muted-foreground">
                  <ClockIcon className="h-4 w-4" />
                  Coming soon
                </div>
              </CardContent>
            </Card>
          </motion.div>

          {/* Desktop/Web Card */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.3 }}
          >
            <Card className="h-full border-2 hover:border-primary/20 transition-all duration-300 hover:shadow-lg">
              <CardHeader className="text-center pb-4">
                <div className="mx-auto w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                  <MonitorIcon className="h-8 w-8 text-primary" />
                </div>
                <div className="flex items-center justify-center gap-2 mb-2">
                  <CardTitle className="text-xl">Desktop</CardTitle>
                  <Badge variant="secondary">
                    Web App
                  </Badge>
                </div>
                <CardDescription>
                  Full-featured web application for desktop
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <Button
                  onClick={handleWebAccess}
                  className="w-full"
                  size="lg"
                  variant="outline"
                >
                  <ExternalLinkIcon className="h-5 w-5 mr-2" />
                  Open Web App
                </Button>
                <div className="flex items-center justify-center gap-2 text-sm text-muted-foreground">
                  <CheckCircleIcon className="h-4 w-4 text-primary" />
                  Available now
                </div>
              </CardContent>
            </Card>
          </motion.div>
        </div>

        {/* Key Features */}
        <motion.div
          className="max-w-4xl mx-auto"
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.4 }}
        >
          <div className="text-center mb-8">
            <h3 className="text-2xl font-bold tracking-tight mb-2">Why Use Our Apps?</h3>
            <p className="text-muted-foreground">
              Experience the best of DSMS Philex with these key features
            </p>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div className="text-center">
              <div className="mx-auto w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                <ZapIcon className="h-6 w-6 text-primary" />
              </div>
              <h4 className="font-medium mb-1">Fast Access</h4>
              <p className="text-sm text-muted-foreground">Quick login and seamless navigation</p>
            </div>

            <div className="text-center">
              <div className="mx-auto w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                <BellIcon className="h-6 w-6 text-primary" />
              </div>
              <h4 className="font-medium mb-1">Notifications</h4>
              <p className="text-sm text-muted-foreground">Stay updated on application status</p>
            </div>

            <div className="text-center">
              <div className="mx-auto w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                <ShieldCheckIcon className="h-6 w-6 text-primary" />
              </div>
              <h4 className="font-medium mb-1">Secure</h4>
              <p className="text-sm text-muted-foreground">Protected data and secure login</p>
            </div>

            <div className="text-center">
              <div className="mx-auto w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                <WifiOffIcon className="h-6 w-6 text-primary" />
              </div>
              <h4 className="font-medium mb-1">Offline Ready</h4>
              <p className="text-sm text-muted-foreground">Access key features without internet</p>
            </div>
          </div>
        </motion.div>

        {/* Installation Note */}
        <motion.div
          className="mt-12 text-center"
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.6 }}
        >
          <div className="max-w-2xl mx-auto p-4 bg-card rounded-lg border">
            <p className="text-sm text-muted-foreground">
              <strong className="text-foreground">Android Beta:</strong> The Android app is currently in beta testing.
              You may need to enable "Install from unknown sources" in your device settings to install the APK file.
            </p>
          </div>
        </motion.div>
      </div>
    </div>
  );
}
