<?php

namespace App\Helpers;

class SettingsSchema
{
    /**
     * Get schema definition for Hero section
     */
    public static function getHeroSchema(): array
    {
        return [
            'content' => [
                'title' => [
                    'type' => 'text',
                    'label' => 'Title',
                    'default' => 'Welcome to Our Store',
                ],
                'subtitle' => [
                    'type' => 'textarea',
                    'label' => 'Subtitle',
                    'default' => 'Discover amazing products',
                ],
                'button_text' => [
                    'type' => 'text',
                    'label' => 'Button Text',
                    'default' => 'Shop Now',
                ],
                'button_url' => [
                    'type' => 'text',
                    'label' => 'Button URL',
                    'default' => '/products',
                ],
            ],
            'dimensions' => [
                'min_height_desktop' => [
                    'type' => 'range',
                    'label' => 'Min Height (Desktop)',
                    'min' => 200,
                    'max' => 2000,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 600,
                ],
                'min_height_mobile' => [
                    'type' => 'range',
                    'label' => 'Min Height (Mobile)',
                    'min' => 200,
                    'max' => 1000,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 400,
                ],
            ],
            'typography' => [
                'title_font_size_desktop' => [
                    'type' => 'range',
                    'label' => 'Title Font Size (Desktop)',
                    'min' => 20,
                    'max' => 120,
                    'step' => 2,
                    'unit' => 'px',
                    'default' => 64,
                ],
                'title_font_size_mobile' => [
                    'type' => 'range',
                    'label' => 'Title Font Size (Mobile)',
                    'min' => 16,
                    'max' => 80,
                    'step' => 2,
                    'unit' => 'px',
                    'default' => 36,
                ],
                'title_font_weight' => [
                    'type' => 'select',
                    'label' => 'Title Font Weight',
                    'options' => [
                        300 => 'Light (300)',
                        400 => 'Normal (400)',
                        500 => 'Medium (500)',
                        600 => 'Semi-Bold (600)',
                        700 => 'Bold (700)',
                        800 => 'Extra Bold (800)',
                    ],
                    'default' => 700,
                ],
                'title_line_height' => [
                    'type' => 'range',
                    'label' => 'Title Line Height',
                    'min' => 0.8,
                    'max' => 3.0,
                    'step' => 0.1,
                    'unit' => '',
                    'default' => 1.2,
                ],
                'title_letter_spacing' => [
                    'type' => 'range',
                    'label' => 'Title Letter Spacing',
                    'min' => -5,
                    'max' => 20,
                    'step' => 0.5,
                    'unit' => 'px',
                    'default' => 0,
                ],
                'title_color' => [
                    'type' => 'color',
                    'label' => 'Title Color',
                    'default' => '#ffffff',
                ],
                'title_text_align_desktop' => [
                    'type' => 'alignment',
                    'label' => 'Title Alignment (Desktop)',
                    'options' => ['left', 'center', 'right', 'justify'],
                    'default' => 'center',
                ],
                'title_text_align_mobile' => [
                    'type' => 'alignment',
                    'label' => 'Title Alignment (Mobile)',
                    'options' => ['left', 'center', 'right', 'justify'],
                    'default' => 'center',
                ],
                'subtitle_font_size_desktop' => [
                    'type' => 'range',
                    'label' => 'Subtitle Font Size (Desktop)',
                    'min' => 12,
                    'max' => 48,
                    'step' => 1,
                    'unit' => 'px',
                    'default' => 24,
                ],
                'subtitle_font_size_mobile' => [
                    'type' => 'range',
                    'label' => 'Subtitle Font Size (Mobile)',
                    'min' => 12,
                    'max' => 32,
                    'step' => 1,
                    'unit' => 'px',
                    'default' => 18,
                ],
                'subtitle_color' => [
                    'type' => 'color',
                    'label' => 'Subtitle Color',
                    'default' => '#f0f0f0',
                ],
            ],
            'spacing' => [
                'padding_top_desktop' => [
                    'type' => 'range',
                    'label' => 'Padding Top (Desktop)',
                    'min' => 0,
                    'max' => 500,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 120,
                ],
                'padding_bottom_desktop' => [
                    'type' => 'range',
                    'label' => 'Padding Bottom (Desktop)',
                    'min' => 0,
                    'max' => 500,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 120,
                ],
                'padding_left_desktop' => [
                    'type' => 'range',
                    'label' => 'Padding Left (Desktop)',
                    'min' => 0,
                    'max' => 500,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 40,
                ],
                'padding_right_desktop' => [
                    'type' => 'range',
                    'label' => 'Padding Right (Desktop)',
                    'min' => 0,
                    'max' => 500,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 40,
                ],
                'padding_top_mobile' => [
                    'type' => 'range',
                    'label' => 'Padding Top (Mobile)',
                    'min' => 0,
                    'max' => 300,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 60,
                ],
                'padding_bottom_mobile' => [
                    'type' => 'range',
                    'label' => 'Padding Bottom (Mobile)',
                    'min' => 0,
                    'max' => 300,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 60,
                ],
                'padding_left_mobile' => [
                    'type' => 'range',
                    'label' => 'Padding Left (Mobile)',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'unit' => 'px',
                    'default' => 20,
                ],
                'padding_right_mobile' => [
                    'type' => 'range',
                    'label' => 'Padding Right (Mobile)',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'unit' => 'px',
                    'default' => 20,
                ],
            ],
            'background' => [
                'background_color' => [
                    'type' => 'color',
                    'label' => 'Background Color',
                    'default' => '#000000',
                ],
                'background_image' => [
                    'type' => 'image',
                    'label' => 'Background Image',
                    'default' => '',
                ],
                'background_size' => [
                    'type' => 'select',
                    'label' => 'Background Size',
                    'options' => [
                        'cover' => 'Cover',
                        'contain' => 'Contain',
                        'auto' => 'Auto',
                    ],
                    'default' => 'cover',
                ],
                'background_position_x' => [
                    'type' => 'range',
                    'label' => 'Background Position X',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'unit' => '%',
                    'default' => 50,
                ],
                'background_position_y' => [
                    'type' => 'range',
                    'label' => 'Background Position Y',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'unit' => '%',
                    'default' => 50,
                ],
                'overlay_color' => [
                    'type' => 'color',
                    'label' => 'Overlay Color',
                    'default' => '#000000',
                ],
                'overlay_opacity' => [
                    'type' => 'range',
                    'label' => 'Overlay Opacity',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'unit' => '%',
                    'default' => 40,
                ],
            ],
            'visibility' => [
                'hide_on_desktop' => [
                    'type' => 'checkbox',
                    'label' => 'Hide on Desktop',
                    'default' => false,
                ],
                'hide_on_mobile' => [
                    'type' => 'checkbox',
                    'label' => 'Hide on Mobile',
                    'default' => false,
                ],
            ],
        ];
    }

    /**
     * Get schema definition for Product Grid section
     */
    public static function getProductGridSchema(): array
    {
        return [
            'content' => [
                'title' => [
                    'type' => 'text',
                    'label' => 'Section Title',
                    'default' => 'Featured Products',
                ],
                'limit' => [
                    'type' => 'range',
                    'label' => 'Number of Products',
                    'min' => 1,
                    'max' => 50,
                    'step' => 1,
                    'unit' => '',
                    'default' => 8,
                ],
                'category_filter' => [
                    'type' => 'select',
                    'label' => 'Filter by Category',
                    'options' => [], // Populated dynamically
                    'default' => null,
                ],
                'sort_by' => [
                    'type' => 'select',
                    'label' => 'Sort By',
                    'options' => [
                        'latest' => 'Latest',
                        'price_low' => 'Price: Low to High',
                        'price_high' => 'Price: High to Low',
                        'name' => 'Name',
                    ],
                    'default' => 'latest',
                ],
            ],
            'layout' => [
                'columns_desktop' => [
                    'type' => 'range',
                    'label' => 'Columns (Desktop)',
                    'min' => 1,
                    'max' => 6,
                    'step' => 1,
                    'unit' => '',
                    'default' => 4,
                ],
                'columns_tablet' => [
                    'type' => 'range',
                    'label' => 'Columns (Tablet)',
                    'min' => 1,
                    'max' => 4,
                    'step' => 1,
                    'unit' => '',
                    'default' => 3,
                ],
                'columns_mobile' => [
                    'type' => 'range',
                    'label' => 'Columns (Mobile)',
                    'min' => 1,
                    'max' => 3,
                    'step' => 1,
                    'unit' => '',
                    'default' => 2,
                ],
                'gap_desktop' => [
                    'type' => 'range',
                    'label' => 'Gap (Desktop)',
                    'min' => 0,
                    'max' => 100,
                    'step' => 4,
                    'unit' => 'px',
                    'default' => 24,
                ],
                'gap_mobile' => [
                    'type' => 'range',
                    'label' => 'Gap (Mobile)',
                    'min' => 0,
                    'max' => 50,
                    'step' => 4,
                    'unit' => 'px',
                    'default' => 16,
                ],
            ],
            'spacing' => [
                'padding_top' => [
                    'type' => 'range',
                    'label' => 'Padding Top',
                    'min' => 0,
                    'max' => 300,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 80,
                ],
                'padding_bottom' => [
                    'type' => 'range',
                    'label' => 'Padding Bottom',
                    'min' => 0,
                    'max' => 300,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 80,
                ],
            ],
        ];
    }

    /**
     * Get schema definition for Text/Content Block section
     */
    public static function getTextBlockSchema(): array
    {
        return [
            'content' => [
                'content' => [
                    'type' => 'wysiwyg',
                    'label' => 'Content',
                    'default' => '<p>Your content here...</p>',
                ],
            ],
            'typography' => [
                'font_size_desktop' => [
                    'type' => 'range',
                    'label' => 'Font Size (Desktop)',
                    'min' => 12,
                    'max' => 32,
                    'step' => 1,
                    'unit' => 'px',
                    'default' => 16,
                ],
                'font_size_mobile' => [
                    'type' => 'range',
                    'label' => 'Font Size (Mobile)',
                    'min' => 12,
                    'max' => 24,
                    'step' => 1,
                    'unit' => 'px',
                    'default' => 14,
                ],
                'line_height' => [
                    'type' => 'range',
                    'label' => 'Line Height',
                    'min' => 1.0,
                    'max' => 3.0,
                    'step' => 0.1,
                    'unit' => '',
                    'default' => 1.6,
                ],
                'text_color' => [
                    'type' => 'color',
                    'label' => 'Text Color',
                    'default' => '#333333',
                ],
                'text_align_desktop' => [
                    'type' => 'alignment',
                    'label' => 'Text Alignment (Desktop)',
                    'options' => ['left', 'center', 'right', 'justify'],
                    'default' => 'left',
                ],
                'text_align_mobile' => [
                    'type' => 'alignment',
                    'label' => 'Text Alignment (Mobile)',
                    'options' => ['left', 'center', 'right', 'justify'],
                    'default' => 'left',
                ],
            ],
            'dimensions' => [
                'max_width' => [
                    'type' => 'range',
                    'label' => 'Max Width',
                    'min' => 400,
                    'max' => 1400,
                    'step' => 50,
                    'unit' => 'px',
                    'default' => 800,
                ],
            ],
            'spacing' => [
                'padding_top' => [
                    'type' => 'range',
                    'label' => 'Padding Top',
                    'min' => 0,
                    'max' => 200,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 60,
                ],
                'padding_bottom' => [
                    'type' => 'range',
                    'label' => 'Padding Bottom',
                    'min' => 0,
                    'max' => 200,
                    'step' => 10,
                    'unit' => 'px',
                    'default' => 60,
                ],
            ],
        ];
    }

    /**
     * Get all available schemas
     */
    public static function getAllSchemas(): array
    {
        return [
            'hero' => self::getHeroSchema(),
            'products' => self::getProductGridSchema(),
            'featured_products' => self::getProductGridSchema(),
            'text' => self::getTextBlockSchema(),
            'content_block' => self::getTextBlockSchema(),
        ];
    }

    /**
     * Get default settings for a section type
     */
    public static function getDefaults(string $type): array
    {
        $schemas = self::getAllSchemas();
        $schema = $schemas[$type] ?? [];
        
        $defaults = [];
        foreach ($schema as $group => $fields) {
            foreach ($fields as $key => $config) {
                $defaults[$key] = $config['default'] ?? null;
            }
        }
        
        return $defaults;
    }
}
