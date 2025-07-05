import React, { useState } from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import {
    Upload,
    File,
    CheckCircle2,
    XCircle,
    Clock,
    AlertCircle,
    AlertTriangle,
    Image as ImageIcon,
    FileText,
    Eye,
    Download
} from 'lucide-react';
import { DocumentRequirement, DocumentUpload } from '@/types';
import { route } from 'ziggy-js';

interface DocumentCardProps {
    requirement: DocumentRequirement;
    upload: DocumentUpload | null;
    onUploadClick: (requirement: DocumentRequirement) => void;
    canUpload: boolean;
    isHighlighted?: boolean;
}

export default function DocumentCard({
    requirement,
    upload,
    onUploadClick,
    canUpload,
    isHighlighted = false
}: DocumentCardProps) {
    const [showPreview, setShowPreview] = useState(false);

    const formatStatus = (status: string) => {
        return status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    };

    const formatFileSize = (bytes: number) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    const isImageFile = (filename: string) => {
        const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp'];
        return imageExtensions.some(ext => filename.toLowerCase().endsWith(ext));
    };

    const getFileIcon = (filename: string) => {
        return isImageFile(filename) ? ImageIcon : FileText;
    };

    const getFilePreviewUrl = (upload: DocumentUpload) => {
        return route('student.applications.documents.view', {
            application: upload.scholarship_application_id,
            document: upload.id
        });
    };
    
    const getStatusInfo = (status: string) => {
        if (['completed', 'disbursement_processed', 'service_completed', 'documents_approved', 'eligibility_verified', 'approved'].includes(status)) {
            return { color: 'green', bgColor: 'bg-green-500', icon: CheckCircle2, variant: 'default' as const };
        }
        if (status.startsWith('rejected_') || status === 'rejected') {
            return { color: 'red', bgColor: 'bg-red-500', icon: XCircle, variant: 'destructive' as const };
        }
        if (['disbursement_pending', 'service_pending', 'documents_under_review', 'pending_review'].includes(status)) {
            return { color: 'blue', bgColor: 'bg-blue-500', icon: Clock, variant: 'secondary' as const };
        }
        return { color: 'gray', bgColor: 'bg-gray-500', icon: File, variant: 'outline' as const };
    };

    const docStatusInfo = upload ? getStatusInfo(upload.status) : { color: 'gray', icon: File, variant: 'outline' as const };
    const DocIcon = docStatusInfo.icon;

    return (
        <Card className={`transition-all duration-200 ${
            isHighlighted ? 'ring-2 ring-primary ring-offset-2 animate-pulse' : ''
        }`}>
            <CardContent className="p-4">
                <div className="space-y-3">
                    <div className="flex items-start justify-between">
                        <div className="flex-1">
                            <h4 className="font-medium text-sm">{requirement.name}</h4>
                            <p className="text-xs text-muted-foreground mt-1">
                                {requirement.description}
                            </p>
                        </div>
                        {requirement.is_required && (
                            <Badge variant="secondary" className="text-xs ml-2">
                                Required
                            </Badge>
                        )}
                    </div>

                    <div className="space-y-2">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-medium text-muted-foreground">Status</span>
                        </div>
                        
                        <div className="space-y-3">
                            {upload ? (
                                <div className="space-y-2">
                                    <div className="flex items-center gap-2">
                                        <DocIcon className={`h-4 w-4 ${
                                            docStatusInfo.color === 'green' ? 'text-green-600' :
                                            docStatusInfo.color === 'red' ? 'text-red-600' :
                                            docStatusInfo.color === 'blue' ? 'text-blue-600' :
                                            'text-gray-600'
                                        }`} />
                                        <Badge variant={docStatusInfo.variant}>
                                            {formatStatus(upload.status)}
                                        </Badge>
                                        <span className="text-xs text-muted-foreground">
                                            {new Date(upload.uploaded_at).toLocaleDateString()}
                                        </span>
                                    </div>
                                    
                                    <div className="space-y-2">
                                        {/* File Preview/Thumbnail */}
                                        {isImageFile(upload.original_filename) ? (
                                            <div className="flex items-start gap-3">
                                                <div className="flex-shrink-0">
                                                    <img
                                                        src={getFilePreviewUrl(upload)}
                                                        alt="Document preview"
                                                        className="w-16 h-16 object-cover rounded-lg border cursor-pointer hover:opacity-80 transition-opacity"
                                                        onClick={() => setShowPreview(true)}
                                                    />
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <div className="flex items-center gap-2 text-sm">
                                                        <ImageIcon className="h-4 w-4 text-blue-500" />
                                                        <span className="font-medium truncate">{upload.original_filename}</span>
                                                    </div>
                                                    <p className="text-xs text-muted-foreground mt-1">
                                                        Image • Click to preview
                                                    </p>
                                                </div>
                                            </div>
                                        ) : (
                                            <div className="flex items-center gap-3">
                                                <div className="w-16 h-16 bg-muted rounded-lg border flex items-center justify-center">
                                                    <FileText className="h-8 w-8 text-muted-foreground" />
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <div className="flex items-center gap-2 text-sm">
                                                        <FileText className="h-4 w-4 text-red-500" />
                                                        <span className="font-medium truncate">{upload.original_filename}</span>
                                                    </div>
                                                    <p className="text-xs text-muted-foreground mt-1">
                                                        PDF Document
                                                    </p>
                                                </div>
                                            </div>
                                        )}
                                    </div>

                                    {upload.rejection_reason && (
                                        <div className="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                            <div className="flex items-start gap-2">
                                                <AlertCircle className="h-4 w-4 text-red-600 dark:text-red-400 mt-0.5" />
                                                <div>
                                                    <p className="text-sm font-medium text-red-700 dark:text-red-300">
                                                        Rejection Reason
                                                    </p>
                                                    <p className="text-sm text-red-600 dark:text-red-400 mt-1">
                                                        {upload.rejection_reason}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                    <AlertTriangle className="h-4 w-4" />
                                    Not uploaded yet
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Action Buttons */}
                    {canUpload && (
                        <div className="flex gap-2">
                            {upload && (
                                <Button
                                    onClick={() => setShowPreview(true)}
                                    size="sm"
                                    variant="outline"
                                >
                                    <Eye className="h-4 w-4 mr-2" />
                                    View
                                </Button>
                            )}
                            <Button
                                onClick={() => onUploadClick(requirement)}
                                size="sm"
                                variant={upload ? "outline" : "default"}
                            >
                                <Upload className="h-4 w-4 mr-2" />
                                {upload ? 'Replace' : 'Upload'}
                            </Button>
                        </div>
                    )}
                </div>
            </CardContent>

            {/* Preview Dialog */}
            {upload && (
                <Dialog open={showPreview} onOpenChange={setShowPreview}>
                    <DialogContent className="max-w-4xl max-h-[90vh]">
                        <DialogHeader>
                            <DialogTitle className="flex items-center gap-2">
                                {getFileIcon(upload.original_filename)({ className: "h-5 w-5" })}
                                {upload.original_filename}
                            </DialogTitle>
                        </DialogHeader>
                        <div className="flex-1 overflow-auto">
                            {isImageFile(upload.original_filename) ? (
                                <div className="flex justify-center">
                                    <img
                                        src={getFilePreviewUrl(upload)}
                                        alt="Document preview"
                                        className="max-w-full max-h-[70vh] object-contain rounded-lg"
                                    />
                                </div>
                            ) : (
                                <div className="text-center py-8">
                                    <FileText className="h-16 w-16 text-muted-foreground mx-auto mb-4" />
                                    <p className="text-lg font-medium mb-2">PDF Document</p>
                                    <p className="text-muted-foreground mb-4">
                                        Preview not available for PDF files
                                    </p>
                                    <Button asChild>
                                        <a
                                            href={getFilePreviewUrl(upload)}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <Download className="h-4 w-4 mr-2" />
                                            Download PDF
                                        </a>
                                    </Button>
                                </div>
                            )}
                        </div>
                    </DialogContent>
                </Dialog>
            )}
        </Card>
    );
}
