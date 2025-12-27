-- Quick fix to populate Hero section with "Refined Retail Reimagined" text
-- Run this in your database to add the content
-- First, check if you have a page_layouts table entry for 'home'
SELECT *
FROM page_layouts
WHERE page_name = 'home';
-- If it exists, update it with the Hero section data:
UPDATE page_layouts
SET sections = '[
    {
        "id": "hero-1",
        "type": "hero",
        "enabled": true,
        "order": 1,
        "title": "Refined Retail Reimagined.",
        "content": "Discover a curated collection of premium essentials designed to elevate your everyday lifestyle.",
        "settings": {
            "button_text": "Start Shopping",
            "button_link": "/products",
            "background_color": "#1a1a2e",
            "overlay_color": "#000000",
            "overlay_opacity": 40,
            "min_height_desktop": 600,
            "min_height_mobile": 400,
            "title_color": "#ffffff",
            "title_font_size_desktop": 64,
            "title_font_size_mobile": 36
        }
    }
]'
WHERE page_name = 'home';
-- If it doesn't exist, create it:
INSERT INTO page_layouts (
        page_name,
        template_id,
        is_active,
        sections,
        created_at,
        updated_at
    )
VALUES (
        'home',
        NULL,
        1,
        '[{"id":"hero-1","type":"hero","enabled":true,"order":1,"title":"Refined Retail Reimagined.","content":"Discover a curated collection of premium essentials designed to elevate your everyday lifestyle.","settings":{"button_text":"Start Shopping","button_link":"/products","background_color":"#1a1a2e","overlay_color":"#000000","overlay_opacity":40,"min_height_desktop":600,"min_height_mobile":400,"title_color":"#ffffff","title_font_size_desktop":64,"title_font_size_mobile":36}}]',
        NOW(),
        NOW()
    );