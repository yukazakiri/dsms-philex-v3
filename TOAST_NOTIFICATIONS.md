# Toast Notifications Guide

## Overview

This project uses the Sonner toast library for displaying notifications to users. The toast system is integrated with Laravel's flash message system and Inertia.js, providing automatic toast notifications across all layouts.

## How It Works

### 1. Backend Flash Messages

Controllers use Laravel's `with()` method to set flash messages. The following flash message types are supported:

```php
// Success message
return Redirect::back()->with('success', 'Operation completed successfully.');

// Error message
return Redirect::back()->with('error', 'Something went wrong.');

// Warning message
return Redirect::back()->with('warning', 'Please check your input.');

// Info message
return Redirect::back()->with('info', 'Here is some information.');

// Status message (typically from Laravel auth)
return Redirect::back()->with('status', 'Password reset link sent.');
```

### 2. Inertia Middleware Configuration

The `HandleInertiaRequests` middleware automatically shares flash messages with the frontend:

```php
// In app/Http/Middleware/HandleInertiaRequests.php
'flash' => [
    'success' => $request->session()->get('success'),
    'error' => $request->session()->get('error'),
    'warning' => $request->session()->get('warning'),
    'info' => $request->session()->get('info'),
    'status' => $request->session()->get('status'),
],
```

### 3. Frontend Toast Display

All layouts automatically handle flash messages and display them as toasts:

- **AppSidebarLayout** (main app layout)
- **AuthSplitLayout** (authentication pages)
- **SettingsLayout** (settings pages)

```typescript
// Example from app-sidebar-layout.tsx
useEffect(() => {
    if (flash?.success) toast.success(flash.success);
    if (flash?.error) toast.error(flash.error);
    if (flash?.warning) toast.warning(flash.warning);
    if (flash?.info) toast.info(flash.info);
    if (flash?.status) toast.info(flash.status);
}, [flash]);
```

### 4. Manual Toast Notifications

You can also trigger toasts manually in React components:

```typescript
import { toast } from 'sonner';

// Success toast
toast.success('Operation completed!');

// Error toast
toast.error('Something went wrong!');

// Info toast
toast.info('Information message');

// Warning toast
toast.warning('Warning message');
```

## Testing

### Test Page

Visit `/test-notifications` to test both database notifications and flash messages. The page includes:

- Database notification tests
- Flash message tests for all types (success, error, warning, info, status)
- Real-time notification display

### Test Routes

The following test routes are available for flash messages:

```
POST /test-flash/success  - Test success messages
POST /test-flash/error    - Test error messages
POST /test-flash/warning  - Test warning messages
POST /test-flash/info     - Test info messages
POST /test-flash/status   - Test status messages
```

## Scholarship Edit Fix

### Problem
The Scholarship Edit page was not showing toast notifications when saving changes.

### Solution
1. **Backend**: Changed redirect from `show` page to `back()` so the flash message appears on the edit page
2. **Frontend**: Added error handling with manual toast for validation errors
3. **Layout**: The existing `AppSidebarLayout` handles success flash messages automatically

### Code Changes

**Backend (ScholarshipController.php):**
```php
// Before
return Redirect::route('admin.scholarships.show', $scholarship->id)
    ->with('success', 'Scholarship program updated successfully.');

// After  
return Redirect::back()
    ->with('success', 'Scholarship program updated successfully.');
```

**Frontend (Edit.tsx):**
```typescript
put(route('admin.scholarships.update', scholarship.id), {
  onError: (errors) => {
    console.error('Scholarship update failed:', errors);
    toast.error('Failed to update scholarship. Please check the form and try again.');
  },
});
```

## Testing

### Manual Testing
1. Go to Admin → Scholarships → Edit any scholarship
2. Make changes and save
3. You should see a green success toast: "Scholarship program updated successfully."
4. If there are validation errors, you'll see a red error toast

### Automated Testing
```bash
php artisan test tests/Feature/Admin/ScholarshipDocumentRequirementsTest.php
```

## Toast Configuration

The toast system is configured in `AppSidebarLayout`:

```typescript
<Toaster position="top-right" richColors closeButton />
```

Options:
- `position`: Where toasts appear (top-right, top-left, bottom-right, etc.)
- `richColors`: Enables colored backgrounds for different toast types
- `closeButton`: Shows an X button to manually close toasts

## Debugging

### Console Logs
- Backend logs are in `storage/logs/laravel.log`
- Frontend logs are in browser console
- Look for "Scholarship Update" entries in logs

### Common Issues
1. **No toast appears**: Check if the page uses a layout with toast support (AppSidebarLayout, AuthSplitLayout, or SettingsLayout)
2. **Toast appears on wrong page**: Ensure backend redirects to the correct page with `Redirect::back()`
3. **Multiple toasts**: Avoid manual toasts when flash messages are already handled by the layout
4. **Flash messages not shared**: Ensure `HandleInertiaRequests` middleware includes flash messages in shared data

## Best Practices

### Backend
- Use appropriate flash message types (`success`, `error`, `warning`, `info`, `status`)
- Always redirect with flash messages: `return Redirect::back()->with('success', 'Message')`
- Use descriptive, user-friendly messages

### Frontend
- All layouts automatically handle flash messages - no manual setup needed
- Use manual toasts only for client-side actions that don't involve server redirects
- Toast positioning is consistent across all layouts (top-right)

## Supported Layouts

All major layouts include automatic toast support:

1. **AppSidebarLayout** - Main application layout
2. **AuthSplitLayout** - Authentication pages (login, register, etc.)
3. **SettingsLayout** - User settings pages

Each layout includes:
- Automatic flash message handling
- Sonner Toaster component with consistent styling
- Support for all flash message types

1. **Use flash messages for server-side operations** (form submissions, database updates)
2. **Use manual toasts for client-side operations** (validation, UI feedback)
3. **Keep messages concise and user-friendly**
4. **Use appropriate toast types** (success for positive actions, error for failures)
5. **Test both success and error scenarios**
