import { updateConfigByPath } from './utils';
import Alpine from 'alpinejs';

/**
 * CMS Editor Store
 * Manages the global state of the Visual Editor.
 */
export const EditorStore = {
    isEditMode: false,
    liveConfig: {},   // The state currently in DB
    draftConfig: {},  // The state being edited

    init() {
        // Initialize with data injected from backend if available
        // e.g. window.themeConfig
        if (window.themeConfig) {
            this.liveConfig = JSON.parse(JSON.stringify(window.themeConfig));
            this.draftConfig = JSON.parse(JSON.stringify(window.themeConfig));
        }

        // Listen for keyboard shortcuts (Ctrl+E to toggle edit mode)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                e.preventDefault();
                this.toggleEditMode();
            }
        });
    },

    toggleEditMode() {
        this.isEditMode = !this.isEditMode;
        console.log('Edit Mode:', this.isEditMode ? 'ON' : 'OFF');

        // If exiting edit mode, maybe confirm discard? 
        // For now, we just toggle UI.
    },

    updateField(path, value) {
        console.log(`[CMS] Updating ${path} to:`, value);
        this.draftConfig = updateConfigByPath(this.draftConfig, path, value);

        // Dispatch event for components that might not be reactive to deep changes
        window.dispatchEvent(new CustomEvent('cms-update', { detail: { path, value } }));
    },

    saveChanges() {
        // Here we would sync draftConfig to backend via API
        console.log('Saving config:', this.draftConfig);
        alert('Configuration saved! (Mock)');
        this.liveConfig = JSON.parse(JSON.stringify(this.draftConfig));
    },

    resetChanges() {
        if (confirm('Discard unsaved changes?')) {
            this.draftConfig = JSON.parse(JSON.stringify(this.liveConfig));
        }
    },

    get hasUnsavedChanges() {
        return JSON.stringify(this.liveConfig) !== JSON.stringify(this.draftConfig);
    }
};
