import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, UserPlus } from 'lucide-react';
import { toast } from 'sonner';

interface CreateUserProps {
  statusOptions: { value: string; label: string }[];
  roleOptions: { value: string; label: string }[];
}

export default function CreateUser({ statusOptions, roleOptions }: CreateUserProps) {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    email: '',
    role: 'student' as 'admin' | 'student',
    status: 'active' as 'active' | 'inactive' | 'suspended' | 'pending',
    password: '',
    password_confirmation: '',
  });

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Admin Dashboard',
      href: route('admin.dashboard'),
    },
    {
      title: 'Users',
      href: route('admin.users.index'),
    },
    { title: 'Create User' },
  ];

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('admin.users.store'), {
      onSuccess: () => {
        toast.success('User created successfully');
        reset();
      },
      onError: () => {
        toast.error('Failed to create user');
      },
    });
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Create User" />
      <div className="container mx-auto px-4 py-6">
        <div className="mb-6 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
          <div className="flex items-center gap-4">
            <Button variant="outline" size="sm" asChild>
              <Link href={route('admin.users.index')}>
                <ArrowLeft className="mr-2 h-4 w-4" />
                Back to Users
              </Link>
            </Button>
            <div>
              <h1 className="text-2xl font-bold md:text-3xl">Create User</h1>
              <p className="text-muted-foreground">Add a new user to the system</p>
            </div>
          </div>
        </div>

        <div className="max-w-2xl">
          <Card>
            <CardHeader>
              <CardTitle>User Information</CardTitle>
              <CardDescription>
                Enter the user's basic information and settings.
              </CardDescription>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <Label htmlFor="name">Name</Label>
                    <Input
                      id="name"
                      value={data.name}
                      onChange={(e) => setData('name', e.target.value)}
                      required
                      className="mt-1"
                      placeholder="Enter full name"
                    />
                    {errors.name && <p className="text-xs text-red-500 mt-1">{errors.name}</p>}
                  </div>

                  <div>
                    <Label htmlFor="email">Email</Label>
                    <Input
                      id="email"
                      type="email"
                      value={data.email}
                      onChange={(e) => setData('email', e.target.value)}
                      required
                      className="mt-1"
                      placeholder="Enter email address"
                    />
                    {errors.email && <p className="text-xs text-red-500 mt-1">{errors.email}</p>}
                  </div>

                  <div>
                    <Label htmlFor="role">Role</Label>
                    <Select value={data.role} onValueChange={(v) => setData('role', v as 'admin' | 'student')}>
                      <SelectTrigger className="mt-1">
                        <SelectValue placeholder="Select role" />
                      </SelectTrigger>
                      <SelectContent>
                        {roleOptions.map((option) => (
                          <SelectItem key={option.value} value={option.value}>
                            {option.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    {errors.role && <p className="text-xs text-red-500 mt-1">{errors.role}</p>}
                  </div>

                  <div>
                    <Label htmlFor="status">Status</Label>
                    <Select value={data.status} onValueChange={(v) => setData('status', v as 'active' | 'inactive' | 'suspended' | 'pending')}>
                      <SelectTrigger className="mt-1">
                        <SelectValue placeholder="Select status" />
                      </SelectTrigger>
                      <SelectContent>
                        {statusOptions.map((option) => (
                          <SelectItem key={option.value} value={option.value}>
                            {option.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    {errors.status && <p className="text-xs text-red-500 mt-1">{errors.status}</p>}
                  </div>

                  <div>
                    <Label htmlFor="password">Password</Label>
                    <Input
                      id="password"
                      type="password"
                      value={data.password}
                      onChange={(e) => setData('password', e.target.value)}
                      required
                      className="mt-1"
                      placeholder="Enter password"
                    />
                    {errors.password && <p className="text-xs text-red-500 mt-1">{errors.password}</p>}
                  </div>

                  <div>
                    <Label htmlFor="password_confirmation">Confirm Password</Label>
                    <Input
                      id="password_confirmation"
                      type="password"
                      value={data.password_confirmation}
                      onChange={(e) => setData('password_confirmation', e.target.value)}
                      required
                      className="mt-1"
                      placeholder="Confirm password"
                    />
                    {errors.password_confirmation && <p className="text-xs text-red-500 mt-1">{errors.password_confirmation}</p>}
                  </div>
                </div>

                <div className="flex justify-end gap-3 pt-6">
                  <Button type="button" variant="outline" asChild>
                    <Link href={route('admin.users.index')}>
                      Cancel
                    </Link>
                  </Button>
                  <Button type="submit" disabled={processing}>
                    <UserPlus className="mr-2 h-4 w-4" />
                    {processing ? 'Creating...' : 'Create User'}
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>
        </div>
      </div>
    </AppLayout>
  );
}
