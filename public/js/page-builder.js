/**
 * Advanced Page Builder Alpine.js Component
 * Handles real-time preview, section management, and AJAX uploads
 */

function advancedPageBuilder() {
    return {
        // State
        sections: [],
        availableSections: [],
        editingSection: null,
        editingIndex: null,
        sidebarTab: 'layers',
        device: 'desktop',
        uploading: false,
        previewTimeout: null,

        /**
         * Initialize the page builder
         */
        init() {
            // Load initial data from server
            this.sections = window.initialSections || [];
            this.availableSections = window.availableSections || [];

            // Setup sortable for layers
            this.initSortable();

            // Listen for messages from iframe
            this.setupIframeListener();

            // Watch for section changes
            this.$watch('sections', () => {
                this.debouncedPreviewUpdate();
            }, { deep: true });

            console.log('Page Builder initialized with', this.sections.length, 'sections');
        },

        /**
         * Initialize SortableJS for drag-and-drop
         */
        initSortable() {
            const layoutEl = document.getElementById('current-layout');
            if (!layoutEl) return;

            Sortable.create(layoutEl, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'opacity-50',
                onEnd: (evt) => {
                    const item = this.sections.splice(evt.oldIndex, 1)[0];
                    this.sections.splice(evt.newIndex, 0, item);
                    this.updateOrder();
                    this.updatePreview();
                }
            });
        },

        /**
         * Setup PostMessage listener for iframe communication
         */
        setupIframeListener() {
            window.addEventListener('message', (event) => {
                if (event.data.type === 'sectionSelected') {
                    if (typeof event.data.index === 'number') {
                        this.configureSection(event.data.index);
                    }
                }
            });
        },

        /**
         * Add a new section
         */
        addSection(type) {
            const newSection = {
                id: type + '-' + Date.now(),
                type: type,
                enabled: true,
                order: this.sections.length + 1,
                settings: this.getDefaultSettings(type),
                title: '',
                content: ''
            };

            this.sections.push(newSection);
            this.sidebarTab = 'layers';
            this.updateOrder();

            // Auto-open for editing
            setTimeout(() => {
                this.configureSection(this.sections.length - 1);
            }, 100);
        },

        /**
         * Get default settings for a section type
         */
        getDefaultSettings(type) {
            const defaults = {
                hero: {
                    min_height_desktop: 600,
                    min_height_mobile: 400,
                    title_font_size_desktop: 64,
                    title_font_size_mobile: 36,
                    title_font_weight: 700,
                    title_color: '#ffffff',
                    title_text_align_desktop: 'center',
                    title_text_align_mobile: 'center',
                    padding_top_desktop: 120,
                    padding_bottom_desktop: 120,
                    padding_left_desktop: 40,
                    padding_right_desktop: 40,
                    padding_top_mobile: 60,
                    padding_bottom_mobile: 60,
                    padding_left_mobile: 20,
                    padding_right_mobile: 20,
                    background_color: '#000000',
                    overlay_color: '#000000',
                    overlay_opacity: 40,
                    background_size: 'cover',
                    background_position_x: 50,
                    background_position_y: 50,
                },
                products: {
                    limit: 8,
                    columns_desktop: 4,
                    columns_tablet: 3,
                    columns_mobile: 2,
                    gap_desktop: 24,
                    gap_mobile: 16,
                    padding_top: 80,
                    padding_bottom: 80,
                },
                text: {
                    font_size_desktop: 16,
                    font_size_mobile: 14,
                    line_height: 1.6,
                    text_color: '#333333',
                    text_align_desktop: 'left',
                    text_align_mobile: 'left',
                    max_width: 800,
                    padding_top: 60,
                    padding_bottom: 60,
                }
            };

            return defaults[type] || {};
        },

        /**
         * Remove a section
         */
        removeSection(index) {
            if (confirm('Remove this section?')) {
                this.sections.splice(index, 1);
                this.closeSettings();
                this.updateOrder();
                this.updatePreview();
            }
        },

        /**
         * Configure/edit a section
         */
        configureSection(index) {
            this.editingIndex = index;
            this.editingSection = this.sections[index];
        },

        /**
         * Close settings panel
         */
        closeSettings() {
            this.editingIndex = null;
            this.editingSection = null;
        },

        /**
         * Move section to new position
         */
        moveSection(fromIndex, toIndex) {
            if (isNaN(toIndex)) return;
            toIndex = Math.max(0, Math.min(toIndex, this.sections.length - 1));
            if (fromIndex === toIndex) return;

            const item = this.sections.splice(fromIndex, 1)[0];
            this.sections.splice(toIndex, 0, item);
            this.updateOrder();
            this.updatePreview();
        },

        /**
         * Update section order numbers
         */
        updateOrder() {
            this.sections.forEach((section, index) => {
                section.order = index + 1;
            });
        },

        /**
         * Upload image with instant preview
         */
        async uploadImage(event, field) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB');
                return;
            }

            // Show data URL preview immediately
            const reader = new FileReader();
            reader.onload = (e) => {
                this.editingSection.settings[field] = e.target.result;
                this.updatePreview();
            };
            reader.readAsDataURL(file);

            // Upload to server
            this.uploading = true;
            const formData = new FormData();
            formData.append('image', file);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch('/admin/page-builder/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Replace data URL with server URL
                    this.editingSection.settings[field] = data.url;
                    this.updatePreview();

                    console.log('Image uploaded:', data.dimensions);
                } else {
                    alert('Upload failed: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Upload failed. Please try again.');
            } finally {
                this.uploading = false;
            }
        },

        /**
         * Debounced preview update (150ms delay)
         */
        debouncedPreviewUpdate() {
            if (this.previewTimeout) {
                clearTimeout(this.previewTimeout);
            }

            this.previewTimeout = setTimeout(() => {
                this.updatePreview();
            }, 150);
        },

        /**
         * Update preview iframe
         */
        async updatePreview() {
            const iframe = document.getElementById('preview-frame');
            if (!iframe) return;

            try {
                // Generate CSS from current settings
                const css = this.generateCSS();

                // Deep clone sections to avoid DataCloneError with Alpine.js Proxy
                const sectionsClone = JSON.parse(JSON.stringify(this.sections));

                // Send to iframe via PostMessage
                iframe.contentWindow.postMessage({
                    type: 'updateStyles',
                    css: css,
                    sections: sectionsClone,
                    device: this.device
                }, '*');

            } catch (error) {
                console.error('Preview update error:', error);
            }
        },

        /**
         * Generate CSS from section settings
         */
        generateCSS() {
            let css = '';

            this.sections.forEach(section => {
                if (!section.enabled) return;

                const id = section.id;
                const s = section.settings;

                // Check visibility
                if (this.device === 'desktop' && s.hide_on_desktop) {
                    css += `#${id} { display: none !important; }\n`;
                    return;
                }
                if (this.device === 'mobile' && s.hide_on_mobile) {
                    css += `@media (max-width: 768px) { #${id} { display: none !important; } }\n`;
                    return;
                }

                // Generate CSS based on device
                if (this.device === 'desktop') {
                    css += this.generateDesktopCSS(id, s);
                } else {
                    css += this.generateMobileCSS(id, s);
                }
            });

            return css;
        },

        /**
         * Generate desktop CSS
         */
        generateDesktopCSS(id, s) {
            let css = `#${id} {\n`;

            // Dimensions
            if (s.min_height_desktop) css += `  min-height: ${s.min_height_desktop}px;\n`;
            if (s.max_width) css += `  max-width: ${s.max_width}px;\n`;

            // Padding
            const pt = s.padding_top_desktop || s.padding_top || 0;
            const pr = s.padding_right_desktop || s.padding_right || 0;
            const pb = s.padding_bottom_desktop || s.padding_bottom || 0;
            const pl = s.padding_left_desktop || s.padding_left || 0;
            css += `  padding: ${pt}px ${pr}px ${pb}px ${pl}px;\n`;

            // Typography
            if (s.title_font_size_desktop || s.font_size_desktop) {
                css += `  font-size: ${s.title_font_size_desktop || s.font_size_desktop}px;\n`;
            }
            if (s.title_font_weight) css += `  font-weight: ${s.title_font_weight};\n`;
            if (s.title_line_height || s.line_height) {
                css += `  line-height: ${s.title_line_height || s.line_height};\n`;
            }
            if (s.title_letter_spacing) css += `  letter-spacing: ${s.title_letter_spacing}px;\n`;
            if (s.title_color || s.text_color) {
                css += `  color: ${s.title_color || s.text_color};\n`;
            }
            if (s.title_text_align_desktop || s.text_align_desktop) {
                css += `  text-align: ${s.title_text_align_desktop || s.text_align_desktop};\n`;
            }

            // Background
            if (s.background_color) css += `  background-color: ${s.background_color};\n`;
            if (s.background_image) {
                css += `  background-image: url('${s.background_image}');\n`;
                css += `  background-size: ${s.background_size || 'cover'};\n`;
                const posX = s.background_position_x || 50;
                const posY = s.background_position_y || 50;
                css += `  background-position: ${posX}% ${posY}%;\n`;
            }

            css += `}\n`;

            // Overlay
            if (s.overlay_color && s.overlay_opacity > 0) {
                const opacity = s.overlay_opacity / 100;
                css += `#${id}::before {\n`;
                css += `  content: '';\n`;
                css += `  position: absolute;\n`;
                css += `  inset: 0;\n`;
                css += `  background-color: ${s.overlay_color};\n`;
                css += `  opacity: ${opacity};\n`;
                css += `  pointer-events: none;\n`;
                css += `}\n`;
            }

            return css;
        },

        /**
         * Generate mobile CSS
         */
        generateMobileCSS(id, s) {
            let css = `@media (max-width: 768px) {\n`;
            css += `  #${id} {\n`;

            // Dimensions
            if (s.min_height_mobile) css += `    min-height: ${s.min_height_mobile}px;\n`;

            // Padding
            const pt = s.padding_top_mobile || 0;
            const pr = s.padding_right_mobile || 0;
            const pb = s.padding_bottom_mobile || 0;
            const pl = s.padding_left_mobile || 0;
            if (pt || pr || pb || pl) {
                css += `    padding: ${pt}px ${pr}px ${pb}px ${pl}px;\n`;
            }

            // Typography
            if (s.title_font_size_mobile || s.font_size_mobile) {
                css += `    font-size: ${s.title_font_size_mobile || s.font_size_mobile}px;\n`;
            }
            if (s.title_text_align_mobile || s.text_align_mobile) {
                css += `    text-align: ${s.title_text_align_mobile || s.text_align_mobile};\n`;
            }

            css += `  }\n`;
            css += `}\n`;

            return css;
        },

        /**
         * Save layout to server
         */
        async saveLayout() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch('/admin/page-builder/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        page_name: window.pageName || 'home',
                        sections: this.sections
                    })
                });

                if (response.ok) {
                    // Reload iframe to show saved changes
                    document.getElementById('preview-frame').contentWindow.location.reload();
                    alert('Saved successfully!');
                } else {
                    alert('Save failed. Please try again.');
                }
            } catch (error) {
                console.error('Save error:', error);
                alert('Save failed. Please try again.');
            }
        },

        /**
         * Reset layout to defaults
         */
        async resetLayout() {
            if (!confirm('Reset to default layout? This cannot be undone.')) return;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch('/admin/page-builder/reset?page=' + (window.pageName || 'home'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Reset failed. Please try again.');
                }
            } catch (error) {
                console.error('Reset error:', error);
                alert('Reset failed. Please try again.');
            }
        },

        /**
         * Get section icon
         */
        getSectionIcon(type) {
            const icons = {
                hero: '🎨',
                products: '🛍️',
                featured_products: '⭐',
                categories: '📁',
                text: '📝',
                content_block: '📝',
                gallery: '🖼️',
                video: '🎥',
                testimonials: '💬',
                newsletter: '📧',
                custom_html: '💻'
            };
            return icons[type] || '📦';
        },

        /**
         * Get section name
         */
        getSectionName(type) {
            return type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }
    };
}

// Export for global use
window.advancedPageBuilder = advancedPageBuilder;
