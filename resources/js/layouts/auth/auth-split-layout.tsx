import AppLogoIcon from '@/components/app-logo-icon';
import { type SharedData } from '@/types/index.d';
import { Link, usePage } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    title?: string;
    description?: string;
}

export default function AuthSplitLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
    const { name, quote } = usePage<SharedData>().props;

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
        </div>
    );
}
