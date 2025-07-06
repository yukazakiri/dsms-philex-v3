import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.philex.dsms',
  appName: 'DSMS Philex',
  webDir: 'public/mobile',
  server: {
    androidScheme: 'https'
  }
};

export default config;
