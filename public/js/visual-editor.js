/**
 * Visual Page Builder - Elementor-like editing experience
 * Handles hover overlays, edit buttons, and communication with parent customizer
 */

class VisualEditor {
    constructor() {
        this.activeOverlay = null;
        this.selectedElement = null;
        this.overlays = new Map();
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        console.log('🎨 Visual Editor initialized');

        // Find all editable sections
        this.attachEditControls();

        // Listen for updates from parent customizer
        this.setupMessageListener();

        // Prevent text selection during edit mode
        document.body.style.userSelect = 'none';
        document.body.style.cursor = 'default';
    }

    attachEditControls() {
        const sections = document.querySelectorAll('[data-section-id]');

        sections.forEach((section, index) => {
            // Store index as data attribute for easy access
            section.setAttribute('data-section-index', index);

            // Add hover listeners
            section.addEventListener('mouseenter', (e) => this.handleMouseEnter(e, section, index));
            section.addEventListener('mouseleave', (e) => this.handleMouseLeave(e, section));

            // Add click listener for selection
            section.addEventListener('click', (e) => this.handleClick(e, section, index));
        });

        console.log(`✅ Attached edit controls to ${sections.length} sections`);
    }

    handleMouseEnter(e, section, index) {
        e.stopPropagation();

        // Don't show overlay if this section is already selected
        if (this.selectedElement === section) return;

        this.showOverlay(section, index);
    }

    handleMouseLeave(e, section) {
        e.stopPropagation();

        // Don't hide overlay if this section is selected
        if (this.selectedElement === section) return;

        this.hideOverlay(section);
    }

    handleClick(e, section, index) {
        e.preventDefault();
        e.stopPropagation();

        this.selectElement(section, index);
    }

    showOverlay(section, index) {
        // Create overlay if it doesn't exist
        if (!this.overlays.has(section)) {
            const overlay = this.createOverlay(section, index);
            this.overlays.set(section, overlay);
        }

        const overlay = this.overlays.get(section);
        this.positionOverlay(overlay, section);
        overlay.style.display = 'block';
        this.activeOverlay = overlay;
    }

    hideOverlay(section) {
        const overlay = this.overlays.get(section);
        if (overlay && this.selectedElement !== section) {
            overlay.style.display = 'none';
        }
    }

    createOverlay(section, index) {
        const overlay = document.createElement('div');
        overlay.className = 've-overlay';
        overlay.innerHTML = `
            <div class="ve-toolbar">
                <span class="ve-label">${this.getSectionLabel(section)}</span>
                <button class="ve-edit-btn" title="Edit Section">
                    <i class="fas fa-pencil-alt"></i>
                </button>
            </div>
        `;

        // Add click handler to edit button
        const editBtn = overlay.querySelector('.ve-edit-btn');
        editBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.openEditor(index, section);
        });

        document.body.appendChild(overlay);
        return overlay;
    }

    positionOverlay(overlay, section) {
        const rect = section.getBoundingClientRect();

        overlay.style.top = `${rect.top + window.scrollY}px`;
        overlay.style.left = `${rect.left + window.scrollX}px`;
        overlay.style.width = `${rect.width}px`;
        overlay.style.height = `${rect.height}px`;
    }

    getSectionLabel(section) {
        const type = section.getAttribute('data-section-type') || 'Section';
        const title = section.querySelector('[data-element-type="title"]')?.textContent?.trim();

        if (title && title.length > 0) {
            return `${this.formatType(type)}: ${this.truncate(title, 30)}`;
        }

        return this.formatType(type);
    }

    formatType(type) {
        return type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    truncate(str, length) {
        return str.length > length ? str.substring(0, length) + '...' : str;
    }

    selectElement(section, index) {
        // Remove previous selection
        if (this.selectedElement) {
            this.selectedElement.classList.remove('ve-selected');
        }

        // Add selection to new element
        section.classList.add('ve-selected');
        this.selectedElement = section;

        // Keep overlay visible
        const overlay = this.overlays.get(section);
        if (overlay) {
            overlay.classList.add('ve-overlay-selected');
        }

        // Open editor in parent customizer
        this.openEditor(index, section);
    }

    openEditor(index, section) {
        const sectionType = section.getAttribute('data-section-type');

        console.log(`📝 Opening editor for section ${index} (${sectionType})`);

        // Send message to parent customizer
        if (window.parent !== window) {
            window.parent.postMessage({
                type: 'editSection',
                index: index,
                sectionType: sectionType
            }, '*');
        }
    }

    setupMessageListener() {
        window.addEventListener('message', (event) => {
            if (event.data.type === 'updateContent') {
                this.applyContentUpdate(event.data);
            } else if (event.data.type === 'highlightSection') {
                this.highlightSection(event.data.index);
            }
        });
    }

    applyContentUpdate(data) {
        const section = document.querySelector(`[data-section-index="${data.index}"]`);
        if (!section) return;

        console.log('🔄 Applying live update to section', data.index);

        // Update title
        if (data.title !== undefined) {
            const titleEl = section.querySelector('[data-element-type="title"]');
            if (titleEl) {
                titleEl.textContent = data.title;
            }
        }

        // Update content/subtitle
        if (data.content !== undefined) {
            const contentEl = section.querySelector('[data-element-type="content"]');
            if (contentEl) {
                contentEl.textContent = data.content;
            }
        }

        // Update button text
        if (data.settings?.button_text !== undefined) {
            const btnEl = section.querySelector('[data-element-type="button"]');
            if (btnEl) {
                btnEl.textContent = data.settings.button_text;
            }
        }

        // Update background image
        if (data.settings?.background_image !== undefined) {
            section.style.backgroundImage = `url('${data.settings.background_image}')`;
        }

        // Update background color
        if (data.settings?.background_color !== undefined) {
            section.style.backgroundColor = data.settings.background_color;
        }

        // Reposition overlay if visible
        const overlay = this.overlays.get(section);
        if (overlay && overlay.style.display === 'block') {
            this.positionOverlay(overlay, section);
        }
    }

    highlightSection(index) {
        const section = document.querySelector(`[data-section-index="${index}"]`);
        if (section) {
            this.selectElement(section, index);
            section.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Reposition overlays on scroll/resize
    handleReposition() {
        this.overlays.forEach((overlay, section) => {
            if (overlay.style.display === 'block') {
                this.positionOverlay(overlay, section);
            }
        });
    }
}

// Initialize when in edit mode
if (window.location.search.includes('edit_mode=1')) {
    const editor = new VisualEditor();

    // Reposition overlays on scroll and resize
    let repositionTimeout;
    window.addEventListener('scroll', () => {
        clearTimeout(repositionTimeout);
        repositionTimeout = setTimeout(() => editor.handleReposition(), 50);
    });

    window.addEventListener('resize', () => {
        clearTimeout(repositionTimeout);
        repositionTimeout = setTimeout(() => editor.handleReposition(), 100);
    });
}
