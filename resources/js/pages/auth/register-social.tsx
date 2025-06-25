import { Head, useForm } from "@inertiajs/react";
import { LoaderCircle } from "lucide-react";
import { FormEventHandler } from "react";

import InputError from "@/components/input-error";
import SocialitePlus from "@/components/socialite-plus";
import TextLink from "@/components/text-link";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import AuthLayout from "@/layouts/auth-layout";

type RegisterForm = {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
};

interface SocialRegisterProps {
  providersConfig: {
    button_text: string;
    disable_credentials_login: boolean;
    providers: { name: string; icon: 'FacebookIcon' | 'GitHubIcon' | 'GoogleIcon' | 'LinkedInIcon'; branded: boolean }[];
  };
}

export default function Register({ providersConfig }: SocialRegisterProps) {
  const { data, setData, post, processing, errors, reset } = useForm<
    Required<RegisterForm>
  >({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post(route("register"), {
      onFinish: () => reset("password", "password_confirmation"),
    });
  };

  const credentialForm = (
      <form className="flex flex-col gap-6" onSubmit={submit}>
        <div className="grid gap-6">
          <div className="grid gap-2">
            <Label htmlFor="name">Name</Label>
            <Input
              id="name"
              type="text"
              required
              autoFocus
              tabIndex={1}
              autoComplete="name"
              value={data.name}
              onChange={(e) => setData("name", e.target.value)}
              disabled={processing}
              placeholder="Full name"
            />
            <InputError message={errors.name} className="mt-2" />
          </div>

          <div className="grid gap-2">
            <Label htmlFor="email">Email address</Label>
            <Input
              id="email"
              type="email"
              required
              tabIndex={2}
              autoComplete="email"
              value={data.email}
              onChange={(e) => setData("email", e.target.value)}
              disabled={processing}
              placeholder="email@example.com"
            />
            <InputError message={errors.email} />
          </div>

          <div className="grid gap-2">
            <Label htmlFor="password">Password</Label>
            <Input
              id="password"
              type="password"
              required
              tabIndex={3}
              autoComplete="new-password"
              value={data.password}
              onChange={(e) => setData("password", e.target.value)}
              disabled={processing}
              placeholder="Password"
            />
            <InputError message={errors.password} />
          </div>

          <div className="grid gap-2">
            <Label htmlFor="password_confirmation">Confirm password</Label>
            <Input
              id="password_confirmation"
              type="password"
              required
              tabIndex={4}
              autoComplete="new-password"
              value={data.password_confirmation}
              onChange={(e) => setData("password_confirmation", e.target.value)}
              disabled={processing}
              placeholder="Confirm password"
            />
            <InputError message={errors.password_confirmation} />
          </div>

        <div className="grid gap-2">
          <Button
            type="submit"
            className="mt-2 w-full"
            tabIndex={5}
            disabled={processing}
          >
            {processing && <LoaderCircle className="w-4 h-4 animate-spin" />}
            Create account
          </Button>
        </div>

        <SocialitePlus
          providersConfig={providersConfig}
          disableCredentialsLogin={providersConfig.disable_credentials_login}
        />

        <div className="text-sm text-center text-muted-foreground">
          Already have an account?{" "}
          <TextLink href={route("login")} tabIndex={6}>
            Log in
          </TextLink>
        </div>
        </div>
      </form>
  );

  const socialOnlyRegister = (
    <div className="flex flex-col gap-6">
      <SocialitePlus
        providersConfig={providersConfig}
        disableCredentialsLogin={providersConfig.disable_credentials_login}
      />
      <div className="text-sm text-center text-muted-foreground">
        Already have an account?{" "}
        <TextLink href={route("login")} tabIndex={6}>
          Log in
        </TextLink>
      </div>
    </div>
  );

  return (
    <AuthLayout
      title={providersConfig.disable_credentials_login ? 'Sign up with' : 'Create an account'}
      description={
        providersConfig.disable_credentials_login
          ? 'Choose a provider to create your account with'
          : 'Enter your details below to create your account'
      }
    >
      <Head title="Register" />
      {providersConfig.disable_credentials_login ? socialOnlyRegister : credentialForm}
    </AuthLayout>
  );
}
