<?php

namespace App\Helpers;

class ShortcodeHelper
{
    /**
     * Parse content and replace valid shortcodes.
     * 
     * @param mixed $content String or Array to parse recursively.
     * @return mixed Parsed content.
     */
    public static function parse($content)
    {
        if (is_array($content)) {
            foreach ($content as $key => $value) {
                $content[$key] = self::parse($value);
            }
            return $content;
        }

        if (is_string($content)) {
            return self::replaceShortcodes($content);
        }

        return $content;
    }

    protected static function replaceShortcodes($string)
    {
        // [company_name]
        $string = str_replace('[company_name]', tenant('name') ?? 'Our Store', $string);

        // [company_logo]
        // This creates an IMG tag. If user wants JUST the URL, they might need [company_logo_url]
        // Assuming [company_logo] is used in a text block, we render the img.
        if (str_contains($string, '[company_logo]')) {
            $logoUrl = \App\Helpers\LogoHelper::getLogo();
            $imgTag = "<img src='{$logoUrl}' alt='" . (tenant('name') ?? 'Logo') . "' class='h-8 inline-block align-middle'>";
            $string = str_replace('[company_logo]', $imgTag, $string);
        }

        // [company_logo_url] - For use in image inputs if they manually type it?
        // Unlikely to identify [company_logo_url] inside a "text" field as meaningful unless it's an image src.
        // We'll leave it simple for now.

        // [year]
        $string = str_replace('[year]', date('Y'), $string);

        return $string;
    }
}
