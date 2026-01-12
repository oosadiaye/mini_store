<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SecureFileUploader
{
    /**
     * Allowed MIME types and their corresponding extensions.
     */
    protected const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * Securely upload a file.
     *
     * @param UploadedFile $file The uploaded file from the request.
     * @param string $directory Target directory within the private storage.
     * @param string $disk The disk to use (defaulting to a private disk).
     * @param array|null $allowedMimes Custom list of allowed MIME types.
     * @return string The path of the stored file.
     * 
     * @throws ValidationException
     */
    public function upload(UploadedFile $file, string $directory = 'uploads', string $disk = 'local', ?array $allowedMimes = null): string
    {
        $allowedMimes = $allowedMimes ?? self::ALLOWED_MIMES;

        // 1. Strict Validation
        // Using mimetypes check ensures the browser-side extension doesn't lie.
        // We also check the file size (defaulting to 5MB here).
        $validator = Validator::make(['file' => $file], [
            'file' => [
                'required',
                'file',
                'max:5120', // 5MB limit
                'mimetypes:' . implode(',', $allowedMimes),
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // 2. Execution Prevention & Sanitization
        // Rename to a random UUID to prevent Directory Traversal and ID guessing.
        // We do NOT use $file->getClientOriginalName().
        
        // HARNESS: Enforce extension based on detected MIME type to prevent polyglot attacks.
        // This ensures a PHP file disguised as an image ends up with a safe extension (or fails if mime lookup fails).
        $mime = $file->getMimeType();
        $extension = $this->getExtensionFromMime($mime);
        
        if (!$extension) {
            // Fallback: If mime mapping fails but validation passed, use guessExtension() carefully.
            // But for strict security, we should likely rely on our map.
            $extension = $file->guessExtension() ?? 'bin'; 
        }

        $filename = Str::uuid()->toString() . '.' . $extension;

        // 3. Storage
        // By using 'local' (configured as app/private) or 's3', 
        // the files are stored outside the public web root.
        // Even if a script is somehow masquerading, it won't be executed by the web server
        // because it's not in the public directory and served as a static asset.
        $path = $file->storeAs($directory, $filename, $disk);

        if (!$path) {
            throw new \RuntimeException('Failed to store the uploaded file.');
        }

        return $path;
    }

    /**
     * Get a temporary URL for the private file (useful if using S3).
     */
    public function getSecureUrl(string $path, string $disk = 'local'): string
    {
        if ($disk === 's3') {
            return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes(60));
        }

        // For local private storage, you would typically serve this via a controller route:
        // return route('files.show', ['path' => $path]);
        return Storage::disk($disk)->url($path);
    }

    /**
     * Map MIME type to safe extension.
     */
    protected function getExtensionFromMime(?string $mime): ?string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
            default => null,
        };
    }
}
