import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { configureEcho } from '@laravel/echo-react';

configureEcho({
    broadcaster: 'pusher',
});

// Initialize theme before anything else
initializeTheme();

// Configure Pusher and Echo
(window as any).Pusher = Pusher;

// Initialize Echo and assign to window.Echo
(window as any).Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: import.meta.env.VITE_PUSHER_SCHEME === 'https',
    encrypted: true,
});

// Wrap in a try-catch to debug JSON parsing errors
try {
    const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

    createInertiaApp({
        title: (title) => `${title} - ${appName}`,
        resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
        setup({ el, App, props }) {
            const root = createRoot(el);

            root.render(<App {...props} />);
        },
        progress: {
            color: '#4B5563',
        },
    });
} catch (error) {
    console.error('Error initializing Inertia app:', error);
    document.body.innerHTML =
        '<div style="padding: 20px;"><h1>Application Error</h1><p>The application failed to initialize. Please try again.</p><pre>' +
        (error instanceof Error ? error.message : String(error)) +
        '</pre></div>';
}
