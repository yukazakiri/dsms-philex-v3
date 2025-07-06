import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.philex.dsms',
  appName: 'DSMS Philex',
  webDir: 'public',
  server: {
    url: 'https://philexscholar.koamishin.org/login',
    androidScheme: 'https',
    cleartext: true
  }
};

export default config;
