import React, { useState } from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { CheckCircle2, Send } from 'lucide-react';
import { DocumentRequirement, DocumentUpload, ScholarshipApplication } from '@/types';
import DocumentCard from './DocumentCard';
import DocumentUploadDialog from './DocumentUploadDialog';
import ApprovedDocumentWarningDialog from './ApprovedDocumentWarningDialog';
import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { toast } from 'sonner';

interface DocumentUploadItem {
    requirement: DocumentRequirement;
    upload: DocumentUpload | null;
}

interface DocumentsTabProps {
    application: ScholarshipApplication;
    documentUploads: DocumentUploadItem[];
    canSubmit: boolean;
    startHighlightingUploads: boolean;
    setStartHighlightingUploads: (value: boolean) => void;
}

export default function DocumentsTab({ 
    application, 
    documentUploads, 
    canSubmit,
    startHighlightingUploads,
    setStartHighlightingUploads
}: DocumentsTabProps) {
    const [showUploadDialog, setShowUploadDialog] = useState(false);
    const [selectedRequirement, setSelectedRequirement] = useState<DocumentRequirement | null>(null);
    const [showApprovedUpdateWarning, setShowApprovedUpdateWarning] = useState(false);
    const [pendingRequirement, setPendingRequirement] = useState<DocumentRequirement | null>(null);

    const openUploadDialog = (requirement: DocumentRequirement) => {
        setSelectedRequirement(requirement);
        setShowUploadDialog(true);
    };

    const handleUploadClick = (requirement: DocumentRequirement) => {
        const upload = documentUploads.find(doc => doc.requirement.id === requirement.id)?.upload;
        
        // If document is approved, show warning first
        if (upload && upload.status === 'approved') {
            setPendingRequirement(requirement);
            setShowApprovedUpdateWarning(true);
        } else {
            openUploadDialog(requirement);
        }
    };

    const confirmApprovedUpdate = () => {
        if (pendingRequirement) {
            setShowApprovedUpdateWarning(false);
            openUploadDialog(pendingRequirement);
            setPendingRequirement(null);
        }
    };

    const cancelApprovedUpdate = () => {
        setShowApprovedUpdateWarning(false);
        setPendingRequirement(null);
    };

    const submitApplication = () => {
        router.post(route('student.applications.submit', application.id), {}, {
            onSuccess: () => {
                toast.success('Application submitted successfully!');
            },
            onError: () => {
                toast.error('Failed to submit application. Please try again.');
            }
        });
    };

    const canUploadDocument = (upload: DocumentUpload | null) => {
        return (
            application.status === 'draft' || 
            (application.status === 'documents_rejected' && upload?.status.startsWith('rejected_')) ||
            upload?.status === 'approved' ||
            upload?.status === 'pending_review' ||
            !upload // No upload yet
        );
    };

    return (
        <div className="space-y-6">
            {/* Progress Overview */}
            <Card>
                <CardContent className="p-6">
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <h3 className="text-lg font-semibold">Document Upload Progress</h3>
                            <span className="text-sm text-muted-foreground">
                                {documentUploads.filter(doc => doc.upload).length} of {documentUploads.length} uploaded
                            </span>
                        </div>
                        
                        <div className="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                className="bg-primary h-2 rounded-full transition-all duration-300" 
                                style={{ 
                                    width: `${(documentUploads.filter(doc => doc.upload).length / documentUploads.length) * 100}%` 
                                }}
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Documents Grid */}
            <div className="grid gap-4">
                {documentUploads.map(({ requirement, upload }) => (
                    <DocumentCard
                        key={requirement.id}
                        requirement={requirement}
                        upload={upload}
                        onUploadClick={handleUploadClick}
                        canUpload={canUploadDocument(upload)}
                        isHighlighted={startHighlightingUploads && (!upload || upload.status.startsWith('rejected_'))}
                    />
                ))}
            </div>

            {/* Submit Application */}
            {canSubmit && (
                <Card className="border-2 border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20">
                    <CardContent className="p-6">
                        <div className="text-center space-y-4">
                            <div className="flex justify-center">
                                <div className="p-4 bg-green-100 dark:bg-green-900/50 rounded-full">
                                    <CheckCircle2 className="h-8 w-8 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div>
                                <h3 className="text-xl font-semibold text-green-700 dark:text-green-300 mb-2">
                                    Ready to Submit!
                                </h3>
                                <p className="text-green-600 dark:text-green-400 mb-4">
                                    All required documents have been uploaded. Review everything once more, then submit your application.
                                </p>
                            </div>
                            <Button 
                                onClick={submitApplication}
                                size="lg"
                                className="bg-green-600 hover:bg-green-700"
                            >
                                <Send className="h-5 w-5 mr-2" />
                                Submit Application
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Dialogs */}
            <ApprovedDocumentWarningDialog
                open={showApprovedUpdateWarning}
                onOpenChange={setShowApprovedUpdateWarning}
                onConfirm={confirmApprovedUpdate}
                onCancel={cancelApprovedUpdate}
            />

            {selectedRequirement && (
                <DocumentUploadDialog
                    open={showUploadDialog}
                    onOpenChange={setShowUploadDialog}
                    requirement={selectedRequirement}
                    applicationId={application.id}
                    existingUpload={documentUploads.find(doc => 
                        doc.requirement.id === selectedRequirement.id
                    )?.upload || null}
                />
            )}
        </div>
    );
}
