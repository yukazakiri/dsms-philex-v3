import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { ArrowLeft, Edit, Mail, Phone, MapPin, GraduationCap, Calendar, Shield, User, Key, MoreHorizontal } from 'lucide-react';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { router } from '@inertiajs/react';
import { toast } from 'sonner';

interface UserShowProps {
  user: {
    id: number;
    name: string;
    email: string;
    role: string;
    status: string;
    last_login_at?: string;
    created_at: string;
    updated_at: string;
    email_verified_at?: string;
    avatar_url?: string;
    cover_image_url?: string;
    provider?: string;
    student_profile?: {
      address: string;
      city: string;
      state: string;
      zip_code: string;
      phone_number: string;
      school_type: string;
      school_level: string;
      school_name: string;
      student_id?: string;
      gpa?: number;
    };
  };
}

export default function UserShow({ user }: UserShowProps) {
  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Admin Dashboard',
      href: route('admin.dashboard'),
    },
    {
      title: 'Users',
      href: route('admin.users.index'),
    },
    { title: user.name },
  ];

  const getStatusBadgeVariant = (status: string) => {
    switch (status) {
      case 'active': return 'default';
      case 'inactive': return 'secondary';
      case 'suspended': return 'destructive';
      case 'pending': return 'outline';
      default: return 'secondary';
    }
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`User: ${user.name}`} />
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
              <h1 className="text-2xl font-bold md:text-3xl">{user.name}</h1>
              <p className="text-muted-foreground">User Details</p>
            </div>
          </div>
          <div className="flex items-center gap-2">
            <Button asChild>
              <Link href={route('admin.users.edit', user.id)}>
                <Edit className="mr-2 h-4 w-4" />
                Edit User
              </Link>
            </Button>
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="outline">
                  <MoreHorizontal className="h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuItem onClick={() => {
                  router.post(route('admin.users.send-password-reset', user.id), {}, {
                    onSuccess: () => toast.success('Password reset email sent'),
                    onError: () => toast.error('Failed to send password reset email'),
                  });
                }}>
                  <Key className="mr-2 h-4 w-4" />
                  Send Password Reset
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => {
                  router.post(route('admin.users.force-password-change', user.id), {}, {
                    onSuccess: () => toast.success('User will be required to change password'),
                    onError: () => toast.error('Failed to force password change'),
                  });
                }}>
                  <Key className="mr-2 h-4 w-4" />
                  Force Password Change
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* User Profile Card */}
          <div className="lg:col-span-1">
            <Card>
              <CardHeader className="text-center">
                <div className="flex justify-center mb-4">
                  <Avatar className="h-24 w-24">
                    <AvatarImage src={user.avatar_url} alt={user.name} />
                    <AvatarFallback className="text-2xl">
                      {user.name.split(' ').map(n => n[0]).join('')}
                    </AvatarFallback>
                  </Avatar>
                </div>
                <CardTitle>{user.name}</CardTitle>
                <CardDescription>{user.email}</CardDescription>
                <div className="flex justify-center gap-2 mt-4">
                  <Badge variant={user.role === 'admin' ? 'secondary' : 'outline'}>
                    {user.role === 'admin' ? 'Admin' : 'Student'}
                  </Badge>
                  <Badge variant={getStatusBadgeVariant(user.status)}>
                    {user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                  </Badge>
                </div>
              </CardHeader>
            </Card>
          </div>

          {/* User Information */}
          <div className="lg:col-span-2 space-y-6">
            {/* Account Information */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <User className="h-5 w-5" />
                  Account Information
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Email</label>
                    <div className="flex items-center gap-2 mt-1">
                      <Mail className="h-4 w-4 text-muted-foreground" />
                      <span>{user.email}</span>
                      {user.email_verified_at && (
                        <Badge variant="outline" className="text-xs">Verified</Badge>
                      )}
                    </div>
                  </div>
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Role</label>
                    <div className="flex items-center gap-2 mt-1">
                      <Shield className="h-4 w-4 text-muted-foreground" />
                      <span className="capitalize">{user.role}</span>
                    </div>
                  </div>
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Status</label>
                    <div className="mt-1">
                      <Badge variant={getStatusBadgeVariant(user.status)}>
                        {user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                      </Badge>
                    </div>
                  </div>
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Login Provider</label>
                    <div className="mt-1">
                      <span className="capitalize">{user.provider || 'Email'}</span>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Activity Information */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Calendar className="h-5 w-5" />
                  Activity Information
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Last Login</label>
                    <p className="mt-1">{user.last_login_at || 'Never'}</p>
                  </div>
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Account Created</label>
                    <p className="mt-1">{user.created_at}</p>
                  </div>
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Last Updated</label>
                    <p className="mt-1">{user.updated_at}</p>
                  </div>
                  <div>
                    <label className="text-sm font-medium text-muted-foreground">Email Verified</label>
                    <p className="mt-1">{user.email_verified_at || 'Not verified'}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Student Profile Information */}
            {user.student_profile && (
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <GraduationCap className="h-5 w-5" />
                    Student Profile
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label className="text-sm font-medium text-muted-foreground">Phone Number</label>
                      <div className="flex items-center gap-2 mt-1">
                        <Phone className="h-4 w-4 text-muted-foreground" />
                        <span>{user.student_profile.phone_number}</span>
                      </div>
                    </div>
                    <div>
                      <label className="text-sm font-medium text-muted-foreground">Address</label>
                      <div className="flex items-center gap-2 mt-1">
                        <MapPin className="h-4 w-4 text-muted-foreground" />
                        <span>
                          {user.student_profile.address}, {user.student_profile.city}, {user.student_profile.state} {user.student_profile.zip_code}
                        </span>
                      </div>
                    </div>
                    <div>
                      <label className="text-sm font-medium text-muted-foreground">School</label>
                      <p className="mt-1">{user.student_profile.school_name}</p>
                    </div>
                    <div>
                      <label className="text-sm font-medium text-muted-foreground">School Type</label>
                      <p className="mt-1 capitalize">{user.student_profile.school_type.replace('_', ' ')}</p>
                    </div>
                    <div>
                      <label className="text-sm font-medium text-muted-foreground">School Level</label>
                      <p className="mt-1">{user.student_profile.school_level}</p>
                    </div>
                    {user.student_profile.student_id && (
                      <div>
                        <label className="text-sm font-medium text-muted-foreground">Student ID</label>
                        <p className="mt-1">{user.student_profile.student_id}</p>
                      </div>
                    )}
                    {user.student_profile.gpa && (
                      <div>
                        <label className="text-sm font-medium text-muted-foreground">GPA</label>
                        <p className="mt-1">{user.student_profile.gpa}</p>
                      </div>
                    )}
                  </div>
                </CardContent>
              </Card>
            )}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
