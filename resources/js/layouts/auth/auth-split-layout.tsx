import AppLogoIcon from '@/components/app-logo-icon';
import { Toaster } from '@/components/ui/sonner';
import { type SharedData } from '@/types/index.d';
import { Link, usePage } from '@inertiajs/react';
import { type PropsWithChildren, useEffect } from 'react';
import { toast } from 'sonner';

interface AuthLayoutProps {
    title?: string;
    description?: string;
}

export default function AuthSplitLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
    const page = usePage<SharedData>();
    const { name, quote, flash } = page.props;

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
        <div className="flex h-screen items-center justify-center">
            <div className="grid h-full w-full p-4 lg:grid-cols-2">
                <div className="m-auto flex w-full max-w-xs flex-col items-center">
                    <Link href={route('home')} className="flex items-center">
                        <AppLogoIcon className="h-9 w-9" />
                    </Link>

                    {title && <p className="mt-4 text-xl font-bold tracking-tight">{title}</p>}
                    {description && <p className="text-sm text-balance text-muted-foreground">{description}</p>}

                    <div className="mt-8 w-full">{children}</div>
                </div>
                <div className="hidden rounded-lg bg-muted lg:block">
                    <img src="/images/cover.jpg" className="h-full w-full rounded-lg object-cover brightness-90 filter" alt="" />
                </div>
            </div>
            <Toaster position="top-right" richColors closeButton />
        </div>
    );
}
