import React from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { 
    CheckCircle2, 
    Clock, 
    XCircle, 
    FileText,
    Send,
    Eye,
    Award,
    Coins,
    Heart
} from 'lucide-react';
import { ScholarshipApplication } from '@/types';

interface TimelineViewProps {
    application: ScholarshipApplication;
}

export default function TimelineView({ application }: TimelineViewProps) {
    const timelineSteps = [
        {
            id: 'created',
            title: 'Application Created',
            description: 'Your scholarship application was initiated',
            icon: FileText,
        },
        {
            id: 'submitted',
            title: 'Application Submitted',
            description: 'All required documents uploaded and application submitted',
            icon: Send,
        },
        {
            id: 'under_review',
            title: 'Under Review',
            description: 'Application and documents are being reviewed',
            icon: Eye,
        },
        {
            id: 'approved',
            title: 'Application Approved',
            description: 'Congratulations! Your application has been approved',
            icon: Award,
        },
        {
            id: 'enrolled',
            title: 'Enrolled in Program',
            description: 'You are now enrolled in the scholarship program',
            icon: CheckCircle2,
        },
        {
            id: 'disbursement',
            title: 'Funds Disbursed',
            description: 'Scholarship funds have been processed and disbursed',
            icon: Coins,
        },
        {
            id: 'completed',
            title: 'Program Completed',
            description: 'Scholarship program completed successfully',
            icon: Heart,
        },
    ];

    const getStepStatus = (stepId: string) => {
        const currentStatus = application.status;
        
        switch (stepId) {
            case 'created':
                return 'completed';
            case 'submitted':
                if (application.submitted_at) return 'completed';
                if (currentStatus === 'draft') return 'current';
                return 'pending';
            case 'under_review':
                if (['documents_approved', 'eligibility_verified', 'enrolled', 'service_pending', 'service_completed', 'disbursement_pending', 'disbursement_processed', 'completed'].includes(currentStatus)) return 'completed';
                if (currentStatus === 'documents_under_review') return 'current';
                if (currentStatus.startsWith('rejected')) return 'failed';
                return 'pending';
            case 'approved':
                if (['eligibility_verified', 'enrolled', 'service_pending', 'service_completed', 'disbursement_pending', 'disbursement_processed', 'completed'].includes(currentStatus)) return 'completed';
                if (currentStatus === 'documents_approved') return 'current';
                if (currentStatus.startsWith('rejected')) return 'failed';
                return 'pending';
            case 'enrolled':
                if (['enrolled', 'service_pending', 'service_completed', 'disbursement_pending', 'disbursement_processed', 'completed'].includes(currentStatus)) return 'completed';
                if (currentStatus === 'eligibility_verified') return 'current';
                return 'pending';
            case 'disbursement':
                if (['disbursement_processed', 'completed'].includes(currentStatus)) return 'completed';
                if (currentStatus === 'disbursement_pending') return 'current';
                return 'pending';
            case 'completed':
                if (currentStatus === 'completed') return 'completed';
                return 'pending';
            default:
                return 'pending';
        }
    };

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'completed':
                return 'text-green-600 bg-green-100 border-green-200';
            case 'current':
                return 'text-blue-600 bg-blue-100 border-blue-200';
            case 'failed':
                return 'text-red-600 bg-red-100 border-red-200';
            default:
                return 'text-gray-400 bg-gray-50 border-gray-200';
        }
    };

    const getStatusIcon = (status: string) => {
        switch (status) {
            case 'completed':
                return CheckCircle2;
            case 'current':
                return Clock;
            case 'failed':
                return XCircle;
            default:
                return Clock;
        }
    };

    return (
        <Card>
            <CardContent className="p-6">
                <div className="space-y-6">
                    <h3 className="text-lg font-semibold">Application Timeline</h3>
                    
                    <div className="space-y-6">
                        {timelineSteps.map((step, index) => {
                            const status = getStepStatus(step.id);
                            const StepIcon = step.icon;
                            const StatusIcon = getStatusIcon(status);
                            const isLast = index === timelineSteps.length - 1;

                            return (
                                <div key={step.id} className="relative flex gap-4 group">
                                    {/* Timeline Line */}
                                    {!isLast && (
                                        <div className="absolute left-6 top-12 w-0.5 h-6 bg-gray-200"></div>
                                    )}
                                    
                                    {/* Step Icon */}
                                    <div className={`relative z-10 flex items-center justify-center w-12 h-12 rounded-full border-2 ${getStatusColor(status)}`}>
                                        <StepIcon className="w-5 h-5" />
                                        
                                        {/* Status indicator */}
                                        <div className="absolute -top-1 -right-1">
                                            <StatusIcon className="w-4 h-4" />
                                        </div>
                                    </div>
                                    
                                    {/* Step Content */}
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2 mb-1">
                                            <h4 className="text-sm font-medium">{step.title}</h4>
                                            <Badge 
                                                variant={status === 'completed' ? 'default' : status === 'current' ? 'secondary' : 'outline'}
                                                className="text-xs"
                                            >
                                                {status === 'completed' ? 'Completed' : 
                                                 status === 'current' ? 'In Progress' : 
                                                 status === 'failed' ? 'Failed' : 'Pending'}
                                            </Badge>
                                        </div>
                                        <p className="text-sm text-muted-foreground">
                                            {step.description}
                                        </p>
                                        
                                        {/* Show dates for completed steps */}
                                        {status === 'completed' && (
                                            <p className="text-xs text-muted-foreground mt-1">
                                                {step.id === 'created' && application.created_at && 
                                                    `Created on ${new Date(application.created_at).toLocaleDateString()}`}
                                                {step.id === 'submitted' && application.submitted_at && 
                                                    `Submitted on ${new Date(application.submitted_at).toLocaleDateString()}`}
                                                {step.id === 'under_review' && application.reviewed_at && 
                                                    `Reviewed on ${new Date(application.reviewed_at).toLocaleDateString()}`}
                                            </p>
                                        )}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}
