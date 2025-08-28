import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { toast } from 'react-hot-toast';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { CheckCircle, Copy, ChevronUp, ChevronDown, Calendar, Lightbulb, Info, Loader2 } from 'lucide-react';

// Validation schema
const linkSchema = z.object({
  url: z.string()
    .min(1, 'Please enter a URL')
    .url('Please enter a valid URL')
    .max(2048, 'URL is too long (maximum 2048 characters)'),
  customAlias: z.string()
    .optional()
    .refine(
      (val) => !val || /^[a-zA-Z0-9_-]+$/.test(val),
      'Custom alias can only contain letters, numbers, hyphens, and underscores'
    ),
  password: z.string()
    .optional()
    .refine(
      (val) => !val || val.length >= 4,
      'Password must be at least 4 characters long'
    ),
  title: z.string().optional(),
  description: z.string().optional(),
  expirationDate: z.string()
    .optional()
    .refine(
      (val) => !val || new Date(val) > new Date(),
      'Expiration date must be in the future'
    ),
});


export function LinkGenerator() {
  const [showAdvanced, setShowAdvanced] = useState(false);
  const [generatedLink, setGeneratedLink] = useState(null);

  const {
    register,
    handleSubmit,
    formState: { errors, isValid, isSubmitting },
    reset,
    watch,
    setError,
    clearErrors,
  } = useForm({
    resolver: zodResolver(linkSchema),
    mode: 'onChange',
  });

  // Field mapping for server errors to form fields
  const fieldMapping = {
    'url': 'url',
    'custom_alias': 'customAlias',
    'password': 'password',
    'title': 'title',
    'description': 'description',
    'expires_at': 'expirationDate',
  };

  // Global error handler
  const handleGlobalError = (error, result = null) => {
    console.error('Error creating link:', error);
    
    if (result?.errors) {
      // Laravel validation errors
      handleServerValidationErrors(result.errors);
    } else if (result?.message) {
      // Server error with message
      toast.error(result.message);
    } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
      // Network/connectivity error
      toast.error('Network error. Please check your connection and try again.');
    } else if (error.name === 'SyntaxError') {
      // JSON parsing error
      toast.error('Server response error. Please try again.');
    } else {
      // Generic error fallback
      toast.error('Something went wrong. Please try again.');
    }
  };

  // Handle server validation errors
  const handleServerValidationErrors = (serverErrors) => {
    let hasFieldErrors = false;
    
    Object.keys(serverErrors).forEach((serverField) => {
      const formField = fieldMapping[serverField];
      const errorMessages = serverErrors[serverField];
      
      if (formField && errorMessages.length > 0) {
        // Set error on the specific form field
        setError(formField, {
          type: 'server',
          message: errorMessages[0], // Use first error message
        });
        hasFieldErrors = true;
      } else {
        // Show errors that don't map to form fields as toasts
        errorMessages.forEach((error) => {
          toast.error(error);
        });
      }
    });

    // If we have field errors, show a general toast as well
    if (hasFieldErrors) {
      toast.error('Please fix the validation errors below.');
    }
  };

  const onSubmit = async (data) => {
    // Clear any previous server errors
    clearErrors();
    
    try {
      const response = await fetch('/links', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          url: data.url,
          custom_alias: data.customAlias,
          password: data.password,
          title: data.title,
          description: data.description,
          expires_at: data.expirationDate,
        }),
      });

      const result = await response.json();

      if (result.success) {
        setGeneratedLink(result.data);
        toast.success(result.message || 'Short link created successfully!');
      } else {
        // Handle server errors using global error handler
        handleGlobalError(new Error('Server validation failed'), result);
      }
    } catch (error) {
      // Handle network and other errors using global error handler
      handleGlobalError(error);
    }
  };

  const copyToClipboard = async (text) => {
    try {
      await navigator.clipboard.writeText(text);
      toast.success('Copied to clipboard!');
    } catch (error) {
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);
      toast.success('Copied to clipboard!');
    }
  };

  const handleCreateAnother = () => {
    setGeneratedLink(null);
    reset();
    clearErrors();
  };

  // Show success state if link was generated
  if (generatedLink) {
    return (
      <div className="space-y-4">
        <div className="rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-800 dark:bg-green-900/20">
          <div className="flex items-center gap-2 mb-4">
            <CheckCircle className="h-5 w-5 text-green-600 dark:text-green-400" />
            <h3 className="text-lg font-semibold text-green-800 dark:text-green-200">
              Link Created Successfully!
            </h3>
          </div>
          
          <div className="space-y-3">
            <div>
              <label className="block text-sm font-medium text-green-700 dark:text-green-300 mb-1">
                Short URL:
              </label>
              <div className="flex gap-2">
                <Input
                  value={generatedLink.short_url}
                  readOnly
                  className="flex-1 bg-white border-green-300 text-green-900 dark:bg-green-900/30 dark:border-green-600 dark:text-green-100"
                />
                <Button
                  type="button"
                  onClick={() => copyToClipboard(generatedLink.short_url)}
                  className="px-3"
                  variant="outline"
                >
                  <Copy className="h-4 w-4" />
                </Button>
              </div>
            </div>
            
            {generatedLink.title && (
              <div>
                <label className="block text-sm font-medium text-green-700 dark:text-green-300 mb-1">
                  Title:
                </label>
                <p className="text-green-800 dark:text-green-200">{generatedLink.title}</p>
              </div>
            )}
            
            {generatedLink.expires_at && (
              <div>
                <label className="block text-sm font-medium text-green-700 dark:text-green-300 mb-1">
                  Expires:
                </label>
                <p className="text-green-800 dark:text-green-200">
                  {new Date(generatedLink.expires_at).toLocaleString()}
                </p>
              </div>
            )}
          </div>
          
          <Button
            type="button"
            onClick={handleCreateAnother}
            className="mt-4 w-full bg-green-600 hover:bg-green-700 text-white"
          >
            Create Another Link
          </Button>
        </div>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <div className="mb-4 flex flex-col gap-3 sm:flex-row">
        <div className="flex-1">
          <Input
            type="url"
            placeholder="Enter a long URL (e.g. https://example.com)"
            {...register('url')}
            className={`border-gray-300 bg-gray-50 text-gray-900 placeholder:text-gray-500 focus:border-primary dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder:text-slate-400 ${
              errors.url ? 'border-red-500 focus:border-red-500' : ''
            }`}
          />
          {errors.url && (
            <p className="mt-1 text-sm text-red-600 dark:text-red-400">
              {errors.url.message}
            </p>
          )}
        </div>
        <Button 
          type="submit"
          disabled={isSubmitting || !isValid}
          className="w-full bg-primary px-4 text-white hover:bg-secondary disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto sm:px-6 md:px-8"
        >
          {isSubmitting ? (
            <>
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              Creating...
            </>
          ) : (
            'Generate Short Link'
          )}
        </Button>
      </div>
      <button
        type="button"
        onClick={() => setShowAdvanced(!showAdvanced)}
        className="mx-auto mb-4 flex items-center text-sm text-primary transition-colors hover:text-secondary"
      >
        {showAdvanced ? <ChevronUp className="mr-1 h-4 w-4" /> : <ChevronDown className="mr-1 h-4 w-4" />}
        {showAdvanced ? 'Hide Advanced Options' : 'Show Advanced Options'}
      </button>
      {/* Advanced Options */}
      {showAdvanced && (
        <div className="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:p-6 dark:border-slate-600 dark:bg-slate-700/30">
          <h3 className="mb-4 text-left font-medium text-gray-900 dark:text-white">Advanced Options</h3>
          <div className="mb-4 grid gap-4 md:grid-cols-2">
            {/* Custom Alias */}
            <div>
              <label className="mb-2 block text-left text-sm text-gray-600 dark:text-slate-300">Custom Alias (Optional)</label>
              <Input
                placeholder="e.g., my-custom-link"
                {...register('customAlias')}
                className={`border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder:text-slate-400 ${
                  errors.customAlias ? 'border-red-500 focus:border-red-500' : ''
                }`}
              />
              {errors.customAlias && (
                <p className="mt-1 text-sm text-red-600 dark:text-red-400">
                  {errors.customAlias.message}
                </p>
              )}
            </div>
            {/* Password Protection */}
            <div>
              <label className="mb-2 block text-left text-sm text-gray-600 dark:text-slate-300">
                Password Protection (Optional)
              </label>
              <Input
                type="password"
                placeholder="Enter password"
                {...register('password')}
                className={`border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder:text-slate-400 ${
                  errors.password ? 'border-red-500 focus:border-red-500' : ''
                }`}
              />
              {errors.password && (
                <p className="mt-1 text-sm text-red-600 dark:text-red-400">
                  {errors.password.message}
                </p>
              )}
            </div>
          </div>
          <div className="mb-4 grid gap-4 md:grid-cols-2">
            {/* Title */}
            <div>
              <label className="mb-2 block text-left text-sm text-gray-600 dark:text-slate-300">
                Title (Optional) • <span className="text-primary">Auto-detected if empty</span>
              </label>
              <Input
                placeholder="Give your link a title (or leave empty for auto-detection)"
                {...register('title')}
                className="border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder:text-slate-400"
              />
            </div>
            {/* Expiration Date */}
            <div>
              <label className="mb-2 block text-left text-sm text-gray-600 dark:text-slate-300">
                Expiration Date (Optional)
              </label>
              <div className="relative">
                <Input
                  type="datetime-local"
                  {...register('expirationDate')}
                  className={`border-gray-300 bg-white text-gray-900 dark:border-slate-600 dark:bg-slate-700 dark:text-white ${
                    errors.expirationDate ? 'border-red-500 focus:border-red-500' : ''
                  }`}
                />
                <Calendar className="pointer-events-none absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 transform text-gray-500 dark:text-slate-400" />
              </div>
              {errors.expirationDate && (
                <p className="mt-1 text-sm text-red-600 dark:text-red-400">
                  {errors.expirationDate.message}
                </p>
              )}
            </div>
          </div>
          {/* Description */}
          <div className="mb-4">
            <label className="mb-2 block text-left text-sm text-gray-600 dark:text-slate-300">
              Description (Optional) • <span className="text-primary">Auto-detected if empty</span>
            </label>
            <Textarea
              placeholder="Add a description for your link (or leave empty for auto-detection)"
              {...register('description')}
              className="resize-none border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder:text-slate-400"
              rows={3}
            />
          </div>
          {/* Smart Detection Info */}
          <div className="mb-4 rounded-lg border border-purple-500/30 bg-purple-600/10 p-4">
            <div className="flex items-start gap-2">
              <Lightbulb className="mt-0.5 h-4 w-4 flex-shrink-0 text-purple-400" />
              <div className="text-sm text-purple-300">
                <strong>Smart Detection:</strong> If you leave title or description empty, we'll automatically fetch them from
                the webpage's meta tags for better link previews.
              </div>
            </div>
          </div>
        </div>
      )}
      <div className="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-slate-600 dark:bg-slate-700/50">
        <div className="flex items-start gap-2 text-sm">
          <Info className="mt-0.5 h-4 w-4 flex-shrink-0 text-cyan-400" />
          <p className="text-gray-600 dark:text-slate-300">
            <strong>Tip:</strong> Create an account to manage your links, view detailed analytics, and access advanced features.
            <a href="#" className="ml-1 text-primary hover:text-cyan-300">
              Sign up free
            </a>
          </p>
        </div>
      </div>
    </form>
  );
}
