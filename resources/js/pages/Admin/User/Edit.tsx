import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Save } from 'lucide-react';
import { toast } from 'sonner';

interface EditUserProps {
  user: {
    id: number;
    name: string;
    email: string;
    role: string;
    status: string;
  };
  statusOptions: { value: string; label: string }[];
  roleOptions: { value: string; label: string }[];
}

export default function EditUser({ user, statusOptions, roleOptions }: EditUserProps) {
  const { data, setData, put, processing, errors } = useForm({
    name: user.name,
    email: user.email,
    role: user.role as 'admin' | 'student',
    status: user.status as 'active' | 'inactive' | 'suspended' | 'pending',
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
    {
      title: user.name,
      href: route('admin.users.show', user.id),
    },
    { title: 'Edit' },
  ];

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    put(route('admin.users.update', user.id), {
      onSuccess: () => {
        toast.success('User updated successfully');
      },
      onError: () => {
        toast.error('Failed to update user');
      },
    });
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Edit User: ${user.name}`} />
      <div className="container mx-auto px-4 py-6">
        <div className="mb-6 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
          <div className="flex items-center gap-4">
            <Button variant="outline" size="sm" asChild>
              <Link href={route('admin.users.show', user.id)}>
                <ArrowLeft className="mr-2 h-4 w-4" />
                Back to User
              </Link>
            </Button>
            <div>
              <h1 className="text-2xl font-bold md:text-3xl">Edit User</h1>
              <p className="text-muted-foreground">Update {user.name}'s information</p>
            </div>
          </div>
        </div>

        <div className="max-w-2xl">
          <Card>
            <CardHeader>
              <CardTitle>User Information</CardTitle>
              <CardDescription>
                Update the user's basic information and settings.
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
                </div>

                <div className="flex justify-end gap-3 pt-6">
                  <Button type="button" variant="outline" asChild>
                    <Link href={route('admin.users.show', user.id)}>
                      Cancel
                    </Link>
                  </Button>
                  <Button type="submit" disabled={processing}>
                    <Save className="mr-2 h-4 w-4" />
                    {processing ? 'Saving...' : 'Save Changes'}
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
