import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import { MobileBottomNav } from '@/components/mobile-bottom-nav';
import { PWAInstallPrompt } from '@/components/pwa-install-prompt';
import { Toaster } from '@/components/ui/sonner';
import { NotificationProvider } from '@/hooks/use-notifications';
import { type BreadcrumbItem } from '@/types';
import { type PropsWithChildren, useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { toast } from 'sonner';

export default function AppSidebarLayout({ children, breadcrumbs = [] }: PropsWithChildren<{ breadcrumbs?: BreadcrumbItem[] }>) {
    const page = usePage();
    const { flash } = page.props as {
        flash?: {
            success?: string;
            error?: string;
            warning?: string;
            info?: string;
            status?: string;
        }
    };

    useEffect(() => {
        // Handle success messages
        if (flash?.success) {
            toast.success(flash.success);
        }

        // Handle error messages
        if (flash?.error) {
            toast.error(flash.error);
        }

        // Handle warning messages
        if (flash?.warning) {
            toast.warning(flash.warning);
        }

        // Handle info messages
        if (flash?.info) {
            toast.info(flash.info);
        }

        // Handle status messages (typically from Laravel auth)
        if (flash?.status) {
            // Status messages are usually informational
            toast.info(flash.status);
        }
    }, [flash]);

    return (
        <NotificationProvider>
            <AppShell variant="sidebar">
                <AppSidebar variant="inset" />
                <AppContent variant="sidebar" className="mb-safe md:mb-0">
                    <AppSidebarHeader breadcrumbs={breadcrumbs} />
                    {children}
                </AppContent>
                <MobileBottomNav />
            </AppShell>
            <PWAInstallPrompt />
            <Toaster position="top-right" richColors closeButton />
        </NotificationProvider>
    );
}
