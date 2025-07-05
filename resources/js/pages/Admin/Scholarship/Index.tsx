import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, ScholarshipProgram } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';
import { cn } from '@/lib/utils';
import { buttonVariants } from '@/components/ui/button';

interface ScholarshipIndexProps {
  scholarships: (ScholarshipProgram & { scholarship_applications_count: number })[];
}

export default function Index({ scholarships }: ScholarshipIndexProps) {
  const [deleteDialogOpen, setDeleteDialogOpen] = useState<number | null>(null);

  const formatCurrency = (amount: number) => {
    return amount.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
  };

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin Dashboard', href: route('admin.dashboard') },
    { title: 'Scholarship Programs' }
  ];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Scholarship Programs" />
      
      <div className="p-4">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold">Scholarship Programs</h1>
          <Button asChild>
            <Link href={route('admin.scholarships.create')}>Create New Program</Link>
          </Button>
        </div>
        
        {scholarships.length === 0 ? (
          <Card>
            <CardHeader>
              <CardTitle>No Scholarships Found</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-muted-foreground">Get started by creating your first scholarship program.</p>
            </CardContent>
          </Card>
        ) : (
          <div className="border rounded-lg overflow-hidden">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b bg-muted/50">
                  <th className="py-3 px-4 text-left font-medium">Name</th>
                  <th className="py-3 px-4 text-left font-medium">Budget</th>
                  <th className="py-3 px-4 text-left font-medium">Eligibility</th>
                  <th className="py-3 px-4 text-left font-medium">Deadline</th>
                  <th className="py-3 px-4 text-left font-medium">Applications</th>
                  <th className="py-3 px-4 text-left font-medium">Status</th>
                  <th className="py-3 px-4 text-left font-medium">Actions</th>
                </tr>
              </thead>
              <tbody>
                {scholarships.map((scholarship) => (
                  <tr key={scholarship.id} className="border-b hover:bg-muted/50">
                    <td className="py-3 px-4">
                      <div className="font-medium">{scholarship.name}</div>
                      <div className="text-xs text-muted-foreground">{scholarship.semester} | {scholarship.academic_year}</div>
                    </td>
                    <td className="py-3 px-4">
                      <div>{formatCurrency(scholarship.per_student_budget)} / student</div>
                      <div className="text-xs text-muted-foreground">Total: {formatCurrency(scholarship.total_budget)}</div>
                    </td>
                    <td className="py-3 px-4">
                      <Badge variant="outline">
                        {scholarship.school_type_eligibility === 'both' ? 'All Students' : 
                         scholarship.school_type_eligibility === 'high_school' ? 'High School' : 'College'}
                      </Badge>
                    </td>
                    <td className="py-3 px-4">
                      {new Date(scholarship.application_deadline).toLocaleDateString()}
                    </td>
                    <td className="py-3 px-4">
                      {scholarship.scholarship_applications_count}
                    </td>
                    <td className="py-3 px-4">
                      <Badge variant={scholarship.active ? 'success' : 'secondary'}>
                        {scholarship.active ? 'Active' : 'Inactive'}
                      </Badge>
                    </td>
                    <td className="py-3 px-4">
                      <div className="flex gap-2">
                        <Button asChild size="sm" variant="outline">
                          <Link href={route('admin.scholarships.show', scholarship.id)}>View</Link>
                        </Button>
                        <Button asChild size="sm" variant="outline">
                          <Link href={route('admin.scholarships.edit', scholarship.id)}>Edit</Link>
                        </Button>
                        <AlertDialog open={deleteDialogOpen === scholarship.id} onOpenChange={(open) => setDeleteDialogOpen(open ? scholarship.id : null)}>
                          <AlertDialogTrigger asChild>
                            <Button size="sm" variant="destructive">
                              <Trash2 className="h-3 w-3" />
                            </Button>
                          </AlertDialogTrigger>
                          <AlertDialogContent>
                            <AlertDialogHeader>
                              <AlertDialogTitle>Delete "{scholarship.name}"?</AlertDialogTitle>
                              <AlertDialogDescription>
                                This will permanently delete this scholarship program and ALL related data including:
                                <br />• {scholarship.scholarship_applications_count} applications
                                <br />• All document uploads and files
                                <br />• All community service reports
                                <br />• All disbursement records
                                <br /><br />
                                This action cannot be undone.
                              </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                              <AlertDialogCancel>Cancel</AlertDialogCancel>
                              <AlertDialogAction asChild>
                                <Link
                                  href={route('admin.scholarships.destroy', scholarship.id)}
                                  method="delete"
                                  as="button"
                                  className={cn(buttonVariants({ variant: 'destructive' }))}
                                  onSuccess={() => setDeleteDialogOpen(null)}
                                >
                                  Delete
                                </Link>
                              </AlertDialogAction>
                            </AlertDialogFooter>
                          </AlertDialogContent>
                        </AlertDialog>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </AppLayout>
  );
}