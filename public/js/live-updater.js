/**
 * Live Updater - Real-time content synchronization
 * Listens for updates from parent customizer and applies them instantly
 */

class LiveUpdater {
    constructor() {
        this.init();
    }

    init() {
        console.log('🔄 Live Updater initialized');
        this.setupMessageListener();
    }

    setupMessageListener() {
        window.addEventListener('message', (event) => {
            const data = event.data;

            switch (data.type) {
                case 'updateContent':
                    this.applyContentUpdate(data);
                    break;
                case 'updateStyles':
                    // Handle batch update from page-builder.js
                    if (data.sections && Array.isArray(data.sections)) {
                        this.applyBatchUpdate(data);
                    } else if (data.index !== undefined) {
                        // Handle individual section update
                        this.applyStyleUpdate(data);
                    }
                    break;
                case 'highlightSection':
                    this.highlightSection(data.index);
                    break;
            }
        });
    }

    /**
     * Apply batch updates from page-builder.js
     */
    applyBatchUpdate(data) {
        console.log('🔄 Applying batch update for', data.sections.length, 'sections');

        // Apply CSS if provided
        if (data.css) {
            this.injectCSS(data.css);
        }

        // Update each section's content
        data.sections.forEach((section, index) => {
            const sectionEl = document.querySelector(`[data-section-index="${index}"]`);
            if (!sectionEl) return;

            // Update text content (title, subtitle, etc.)
            this.updateSectionContent(sectionEl, section);

            // Update inline styles
            this.updateSectionStyles(sectionEl, section.settings);
        });
    }

    /**
     * Update section text content
     */
    updateSectionContent(sectionEl, section) {
        // Update title
        if (section.title !== undefined) {
            const titleEl = sectionEl.querySelector('[data-element-type="title"]');
            if (titleEl && titleEl.textContent !== section.title) {
                titleEl.textContent = section.title;
                this.flashElement(titleEl);
            }
        }

        // Update content/subtitle
        if (section.content !== undefined) {
            const contentEl = sectionEl.querySelector('[data-element-type="content"]');
            if (contentEl && contentEl.textContent !== section.content) {
                contentEl.textContent = section.content;
                this.flashElement(contentEl);
            }
        }

        // Update button text
        if (section.settings?.button_text !== undefined) {
            const btnEl = sectionEl.querySelector('[data-element-type="button"]');
            if (btnEl && btnEl.textContent !== section.settings.button_text) {
                btnEl.textContent = section.settings.button_text;
                this.flashElement(btnEl);
            }
        }

        // Update button link
        if (section.settings?.button_link !== undefined) {
            const btnEl = sectionEl.querySelector('[data-element-type="button"]');
            if (btnEl) {
                btnEl.href = section.settings.button_link;
            }
        }
    }

    /**
     * Update section inline styles
     */
    updateSectionStyles(sectionEl, settings) {
        if (!settings) return;

        // Background image
        if (settings.background_image !== undefined) {
            if (settings.background_image) {
                sectionEl.style.backgroundImage = `url('${settings.background_image}')`;
                sectionEl.style.backgroundSize = settings.background_size || 'cover';
                sectionEl.style.backgroundPosition = `${settings.background_position_x || 50}% ${settings.background_position_y || 50}%`;
            } else {
                sectionEl.style.backgroundImage = 'none';
            }
        }
    }

    /**
     * Inject CSS into page
     */
    injectCSS(css) {
        let styleEl = document.getElementById('live-updater-styles');
        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = 'live-updater-styles';
            document.head.appendChild(styleEl);
        }
        styleEl.textContent = css;
    }

    applyContentUpdate(data) {
        const section = document.querySelector(`[data-section-index="${data.index}"]`);
        if (!section) {
            console.warn('Section not found:', data.index);
            return;
        }

        console.log('📝 Updating content for section', data.index);

        // Update title
        if (data.title !== undefined) {
            const titleEl = section.querySelector('[data-element-type="title"]');
            if (titleEl) {
                titleEl.textContent = data.title;
                this.flashElement(titleEl);
            }
        }

        // Update content/subtitle
        if (data.content !== undefined) {
            const contentEl = section.querySelector('[data-element-type="content"]');
            if (contentEl) {
                contentEl.textContent = data.content;
                this.flashElement(contentEl);
            }
        }

        // Update button text
        if (data.settings?.button_text !== undefined) {
            const btnEl = section.querySelector('[data-element-type="button"]');
            if (btnEl) {
                btnEl.textContent = data.settings.button_text;
                this.flashElement(btnEl);
            }
        }

        // Update button link
        if (data.settings?.button_link !== undefined) {
            const btnEl = section.querySelector('[data-element-type="button"]');
            if (btnEl) {
                btnEl.href = data.settings.button_link;
            }
        }
    }

    applyStyleUpdate(data) {
        const section = document.querySelector(`[data-section-index="${data.index}"]`);
        if (!section) return;

        console.log('🎨 Updating styles for section', data.index);

        const settings = data.settings;

        // Background color
        if (settings.background_color !== undefined) {
            section.style.backgroundColor = settings.background_color;
        }

        // Background image
        if (settings.background_image !== undefined) {
            if (settings.background_image) {
                section.style.backgroundImage = `url('${settings.background_image}')`;
                section.style.backgroundSize = settings.background_size || 'cover';
                section.style.backgroundPosition = `${settings.background_position_x || 50}% ${settings.background_position_y || 50}%`;
            } else {
                section.style.backgroundImage = 'none';
            }
        }

        // Title color
        if (settings.title_color !== undefined) {
            const titleEl = section.querySelector('[data-element-type="title"]');
            if (titleEl) {
                titleEl.style.color = settings.title_color;
            }
        }

        // Title font size (desktop)
        if (settings.title_font_size_desktop !== undefined) {
            const titleEl = section.querySelector('[data-element-type="title"]');
            if (titleEl && window.innerWidth >= 768) {
                titleEl.style.fontSize = `${settings.title_font_size_desktop}px`;
            }
        }

        // Title font weight
        if (settings.title_font_weight !== undefined) {
            const titleEl = section.querySelector('[data-element-type="title"]');
            if (titleEl) {
                titleEl.style.fontWeight = settings.title_font_weight;
            }
        }

        // Text alignment
        if (settings.title_text_align_desktop !== undefined) {
            const contentContainer = section.querySelector('[data-element-type="title"]')?.parentElement;
            if (contentContainer && window.innerWidth >= 768) {
                contentContainer.style.textAlign = settings.title_text_align_desktop;
            }
        }

        // Overlay
        this.updateOverlay(section, settings);

        // Min height
        if (settings.min_height_desktop !== undefined && window.innerWidth >= 768) {
            section.style.minHeight = `${settings.min_height_desktop}px`;
        }

        // Padding
        this.updatePadding(section, settings);
    }

    updateOverlay(section, settings) {
        let overlay = section.querySelector('.hero-overlay');

        if (settings.overlay_opacity !== undefined || settings.overlay_color !== undefined) {
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'hero-overlay absolute inset-0 transition-all duration-300';
                overlay.style.pointerEvents = 'none';
                section.insertBefore(overlay, section.firstChild);
            }

            const color = settings.overlay_color || '#000000';
            const opacity = (settings.overlay_opacity ?? 50) / 100;

            overlay.style.backgroundColor = color;
            overlay.style.opacity = opacity;
        }
    }

    updatePadding(section, settings) {
        const isDesktop = window.innerWidth >= 768;

        if (isDesktop) {
            if (settings.padding_top_desktop !== undefined) {
                section.style.paddingTop = `${settings.padding_top_desktop}px`;
            }
            if (settings.padding_bottom_desktop !== undefined) {
                section.style.paddingBottom = `${settings.padding_bottom_desktop}px`;
            }
            if (settings.padding_left_desktop !== undefined) {
                section.style.paddingLeft = `${settings.padding_left_desktop}px`;
            }
            if (settings.padding_right_desktop !== undefined) {
                section.style.paddingRight = `${settings.padding_right_desktop}px`;
            }
        } else {
            if (settings.padding_top_mobile !== undefined) {
                section.style.paddingTop = `${settings.padding_top_mobile}px`;
            }
            if (settings.padding_bottom_mobile !== undefined) {
                section.style.paddingBottom = `${settings.padding_bottom_mobile}px`;
            }
            if (settings.padding_left_mobile !== undefined) {
                section.style.paddingLeft = `${settings.padding_left_mobile}px`;
            }
            if (settings.padding_right_mobile !== undefined) {
                section.style.paddingRight = `${settings.padding_right_mobile}px`;
            }
        }
    }

    flashElement(element) {
        element.style.transition = 'background-color 0.3s ease';
        const originalBg = element.style.backgroundColor;
        element.style.backgroundColor = 'rgba(99, 102, 241, 0.2)';

        setTimeout(() => {
            element.style.backgroundColor = originalBg;
        }, 300);
    }

    highlightSection(index) {
        // Remove previous highlights
        document.querySelectorAll('.ve-selected').forEach(el => {
            el.classList.remove('ve-selected');
        });

        // Add highlight to target section
        const section = document.querySelector(`[data-section-index="${index}"]`);
        if (section) {
            section.classList.add('ve-selected');
            section.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}

// Initialize when in edit mode
if (window.location.search.includes('edit_mode=1')) {
    new LiveUpdater();
}
