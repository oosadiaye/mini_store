<?php

if (!function_exists('tenant_asset')) {
    /**
     * Get the URL for a tenant asset.
     *
     * @param string|null $path
     * @return string|null
     */
    function tenant_asset($path)
    {
        if (!$path) {
            return null;
        }

        // If it's already a full URL, return it
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Return the route to the media handler
        return route('tenant.media', ['path' => $path]);
    }
}
