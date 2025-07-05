import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Checkbox } from '@/components/ui/checkbox';
import { 
    AlertTriangle, 
    XCircle, 
    Clock, 
    FileX, 
    Ban,
    Loader2
} from 'lucide-react';
import { ScholarshipApplication } from '@/types';

interface CancelApplicationDialogProps {
    isOpen: boolean;
    onClose: () => void;
    application: ScholarshipApplication;
}

export default function CancelApplicationDialog({ 
    isOpen, 
    onClose, 
    application 
}: CancelApplicationDialogProps) {
    const [confirmationChecked, setConfirmationChecked] = useState(false);
    
    const { post, processing } = useForm();

    const handleCancel = () => {
        if (!confirmationChecked) return;
        
        post(route('student.applications.cancel', application.id), {
            onSuccess: () => {
                onClose();
            },
            onError: () => {
                // Error handling is done by the backend redirect
            }
        });
    };

    const getStatusDisplayName = (status: string) => {
        const statusMap: Record<string, string> = {
            'draft': 'Draft',
            'submitted': 'Submitted',
            'documents_pending': 'Documents Pending',
            'documents_under_review': 'Under Review'
        };
        return statusMap[status] || status;
    };

    const consequences = [
        {
            icon: XCircle,
            title: 'Application Permanently Cancelled',
            description: 'Your application will be marked as cancelled and cannot be reactivated.',
            severity: 'high'
        },
        {
            icon: FileX,
            title: 'Document Uploads Lost',
            description: 'All uploaded documents will be removed and you\'ll need to re-upload them if you apply again.',
            severity: 'high'
        },
        {
            icon: Clock,
            title: 'Review Progress Lost',
            description: 'Any review progress made by our team will be lost and the process will start over.',
            severity: 'medium'
        },
        {
            icon: Ban,
            title: 'New Application Required',
            description: 'To apply for this scholarship again, you\'ll need to start a completely new application.',
            severity: 'medium'
        }
    ];

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className="sm:max-w-lg max-w-[95vw] mx-auto">
                <DialogHeader className="space-y-4">
                    <div className="flex items-center gap-3">
                        <div className="p-2 bg-red-100 dark:bg-red-900/20 rounded-full">
                            <AlertTriangle className="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div>
                            <DialogTitle className="text-xl font-semibold text-red-900 dark:text-red-100">
                                Cancel Application
                            </DialogTitle>
                            <DialogDescription className="text-red-700 dark:text-red-300">
                                Are you sure you want to cancel this scholarship application?
                            </DialogDescription>
                        </div>
                    </div>
                </DialogHeader>

                <div className="space-y-6">
                    {/* Current Application Status */}
                    <Alert className="border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20">
                        <Clock className="h-4 w-4 text-amber-600 dark:text-amber-400" />
                        <AlertDescription className="text-amber-800 dark:text-amber-200">
                            <span className="font-medium">Current Status:</span> {getStatusDisplayName(application.status)}
                            <br />
                            <span className="text-sm">
                                Application ID: #{application.id} • {application.scholarshipProgram?.name}
                            </span>
                        </AlertDescription>
                    </Alert>

                    {/* Consequences */}
                    <div className="space-y-4">
                        <h4 className="font-medium text-gray-900 dark:text-gray-100">
                            What happens when you cancel:
                        </h4>
                        
                        <div className="space-y-3">
                            {consequences.map((consequence, index) => {
                                const IconComponent = consequence.icon;
                                return (
                                    <div 
                                        key={index}
                                        className={`flex gap-3 p-3 rounded-lg border ${
                                            consequence.severity === 'high' 
                                                ? 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/10' 
                                                : 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/10'
                                        }`}
                                    >
                                        <div className={`p-1.5 rounded-full ${
                                            consequence.severity === 'high' 
                                                ? 'bg-red-100 dark:bg-red-900/30' 
                                                : 'bg-amber-100 dark:bg-amber-900/30'
                                        }`}>
                                            <IconComponent className={`h-4 w-4 ${
                                                consequence.severity === 'high' 
                                                    ? 'text-red-600 dark:text-red-400' 
                                                    : 'text-amber-600 dark:text-amber-400'
                                            }`} />
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <h5 className={`font-medium text-sm ${
                                                consequence.severity === 'high' 
                                                    ? 'text-red-900 dark:text-red-100' 
                                                    : 'text-amber-900 dark:text-amber-100'
                                            }`}>
                                                {consequence.title}
                                            </h5>
                                            <p className={`text-xs mt-1 ${
                                                consequence.severity === 'high' 
                                                    ? 'text-red-700 dark:text-red-300' 
                                                    : 'text-amber-700 dark:text-amber-300'
                                            }`}>
                                                {consequence.description}
                                            </p>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* Confirmation Checkbox */}
                    <div className="space-y-4">
                        <div className="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border">
                            <Checkbox
                                id="confirm-cancellation"
                                checked={confirmationChecked}
                                onCheckedChange={(checked) => setConfirmationChecked(checked as boolean)}
                                className="mt-0.5"
                            />
                            <label 
                                htmlFor="confirm-cancellation" 
                                className="text-sm text-gray-700 dark:text-gray-300 leading-relaxed cursor-pointer"
                            >
                                I understand that cancelling this application is permanent and cannot be undone. 
                                I will need to start a new application if I want to apply for this scholarship again.
                            </label>
                        </div>
                    </div>

                    {/* Alternative Actions */}
                    <Alert className="border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20">
                        <AlertDescription className="text-blue-800 dark:text-blue-200">
                            <span className="font-medium">Need help instead?</span>
                            <br />
                            <span className="text-sm">
                                If you're having trouble with your application, consider contacting our support team 
                                instead of cancelling. We're here to help you succeed!
                            </span>
                        </AlertDescription>
                    </Alert>
                </div>

                <DialogFooter className="flex flex-col-reverse sm:flex-row gap-2 sm:gap-0">
                    <Button
                        variant="outline"
                        onClick={onClose}
                        disabled={processing}
                        className="w-full sm:w-auto"
                    >
                        Keep Application
                    </Button>
                    <Button
                        variant="destructive"
                        onClick={handleCancel}
                        disabled={!confirmationChecked || processing}
                        className="w-full sm:w-auto"
                    >
                        {processing ? (
                            <>
                                <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                                Cancelling...
                            </>
                        ) : (
                            <>
                                <XCircle className="h-4 w-4 mr-2" />
                                Cancel Application
                            </>
                        )}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
