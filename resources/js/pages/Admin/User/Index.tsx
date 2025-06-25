import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm, router } from '@inertiajs/react';
import { User as UserType, BreadcrumbItem } from '@/types';
import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { PlusCircle, Search, Shield, Trash2, MoreHorizontal, Filter, X, Download, Upload } from 'lucide-react';
import { Sheet, SheetContent } from '@/components/ui/sheet';
import UserForm from './UserForm';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ConfirmDialog } from '@/components/ConfirmDialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { toast } from 'sonner';

interface UserIndexProps {
  users: {
    data: (UserType & {
      role: string;
      status: string;
      last_login_at?: string;
      created_at: string;
      email_verified_at?: string;
      status_badge_color: string;
      avatar_url?: string;
    })[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search?: string;
    status?: string;
    role?: string;
  };
  statusOptions: { value: string; label: string }[];
  roleOptions: { value: string; label: string }[];
}

export default function UserIndex({ users, filters, statusOptions, roleOptions }: UserIndexProps) {
  const { data, setData, get, processing } = useForm({
    search: filters.search || '',
    status: filters.status || 'all',
    role: filters.role || 'all',
  });

  // Only table view
  const [isMobile, setIsMobile] = useState(false);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingUser, setEditingUser] = useState<(UserType & { role: string; status: string }) | null>(null);
  const [selectedUsers, setSelectedUsers] = useState<number[]>([]);
  const [showFilters, setShowFilters] = useState(false);
  const [showImportDialog, setShowImportDialog] = useState(false);
  const [importFile, setImportFile] = useState<File | null>(null);

  // Remove viewMode handling

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Admin Dashboard',
      href: route('admin.dashboard'),
    },
    { title: 'Users' },
  ];

  const searchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    get(route('admin.users.index'));
  };

  const clearFilters = () => {
    setData({ search: '', status: 'all', role: 'all' });
    get(route('admin.users.index'));
  };

  const deleteUser = (userId: number) => {
    router.delete(route('admin.users.destroy', userId), {
      onSuccess: () => {
        toast.success('User deleted successfully');
      },
      onError: () => {
        toast.error('Failed to delete user');
      },
    });
  };

  const updateUserStatus = (userId: number, status: string) => {
    router.patch(route('admin.users.update-status', userId), { status }, {
      onSuccess: () => {
        toast.success('User status updated successfully');
      },
      onError: () => {
        toast.error('Failed to update user status');
      },
    });
  };

  const toggleUserSelection = (userId: number) => {
    setSelectedUsers(prev =>
      prev.includes(userId)
        ? prev.filter(id => id !== userId)
        : [...prev, userId]
    );
  };

  const toggleAllUsers = () => {
    if (selectedUsers.length === users.data.length) {
      setSelectedUsers([]);
    } else {
      setSelectedUsers(users.data.map(user => user.id));
    }
  };

  const handleExport = () => {
    const params = new URLSearchParams();
    if (data.search) params.append('search', data.search);
    if (data.status && data.status !== 'all') params.append('status', data.status);
    if (data.role && data.role !== 'all') params.append('role', data.role);

    window.location.href = route('admin.users.export') + '?' + params.toString();
  };

  const handleImport = () => {
    if (!importFile) {
      toast.error('Please select a file to import');
      return;
    }

    const formData = new FormData();
    formData.append('csv_file', importFile);

    router.post(route('admin.users.import'), formData, {
      onSuccess: () => {
        setShowImportDialog(false);
        setImportFile(null);
        toast.success('Users imported successfully');
      },
      onError: () => {
        toast.error('Failed to import users');
      },
    });
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Users Management" />
      <div className="container mx-auto px-4 py-6">
        <div className="mb-6 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
          <div className="flex-1">
            <h1 className="mb-1 text-2xl font-bold md:text-3xl">Users</h1>
            <p className="text-muted-foreground">Manage application users and their roles</p>
          </div>
          <div className="flex items-center gap-3">
            <Button
              variant="outline"
              onClick={() => setShowFilters(!showFilters)}
            >
              <Filter className="mr-2 h-4 w-4" />
              Filters
            </Button>
            <Button
              variant="outline"
              onClick={handleExport}
            >
              <Download className="mr-2 h-4 w-4" />
              Export
            </Button>
            <Button
              variant="outline"
              onClick={() => setShowImportDialog(true)}
            >
              <Upload className="mr-2 h-4 w-4" />
              Import
            </Button>
            <Button asChild>
              <Link href={route('admin.users.create')}>
                <PlusCircle className="mr-2 h-4 w-4" />
                <span className="hidden sm:inline">Create User</span>
              </Link>
            </Button>
          </div>
        </div>

        {/* Search and Filters */}
        <div className="mb-6 space-y-4">
          <Card>
            <CardHeader className="pb-3">
              <CardTitle>Search Users</CardTitle>
              <CardDescription>Find users by name or email</CardDescription>
            </CardHeader>
            <CardContent>
              <form onSubmit={searchSubmit} className="relative max-w-md">
                <Search className="text-muted-foreground absolute top-2.5 left-2.5 h-4 w-4" />
                <Input
                  placeholder="Search..."
                  className="pl-8"
                  value={data.search}
                  onChange={(e) => setData('search', e.target.value)}
                />
              </form>
            </CardContent>
          </Card>

          {showFilters && (
            <Card>
              <CardHeader className="pb-3">
                <CardTitle>Filters</CardTitle>
                <CardDescription>Filter users by status and role</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label className="text-sm font-medium mb-2 block">Status</label>
                    <Select value={data.status} onValueChange={(value) => setData('status', value)}>
                      <SelectTrigger>
                        <SelectValue placeholder="All statuses" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="all">All statuses</SelectItem>
                        {statusOptions.map((option) => (
                          <SelectItem key={option.value} value={option.value}>
                            {option.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>
                  <div>
                    <label className="text-sm font-medium mb-2 block">Role</label>
                    <Select value={data.role} onValueChange={(value) => setData('role', value)}>
                      <SelectTrigger>
                        <SelectValue placeholder="All roles" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="all">All roles</SelectItem>
                        {roleOptions.map((option) => (
                          <SelectItem key={option.value} value={option.value}>
                            {option.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>
                  <div className="flex items-end gap-2">
                    <Button onClick={searchSubmit} disabled={processing}>
                      {processing ? 'Searching...' : 'Apply Filters'}
                    </Button>
                    <Button variant="outline" onClick={clearFilters}>
                      <X className="mr-2 h-4 w-4" />
                      Clear
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>
          )}
        </div>

        {/* User Statistics */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <Card>
            <CardContent className="p-4">
              <div className="text-2xl font-bold">{users.total}</div>
              <p className="text-xs text-muted-foreground">Total Users</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="text-2xl font-bold text-green-600">
                {users.data.filter(u => u.status === 'active').length}
              </div>
              <p className="text-xs text-muted-foreground">Active Users</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="text-2xl font-bold text-blue-600">
                {users.data.filter(u => u.role === 'admin').length}
              </div>
              <p className="text-xs text-muted-foreground">Administrators</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="text-2xl font-bold text-purple-600">
                {users.data.filter(u => u.role === 'student').length}
              </div>
              <p className="text-xs text-muted-foreground">Students</p>
            </CardContent>
          </Card>
        </div>

        {/* Bulk Actions */}
        {selectedUsers.length > 0 && (
          <Card className="mb-6">
            <CardContent className="py-4">
              <div className="flex items-center justify-between">
                <span className="text-sm font-medium">
                  {selectedUsers.length} user{selectedUsers.length > 1 ? 's' : ''} selected
                </span>
                <div className="flex items-center gap-2">
                  <Select onValueChange={(status) => {
                    router.post(route('admin.users.bulk-status'), {
                      user_ids: selectedUsers,
                      status: status
                    }, {
                      onSuccess: () => {
                        setSelectedUsers([]);
                        toast.success('User statuses updated successfully');
                      },
                      onError: () => {
                        toast.error('Failed to update user statuses');
                      },
                    });
                  }}>
                    <SelectTrigger className="w-40">
                      <SelectValue placeholder="Change status" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="active">Set Active</SelectItem>
                      <SelectItem value="inactive">Set Inactive</SelectItem>
                      <SelectItem value="suspended">Set Suspended</SelectItem>
                      <SelectItem value="pending">Set Pending</SelectItem>
                    </SelectContent>
                  </Select>
                  <ConfirmDialog
                    title="Delete Selected Users"
                    description={`Are you sure you want to delete ${selectedUsers.length} user${selectedUsers.length > 1 ? 's' : ''}? This action cannot be undone.`}
                    confirmText="Delete"
                    variant="destructive"
                    onConfirm={() => {
                      router.delete(route('admin.users.bulk-delete'), {
                        data: { user_ids: selectedUsers },
                        onSuccess: () => {
                          setSelectedUsers([]);
                          toast.success('Users deleted successfully');
                        },
                        onError: () => {
                          toast.error('Failed to delete users');
                        },
                      });
                    }}
                  >
                    <Button variant="destructive" size="sm">
                      <Trash2 className="mr-2 h-4 w-4" />
                      Delete Selected
                    </Button>
                  </ConfirmDialog>
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setSelectedUsers([])}
                  >
                    Clear Selection
                  </Button>
                </div>
              </div>
            </CardContent>
          </Card>
        )}

        {/* Users list */}
        {users.data.length === 0 ? (
          <Card>
            <CardContent className="flex flex-col items-center justify-center py-8">
              <Shield className="text-muted-foreground mb-4 h-12 w-12" />
              <h3 className="mb-1 text-lg font-semibold">No Users Found</h3>
              <p className="text-muted-foreground">Try adjusting your search terms.</p>
            </CardContent>
          </Card>
        ) : (
          <Card>
            <CardContent className="p-0">
              <div className="overflow-x-auto">
                <Table className="min-w-full">
                  <TableHeader>
                    <TableRow>
                      <TableHead className="w-12">
                        <input
                          type="checkbox"
                          checked={selectedUsers.length === users.data.length}
                          onChange={toggleAllUsers}
                          className="rounded border-gray-300"
                        />
                      </TableHead>
                      <TableHead>Name</TableHead>
                      <TableHead>Email</TableHead>
                      <TableHead>Role</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead>Last Login</TableHead>
                      <TableHead>Joined</TableHead>
                      <TableHead className="text-right">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {users.data.map((user) => (
                      <TableRow key={user.id}>
                        <TableCell>
                          <input
                            type="checkbox"
                            checked={selectedUsers.includes(user.id)}
                            onChange={() => toggleUserSelection(user.id)}
                            className="rounded border-gray-300"
                          />
                        </TableCell>
                        <TableCell className="font-medium">
                          <div className="flex items-center gap-3">
                            <Avatar className="h-8 w-8">
                              <AvatarImage src={user.avatar_url} alt={user.name} />
                              <AvatarFallback className="text-xs">
                                {user.name.split(' ').map(n => n[0]).join('')}
                              </AvatarFallback>
                            </Avatar>
                            <Link
                              href={route('admin.users.show', user.id)}
                              className="text-blue-600 hover:text-blue-800 hover:underline"
                            >
                              {user.name}
                            </Link>
                          </div>
                        </TableCell>
                        <TableCell className="text-muted-foreground">{user.email}</TableCell>
                        <TableCell>
                          <Badge variant={user.role === 'admin' ? 'secondary' : 'outline'} className="text-xs">
                            {user.role === 'admin' ? 'Admin' : 'Student'}
                          </Badge>
                        </TableCell>
                        <TableCell>
                          <Badge variant={user.status_badge_color as any} className="text-xs">
                            {user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                          </Badge>
                        </TableCell>
                        <TableCell className="text-muted-foreground text-sm">
                          {user.last_login_at || 'Never'}
                        </TableCell>
                        <TableCell className="text-muted-foreground text-sm">
                          {user.created_at}
                        </TableCell>
                        <TableCell className="text-right">
                          <div className="flex items-center justify-end gap-2">
                            <Button
                              size="sm"
                              variant="outline"
                              onClick={() => { setEditingUser(user); setSheetOpen(true); }}
                            >
                              Quick Edit
                            </Button>
                            <DropdownMenu>
                              <DropdownMenuTrigger asChild>
                                <Button variant="ghost" size="sm">
                                  <MoreHorizontal className="h-4 w-4" />
                                </Button>
                              </DropdownMenuTrigger>
                              <DropdownMenuContent align="end">
                                <DropdownMenuItem asChild>
                                  <Link href={route('admin.users.edit', user.id)}>
                                    Edit User
                                  </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem onClick={() => updateUserStatus(user.id, 'active')}>
                                  Set Active
                                </DropdownMenuItem>
                                <DropdownMenuItem onClick={() => updateUserStatus(user.id, 'inactive')}>
                                  Set Inactive
                                </DropdownMenuItem>
                                <DropdownMenuItem onClick={() => updateUserStatus(user.id, 'suspended')}>
                                  Suspend
                                </DropdownMenuItem>
                                <DropdownMenuItem onClick={() => {
                                  router.post(route('admin.users.send-password-reset', user.id), {}, {
                                    onSuccess: () => toast.success('Password reset email sent'),
                                    onError: () => toast.error('Failed to send password reset email'),
                                  });
                                }}>
                                  Send Password Reset
                                </DropdownMenuItem>
                                <DropdownMenuItem onClick={() => {
                                  router.post(route('admin.users.force-password-change', user.id), {}, {
                                    onSuccess: () => toast.success('User will be required to change password'),
                                    onError: () => toast.error('Failed to force password change'),
                                  });
                                }}>
                                  Force Password Change
                                </DropdownMenuItem>
                                <DropdownMenuItem className="text-destructive">
                                  <ConfirmDialog
                                    title="Delete User"
                                    description={`Are you sure you want to delete ${user.name}? This action cannot be undone.`}
                                    confirmText="Delete"
                                    variant="destructive"
                                    onConfirm={() => deleteUser(user.id)}
                                  >
                                    <span className="w-full text-left">Delete User</span>
                                  </ConfirmDialog>
                                </DropdownMenuItem>
                              </DropdownMenuContent>
                            </DropdownMenu>
                          </div>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </div>
            </CardContent>
          </Card>
        )}

        {/* Pagination */}
        {users.last_page > 1 && (
          <div className="mt-6 flex items-center justify-between">
            <div className="text-muted-foreground text-sm">
              Showing {users.data.length} of {users.total} users
            </div>
            <div className="flex gap-2">
              {users.current_page > 1 && (
                <Button asChild variant="outline" size="sm">
                  <Link href={route('admin.users.index', { page: users.current_page - 1, ...filters })}>Previous</Link>
                </Button>
              )}
              {users.current_page < users.last_page && (
                <Button asChild variant="outline" size="sm">
                  <Link href={route('admin.users.index', { page: users.current_page + 1, ...filters })}>Next</Link>
                </Button>
              )}
            </div>
          </div>
        )}
      </div>

      {/* User Form Sheet */}
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent side="right" className="p-0 w-full sm:max-w-lg">
          <UserForm close={() => setSheetOpen(false)} user={editingUser} />
        </SheetContent>
      </Sheet>

      {/* Import Dialog */}
      <Dialog open={showImportDialog} onOpenChange={setShowImportDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Import Users</DialogTitle>
            <DialogDescription>
              Upload a CSV file to import users. The file should have columns: name, email, role, status, password.
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            <div>
              <label className="text-sm font-medium mb-2 block">CSV File</label>
              <Input
                type="file"
                accept=".csv,.txt"
                onChange={(e) => setImportFile(e.target.files?.[0] || null)}
              />
            </div>
            <div className="text-sm text-muted-foreground">
              <p>CSV format example:</p>
              <code className="block mt-1 p-2 bg-muted rounded text-xs">
                name,email,role,status,password<br />
                John Doe,john@example.com,student,active,password123<br />
                Jane Smith,jane@example.com,admin,active,password123
              </code>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setShowImportDialog(false)}>
              Cancel
            </Button>
            <Button onClick={handleImport} disabled={!importFile}>
              Import Users
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </AppLayout>
  );
}
