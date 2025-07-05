import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, ScholarshipApplication, ScholarshipProgram, DocumentUpload, DocumentRequirement } from '@/types';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { route } from 'ziggy-js';
import MissingDocumentsOnboardingModal from './components/MissingDocumentsOnboardingModal';
import ApplicationOverview from './components/ApplicationOverview';
import DocumentsTab from './components/DocumentsTab';
import TimelineView from './components/TimelineView';

interface DocumentUploadItem {
    requirement: DocumentRequirement;
    upload: DocumentUpload | null;
}

interface ApplicationShowProps {
    application: ScholarshipApplication;
    scholarship: ScholarshipProgram;
    documentUploads: DocumentUploadItem[];
    canSubmit: boolean;
}

export default function Show({
    application,
    scholarship,
    documentUploads,
    canSubmit
}: ApplicationShowProps) {
    const [activeTab, setActiveTab] = useState('overview');
    const [showMissingDocsModal, setShowMissingDocsModal] = useState(false);
    const [startHighlightingUploads, setStartHighlightingUploads] = useState(false);

    const calculateProgress = () => {
        const statuses = [
            'draft', 'submitted', 'documents_pending', 'documents_under_review', 'documents_approved',
            'eligibility_verified', 'enrolled', 'service_pending', 'service_completed',
            'disbursement_pending', 'disbursement_processed', 'completed'
        ];
        const index = statuses.indexOf(application.status);
        return index === -1 ? 0 : Math.round((index / (statuses.length - 1)) * 100);
    };

    const progressPercentage = calculateProgress();

    // Check for missing documents and show onboarding modal
    useEffect(() => {
        if (application.status === 'draft') {
            const missingDocs = documentUploads.filter(doc => !doc.upload);
            if (missingDocs.length > 0) {
                setShowMissingDocsModal(true);
            }
        }
    }, [application.status, documentUploads]);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Student Dashboard', href: route('student.dashboard') },
        { title: 'My Applications', href: route('student.applications.index') },
        { title: 'Application Details' }
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Application #${application.id} - ${scholarship.name}`} />

            <div className="max-w-7xl mx-auto p-4 lg:p-6 space-y-6">
                <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
                    <TabsList className="grid w-full grid-cols-3">
                        <TabsTrigger value="overview">Overview</TabsTrigger>
                        <TabsTrigger value="documents">Documents</TabsTrigger>
                        <TabsTrigger value="timeline">Timeline</TabsTrigger>
                    </TabsList>

                    <TabsContent value="overview" className="space-y-6">
                        <ApplicationOverview
                            application={application}
                            scholarship={scholarship}
                            progressPercentage={progressPercentage}
                        />
                    </TabsContent>

                    <TabsContent value="documents" className="space-y-6">
                        <DocumentsTab
                            application={application}
                            documentUploads={documentUploads}
                            canSubmit={canSubmit}
                            startHighlightingUploads={startHighlightingUploads}
                            setStartHighlightingUploads={setStartHighlightingUploads}
                        />
                    </TabsContent>

                    <TabsContent value="timeline" className="space-y-6">
                        <TimelineView application={application} />
                    </TabsContent>
                </Tabs>
            </div>

            <MissingDocumentsOnboardingModal
                isOpen={showMissingDocsModal}
                onClose={() => setShowMissingDocsModal(false)}
                documents={documentUploads}
                onGoToDocuments={() => {
                    setShowMissingDocsModal(false);
                    setActiveTab('documents');
                    setStartHighlightingUploads(true);
                }}
                applicationStatus={application.status}
            />
        </AppLayout>
    );
}