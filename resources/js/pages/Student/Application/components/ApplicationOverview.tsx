import React, { useState } from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { Button } from '@/components/ui/button';
import {
    CheckCircle2,
    XCircle,
    Clock,
    File,
    Calendar,
    MapPin,
    Users,
    GraduationCap,
    DollarSign,
    Timer,
    Ban
} from 'lucide-react';
import { ScholarshipApplication, ScholarshipProgram } from '@/types';
import CancelApplicationDialog from './CancelApplicationDialog';

interface ApplicationOverviewProps {
    application: ScholarshipApplication;
    scholarship: ScholarshipProgram;
    progressPercentage: number;
}

export default function ApplicationOverview({
    application,
    scholarship,
    progressPercentage
}: ApplicationOverviewProps) {
    const [showCancelDialog, setShowCancelDialog] = useState(false);
    const formatStatus = (status: string) => {
        return status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    };

    const canCancelApplication = (status: string) => {
        const cancellableStatuses = ['draft', 'submitted', 'documents_pending', 'documents_under_review'];
        return cancellableStatuses.includes(status);
    };
    
    const getStatusInfo = (status: string) => {
        if (['completed', 'disbursement_processed', 'service_completed', 'documents_approved', 'eligibility_verified', 'approved'].includes(status)) {
            return { color: 'green', bgColor: 'bg-green-500', icon: CheckCircle2, variant: 'default' as const };
        }
        if (status.startsWith('rejected_') || status === 'rejected' || status === 'cancelled') {
            return { color: 'red', bgColor: 'bg-red-500', icon: XCircle, variant: 'destructive' as const };
        }
        if (['disbursement_pending', 'service_pending', 'documents_under_review', 'pending_review'].includes(status)) {
            return { color: 'blue', bgColor: 'bg-blue-500', icon: Clock, variant: 'secondary' as const };
        }
        return { color: 'gray', bgColor: 'bg-gray-500', icon: File, variant: 'outline' as const };
    };

    const getStatusDescription = (status: string) => {
        const descriptions = {
            'draft': 'Your application is being prepared. Upload required documents to proceed.',
            'submitted': 'Application submitted and awaiting initial review.',
            'documents_pending': 'Please upload all required documents.',
            'documents_under_review': 'Your documents are being reviewed by our team.',
            'documents_approved': 'Documents approved! Eligibility verification in progress.',
            'eligibility_verified': 'Eligibility confirmed. Enrollment process initiated.',
            'enrolled': 'Congratulations! You are enrolled in the scholarship program.',
            'service_pending': 'Community service requirements are pending.',
            'service_completed': 'Community service completed. Disbursement processing.',
            'disbursement_pending': 'Scholarship disbursement is being processed.',
            'disbursement_processed': 'Scholarship funds have been disbursed.',
            'completed': 'Scholarship program completed successfully!',
            'documents_rejected': 'Some documents need revision. Please check and re-upload.',
            'rejected': 'Application not approved at this time.',
            'cancelled': 'This application has been cancelled and is no longer active.'
        };
        return descriptions[status as keyof typeof descriptions] || 'Application is being processed.';
    };

    const statusInfo = getStatusInfo(application.status);
    const StatusIcon = statusInfo.icon;

    return (
        <div className="space-y-6">
            {/* Application Status Card */}
            <Card>
                <CardContent className="p-6">
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <h3 className="text-lg font-semibold">Application Status</h3>
                            <Badge variant={statusInfo.variant} className="flex items-center gap-1">
                                <StatusIcon className="h-3 w-3" />
                                {formatStatus(application.status)}
                            </Badge>
                        </div>
                        
                        <div className="space-y-2">
                            <div className="flex justify-between text-sm">
                                <span>Progress</span>
                                <span>{progressPercentage}%</span>
                            </div>
                            <Progress value={progressPercentage} className="h-2" />
                        </div>
                        
                        <p className="text-sm text-muted-foreground">
                            {getStatusDescription(application.status)}
                        </p>

                        {/* Cancel Application Button */}
                        {canCancelApplication(application.status) && (
                            <div className="pt-4 border-t">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => setShowCancelDialog(true)}
                                    className="w-full sm:w-auto text-red-600 border-red-200 hover:bg-red-50 hover:border-red-300 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-900/20"
                                >
                                    <Ban className="h-4 w-4 mr-2" />
                                    Cancel Application
                                </Button>
                                <p className="text-xs text-muted-foreground mt-2">
                                    You can cancel this application while it's in early stages
                                </p>
                            </div>
                        )}
                    </div>
                </CardContent>
            </Card>

            {/* Scholarship Details */}
            <Card>
                <CardContent className="p-6">
                    <div className="space-y-4">
                        <h3 className="text-lg font-semibold">Scholarship Details</h3>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div className="space-y-3">
                                <div className="flex items-center gap-2">
                                    <GraduationCap className="h-4 w-4 text-muted-foreground" />
                                    <span className="text-sm font-medium">Program</span>
                                </div>
                                <p className="text-sm text-muted-foreground ml-6">
                                    {scholarship.name}
                                </p>
                            </div>
                            
                            <div className="space-y-3">
                                <div className="flex items-center gap-2">
                                    <DollarSign className="h-4 w-4 text-muted-foreground" />
                                    <span className="text-sm font-medium">Amount</span>
                                </div>
                                <p className="text-sm text-muted-foreground ml-6">
                                    ₱{scholarship.per_student_budget?.toLocaleString()}
                                </p>
                            </div>
                            
                            <div className="space-y-3">
                                <div className="flex items-center gap-2">
                                    <Calendar className="h-4 w-4 text-muted-foreground" />
                                    <span className="text-sm font-medium">Academic Year</span>
                                </div>
                                <p className="text-sm text-muted-foreground ml-6">
                                    {scholarship.academic_year} - {scholarship.semester}
                                </p>
                            </div>
                            
                            <div className="space-y-3">
                                <div className="flex items-center gap-2">
                                    <Users className="h-4 w-4 text-muted-foreground" />
                                    <span className="text-sm font-medium">Available Slots</span>
                                </div>
                                <p className="text-sm text-muted-foreground ml-6">
                                    {scholarship.available_slots} slots
                                </p>
                            </div>
                            
                            {scholarship.community_service_days && (
                                <div className="space-y-3">
                                    <div className="flex items-center gap-2">
                                        <Timer className="h-4 w-4 text-muted-foreground" />
                                        <span className="text-sm font-medium">Community Service</span>
                                    </div>
                                    <p className="text-sm text-muted-foreground ml-6">
                                        {scholarship.community_service_days} days required
                                    </p>
                                </div>
                            )}
                            
                            <div className="space-y-3">
                                <div className="flex items-center gap-2">
                                    <Calendar className="h-4 w-4 text-muted-foreground" />
                                    <span className="text-sm font-medium">Application Deadline</span>
                                </div>
                                <p className="text-sm text-muted-foreground ml-6">
                                    {new Date(scholarship.application_deadline).toLocaleDateString()}
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Application Timeline */}
            <Card>
                <CardContent className="p-6">
                    <div className="space-y-4">
                        <h3 className="text-lg font-semibold">Application Timeline</h3>
                        
                        <div className="space-y-3">
                            <div className="flex items-center gap-3">
                                <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                                <div className="flex-1">
                                    <p className="text-sm font-medium">Application Created</p>
                                    <p className="text-xs text-muted-foreground">
                                        {new Date(application.created_at).toLocaleDateString()}
                                    </p>
                                </div>
                            </div>
                            
                            {application.submitted_at && (
                                <div className="flex items-center gap-3">
                                    <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <div className="flex-1">
                                        <p className="text-sm font-medium">Application Submitted</p>
                                        <p className="text-xs text-muted-foreground">
                                            {new Date(application.submitted_at).toLocaleDateString()}
                                        </p>
                                    </div>
                                </div>
                            )}
                            
                            {application.reviewed_at && (
                                <div className="flex items-center gap-3">
                                    <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <div className="flex-1">
                                        <p className="text-sm font-medium">Application Reviewed</p>
                                        <p className="text-xs text-muted-foreground">
                                            {new Date(application.reviewed_at).toLocaleDateString()}
                                        </p>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Cancel Application Dialog */}
            <CancelApplicationDialog
                isOpen={showCancelDialog}
                onClose={() => setShowCancelDialog(false)}
                application={application}
            />
        </div>
    );
}
