import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import {
    FigmaIcon,
    GithubIcon,
    InstagramIcon,
    LoaderCircle,
    TwitchIcon,
    FacebookIcon,
    
} from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AuthLayout from '@/layouts/auth-layout';

type LoginForm = {
    email: string;
    password: string;
    remember: boolean;
};

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const { data, setData, post, processing, errors, reset } = useForm<Required<LoginForm>>({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <AuthLayout title="Log in to your account" description="Enter your email and password below to log in">
            <Head title="Log in" />

            <div className="mt-8 flex items-center gap-3 justify-center">
               
               
                <Button
                    variant="outline"
                    size="icon"
                    className="rounded-full h-10 w-10"
                >
                    <FacebookIcon className="!h-[18px] !w-[18px]" />
                </Button>
            
            </div>

            <div className="my-7 w-full flex items-center justify-center overflow-hidden">
                <Separator />
                <span className="text-sm px-2">OR</span>
                <Separator />
            </div>

            <form className="w-full space-y-4" onSubmit={submit}>
                <div className="space-y-4">
                    <div className="grid gap-2">
                        <Label htmlFor="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="Email"
                            className="w-full"
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="password">Password</Label>
                        <Input
                            id="password"
                            type="password"
                            required
                            tabIndex={2}
                            autoComplete="current-password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="Password"
                            className="w-full"
                        />
                        <InputError message={errors.password} />
                    </div>

                    <div className="flex items-center space-x-3">
                        <Checkbox
                            id="remember"
                            name="remember"
                            checked={data.remember}
                            onClick={() => setData('remember', !data.remember)}
                            tabIndex={3}
                        />
                        <Label htmlFor="remember">Remember me</Label>
                    </div>

                    <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing}>
                        {processing && <LoaderCircle className="h-4 w-4 animate-spin mr-2" />}
                        Continue with Email
                    </Button>
                </div>

                <div className="mt-5 space-y-5">
                    {canResetPassword && (
                        <TextLink href={route('password.request')} className="text-sm block underline text-muted-foreground text-center" tabIndex={5}>
                            Forgot your password?
                        </TextLink>
                    )}
                    <p className="text-sm text-center">
                        Don't have an account?{' '}
                        <TextLink href={route('register')} className="ml-1 underline text-muted-foreground" tabIndex={5}>
                            Create account
                        </TextLink>
                    </p>
                </div>
            </form>

            {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}
        </AuthLayout>
    );
}
