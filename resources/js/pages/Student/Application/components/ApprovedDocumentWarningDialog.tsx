import React from 'react';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { AlertTriangle } from 'lucide-react';

interface ApprovedDocumentWarningDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onConfirm: () => void;
    onCancel: () => void;
}

export default function ApprovedDocumentWarningDialog({ 
    open, 
    onOpenChange, 
    onConfirm, 
    onCancel 
}: ApprovedDocumentWarningDialogProps) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <AlertTriangle className="h-5 w-5 text-amber-600" />
                        Update Approved Document?
                    </DialogTitle>
                    <DialogDescription>
                        This document has already been approved. If you upload a new version, it will need to be reviewed again and your application status may be affected.
                    </DialogDescription>
                </DialogHeader>
                
                <div className="space-y-4">
                    <div className="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <div className="flex items-start gap-3">
                            <AlertTriangle className="h-5 w-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" />
                            <div className="space-y-2">
                                <p className="text-sm font-medium text-amber-800 dark:text-amber-200">
                                    Important Notice:
                                </p>
                                <ul className="text-sm text-amber-700 dark:text-amber-300 space-y-1">
                                    <li>• Your document status will change from "Approved" to "Pending Review"</li>
                                    <li>• The admin will need to review your new document</li>
                                    <li>• This may delay your application processing</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div className="flex gap-3">
                        <Button 
                            type="button" 
                            variant="outline" 
                            onClick={onCancel}
                            className="flex-1"
                        >
                            Cancel
                        </Button>
                        <Button 
                            type="button" 
                            onClick={onConfirm}
                            className="flex-1 bg-amber-600 hover:bg-amber-700"
                        >
                            Continue Update
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}
