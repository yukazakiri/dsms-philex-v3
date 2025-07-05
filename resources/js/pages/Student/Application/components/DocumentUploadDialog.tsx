import React, { useState, useRef, useCallback } from 'react';
import { useForm, router } from '@inertiajs/react';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { Card, CardContent } from '@/components/ui/card';
import {
    Upload,
    File,
    Image as ImageIcon,
    FileText,
    X,
    CheckCircle2,
    AlertCircle,
    Camera,
    Loader2
} from 'lucide-react';
import { DocumentRequirement, DocumentUpload } from '@/types';
import { route } from 'ziggy-js';
import { toast } from 'sonner';

interface DocumentUploadDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    requirement: DocumentRequirement;
    applicationId: number;
    existingUpload: DocumentUpload | null;
}

export default function DocumentUploadDialog({
    open,
    onOpenChange,
    requirement,
    applicationId,
    existingUpload
}: DocumentUploadDialogProps) {
    const [dragActive, setDragActive] = useState(false);
    const [previewUrl, setPreviewUrl] = useState<string | null>(null);
    const [fileInfo, setFileInfo] = useState<{name: string, size: string, type: string} | null>(null);
    const [uploadSuccess, setUploadSuccess] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const { data, setData, post, processing, progress, reset } = useForm({
        document_requirement_id: requirement.id,
        document: null as File | null,
    });

    const formatFileSize = (bytes: number): string => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    const isImageFile = (file: File): boolean => {
        return file.type.startsWith('image/');
    };

    const handleFileSelect = useCallback((file: File) => {
        setData('document', file);
        setFileInfo({
            name: file.name,
            size: formatFileSize(file.size),
            type: file.type
        });

        // Create preview for images
        if (isImageFile(file)) {
            const reader = new FileReader();
            reader.onload = (e) => {
                setPreviewUrl(e.target?.result as string);
            };
            reader.readAsDataURL(file);
        } else {
            setPreviewUrl(null);
        }
    }, [setData]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!data.document) return;

        post(route('student.applications.documents.upload', applicationId), {
            onSuccess: () => {
                setUploadSuccess(true);
                toast.success('Document uploaded successfully!');

                // Force refresh the page data to ensure the new upload is visible
                setTimeout(() => {
                    router.reload({ only: ['documentUploads'] });
                    onOpenChange(false);
                    handleReset();
                }, 1500);
            },
            onError: (errors) => {
                console.error('Upload errors:', errors);
                toast.error('Failed to upload document. Please try again.');
            },
            preserveScroll: true,
            preserveState: false
        });
    };

    const handleReset = () => {
        reset();
        setPreviewUrl(null);
        setFileInfo(null);
        setUploadSuccess(false);
        setDragActive(false);
    };

    const handleDrag = useCallback((e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.type === "dragenter" || e.type === "dragover") {
            setDragActive(true);
        } else if (e.type === "dragleave") {
            setDragActive(false);
        }
    }, []);

    const handleDrop = useCallback((e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(false);

        const files = e.dataTransfer.files;
        if (files && files[0]) {
            const file = files[0];
            // Validate file type
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (allowedTypes.includes(file.type)) {
                handleFileSelect(file);
            } else {
                toast.error('Please select a PDF, JPG, JPEG, or PNG file.');
            }
        }
    }, [handleFileSelect]);

    const handleFileInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const files = e.target.files;
        if (files && files[0]) {
            handleFileSelect(files[0]);
        }
    };

    const clearFile = () => {
        setData('document', null);
        setPreviewUrl(null);
        setFileInfo(null);
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    return (
        <Dialog open={open} onOpenChange={(open) => {
            if (!open) handleReset();
            onOpenChange(open);
        }}>
            <DialogContent className="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        {uploadSuccess ? (
                            <CheckCircle2 className="h-5 w-5 text-green-600" />
                        ) : (
                            <Upload className="h-5 w-5" />
                        )}
                        {uploadSuccess ? 'Upload Successful!' : (existingUpload ? 'Replace Document' : 'Upload Document')}
                    </DialogTitle>
                    <DialogDescription>
                        {requirement.name}
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-4">
                    <div className="p-3 bg-muted/50 rounded-lg">
                        <p className="text-sm text-muted-foreground">
                            {requirement.description}
                        </p>
                    </div>

                    {existingUpload && (
                        <div className={`p-3 border rounded-lg ${
                            existingUpload.status === 'approved' 
                                ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800'
                                : 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800'
                        }`}>
                            <div className="flex items-center gap-2 mb-1">
                                <File className={`h-4 w-4 ${
                                    existingUpload.status === 'approved' ? 'text-green-600' : 'text-blue-600'
                                }`} />
                                <span className="text-sm font-medium">Current File</span>
                                {existingUpload.status === 'approved' && (
                                    <Badge variant="default" className="bg-green-600 hover:bg-green-700">
                                        Approved
                                    </Badge>
                                )}
                            </div>
                            <p className="text-sm text-muted-foreground">
                                {existingUpload.original_filename}
                            </p>
                            {existingUpload.status === 'approved' && (
                                <p className="text-xs text-green-700 dark:text-green-300 mt-2">
                                    ⚠️ Uploading a new file will reset the status to "Pending Review"
                                </p>
                            )}
                        </div>
                    )}

                    {uploadSuccess ? (
                        <div className="text-center py-8">
                            <CheckCircle2 className="h-16 w-16 text-green-600 mx-auto mb-4" />
                            <h3 className="text-lg font-semibold text-green-700 mb-2">Upload Complete!</h3>
                            <p className="text-sm text-muted-foreground">
                                Your document has been uploaded successfully and is now pending review.
                            </p>
                        </div>
                    ) : (
                        <form onSubmit={handleSubmit} className="space-y-4">
                            {/* Drag and Drop Upload Area */}
                            <div
                                className={`relative border-2 border-dashed rounded-lg p-6 transition-colors ${
                                    dragActive
                                        ? 'border-primary bg-primary/5'
                                        : 'border-muted-foreground/25 hover:border-primary/50'
                                } ${processing ? 'pointer-events-none opacity-50' : ''}`}
                                onDragEnter={handleDrag}
                                onDragLeave={handleDrag}
                                onDragOver={handleDrag}
                                onDrop={handleDrop}
                            >
                                <input
                                    ref={fileInputRef}
                                    type="file"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    onChange={handleFileInputChange}
                                    disabled={processing}
                                    className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                />

                                {data.document ? (
                                    <div className="space-y-4">
                                        {/* File Preview */}
                                        <div className="flex items-start gap-4">
                                            <div className="flex-shrink-0">
                                                {previewUrl ? (
                                                    <div className="relative">
                                                        <img
                                                            src={previewUrl}
                                                            alt="Preview"
                                                            className="w-20 h-20 object-cover rounded-lg border"
                                                        />
                                                        <div className="absolute -top-2 -right-2">
                                                            <Button
                                                                type="button"
                                                                variant="destructive"
                                                                size="sm"
                                                                className="h-6 w-6 rounded-full p-0"
                                                                onClick={clearFile}
                                                            >
                                                                <X className="h-3 w-3" />
                                                            </Button>
                                                        </div>
                                                    </div>
                                                ) : (
                                                    <div className="relative">
                                                        <div className="w-20 h-20 bg-muted rounded-lg border flex items-center justify-center">
                                                            <FileText className="h-8 w-8 text-muted-foreground" />
                                                        </div>
                                                        <div className="absolute -top-2 -right-2">
                                                            <Button
                                                                type="button"
                                                                variant="destructive"
                                                                size="sm"
                                                                className="h-6 w-6 rounded-full p-0"
                                                                onClick={clearFile}
                                                            >
                                                                <X className="h-3 w-3" />
                                                            </Button>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>

                                            <div className="flex-1 min-w-0">
                                                <p className="font-medium text-sm truncate">{fileInfo?.name}</p>
                                                <p className="text-xs text-muted-foreground">{fileInfo?.size}</p>
                                                <div className="flex items-center gap-1 mt-1">
                                                    {isImageFile(data.document) ? (
                                                        <ImageIcon className="h-3 w-3 text-blue-500" />
                                                    ) : (
                                                        <FileText className="h-3 w-3 text-red-500" />
                                                    )}
                                                    <span className="text-xs text-muted-foreground">
                                                        {isImageFile(data.document) ? 'Image' : 'PDF'}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="text-center">
                                        <div className="mx-auto h-12 w-12 text-muted-foreground mb-4">
                                            {dragActive ? (
                                                <Upload className="h-12 w-12" />
                                            ) : (
                                                <Camera className="h-12 w-12" />
                                            )}
                                        </div>
                                        <div className="space-y-2">
                                            <p className="text-sm font-medium">
                                                {dragActive ? 'Drop your file here' : 'Drag and drop your file here'}
                                            </p>
                                            <p className="text-xs text-muted-foreground">
                                                or click to browse files
                                            </p>
                                            <p className="text-xs text-muted-foreground">
                                                Supports PDF, JPG, JPEG, PNG (max 10MB)
                                            </p>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Upload Progress */}
                            {processing && progress && progress.percentage !== undefined && (
                                <div className="space-y-2">
                                    <div className="flex justify-between text-sm">
                                        <span className="flex items-center gap-2">
                                            <Loader2 className="h-4 w-4 animate-spin" />
                                            Uploading...
                                        </span>
                                        <span>{Math.round(progress.percentage)}%</span>
                                    </div>
                                    <Progress value={progress.percentage} className="h-2" />
                                </div>
                            )}

                            {/* Action Buttons */}
                            <div className="flex gap-3 pt-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => onOpenChange(false)}
                                    disabled={processing}
                                    className="flex-1"
                                >
                                    Cancel
                                </Button>
                                <Button
                                    type="submit"
                                    disabled={processing || !data.document}
                                    className="flex-1"
                                >
                                    {processing ? (
                                        <>
                                            <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                                            Uploading...
                                        </>
                                    ) : (
                                        <>
                                            <Upload className="h-4 w-4 mr-2" />
                                            Upload Document
                                        </>
                                    )}
                                </Button>
                            </div>
                        </form>
                    )}
                </div>
            </DialogContent>
        </Dialog>
    );
}
