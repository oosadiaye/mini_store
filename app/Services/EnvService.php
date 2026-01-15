<?php

namespace App\Services;

class EnvService
{
    public static function update(array $data)
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return false;
        }

        $content = file_get_contents($path);

        foreach ($data as $key => $value) {
            $key = strtoupper($key);
            // Check if key exists
            if (preg_match("/^{$key}=/m", $content)) {
                // Update existing key
                $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
            } else {
                // Add new key
                $content .= "\n{$key}=\"{$value}\"";
            }
        }

        return file_put_contents($path, $content);
    }
}
