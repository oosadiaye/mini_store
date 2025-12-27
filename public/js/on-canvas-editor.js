/**
 * On-Canvas Editor
 * Elementor-style click-to-edit functionality for page builder
 */

class OnCanvasEditor {
    constructor() {
        this.activeElement = null;
        this.activeBlockId = null;
        this.activeField = null;
        this.activeFieldType = null;
        this.init();
    }

    init() {
        // Only initialize in edit mode
        if (!window.location.search.includes('edit_mode=1')) {
            console.log('📝 On-canvas editor: Not in edit mode');
            return;
        }

        console.log('✅ On-canvas editor: Initializing...');
        this.createEditorPanel();
        this.attachClickHandlers();
        this.attachSaveHandler();
        this.addEditorStyles();
    }

    createEditorPanel() {
        const panel = document.createElement('div');
        panel.id = 'on-canvas-editor-panel';
        panel.className = 'hidden fixed right-4 top-20 bg-white rounded-lg shadow-2xl p-6 w-96 z-[9999] border border-gray-200 animate-slideIn';
        panel.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-edit mr-2 text-indigo-600"></i>
                    Edit Content
                </h3>
                <button id="editor-close-btn" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="editor-field-info" class="mb-3 text-sm text-gray-500">
                <!-- Field info will be inserted here -->
            </div>
            
            <div id="editor-field-container">
                <!-- Dynamic field will be inserted here -->
            </div>
            
            <div class="flex gap-2 mt-4">
                <button id="editor-save-btn" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Save
                </button>
                <button id="editor-cancel-btn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        `;
        document.body.appendChild(panel);
    }

    addEditorStyles() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            .animate-slideIn {
                animation: slideIn 0.3s ease-out;
            }
            
            .editable-element {
                position: relative;
                transition: all 0.2s ease;
            }
            
            .editable-element:hover::after {
                content: '✏️ Click to edit';
                position: absolute;
                top: -25px;
                left: 0;
                background: #4F46E5;
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                white-space: nowrap;
                z-index: 1000;
                pointer-events: none;
            }
            
            .editable-element.active {
                outline: 2px solid #4F46E5 !important;
                outline-offset: 2px;
            }
        `;
        document.head.appendChild(style);
    }

    attachClickHandlers() {
        document.querySelectorAll('[data-field]').forEach(el => {
            // Add visual indicator class
            el.classList.add('editable-element');
            el.style.cursor = 'pointer';

            el.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();
                this.openEditor(el);
            });
        });

        console.log(`✅ Attached click handlers to ${document.querySelectorAll('[data-field]').length} editable elements`);
    }

    openEditor(element) {
        this.activeElement = element;
        this.activeField = element.getAttribute('data-field');
        this.activeFieldType = element.getAttribute('data-field-type') || 'text';

        // Find the closest block
        const blockElement = element.closest('[data-block-id]');
        this.activeBlockId = blockElement?.getAttribute('data-block-id');

        if (!this.activeBlockId) {
            console.error('❌ No block ID found for element:', element);
            this.showNotification('❌ Error: No block ID found', 'error');
            return;
        }

        const currentValue = element.textContent.trim();

        // Remove active class from all elements
        document.querySelectorAll('[data-field]').forEach(el => {
            el.classList.remove('active');
        });

        // Add active class to current element
        element.classList.add('active');

        // Build editor field
        const container = document.getElementById('editor-field-container');
        container.innerHTML = this.buildFieldEditor(this.activeFieldType, currentValue);

        // Show field info
        const infoContainer = document.getElementById('editor-field-info');
        infoContainer.innerHTML = `
            <div class="bg-indigo-50 p-2 rounded">
                <strong>Block:</strong> ${this.activeBlockId}<br>
                <strong>Field:</strong> ${this.activeField}
            </div>
        `;

        // Show panel
        const panel = document.getElementById('on-canvas-editor-panel');
        panel.classList.remove('hidden');

        // Focus the input
        setTimeout(() => {
            const input = document.getElementById('editor-input');
            if (input) {
                input.focus();
                if (input.tagName === 'INPUT') {
                    input.select();
                }
            }
        }, 100);

        console.log('📝 Opened editor for:', {
            blockId: this.activeBlockId,
            field: this.activeField,
            type: this.activeFieldType
        });
    }

    buildFieldEditor(fieldType, value) {
        // Escape HTML for security
        const escapedValue = value.replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

        switch (fieldType) {
            case 'textarea':
                return `
                    <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                    <textarea 
                        id="editor-input" 
                        rows="6" 
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                        placeholder="Enter content..."
                    >${value}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Use Shift+Enter for new lines
                    </p>
                `;

            case 'url':
                return `
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                    <input 
                        type="url" 
                        id="editor-input" 
                        value="${escapedValue}" 
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="https://example.com"
                    >
                `;

            case 'text':
            default:
                return `
                    <label class="block text-sm font-medium text-gray-700 mb-2">Text</label>
                    <input 
                        type="text" 
                        id="editor-input" 
                        value="${escapedValue}" 
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Enter text..."
                    >
                `;
        }
    }

    attachSaveHandler() {
        document.getElementById('editor-save-btn').addEventListener('click', () => {
            this.saveBlock();
        });

        document.getElementById('editor-cancel-btn').addEventListener('click', () => {
            this.closeEditor();
        });

        document.getElementById('editor-close-btn').addEventListener('click', () => {
            this.closeEditor();
        });

        // Save on Ctrl+Enter
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'Enter') {
                const panel = document.getElementById('on-canvas-editor-panel');
                if (!panel.classList.contains('hidden')) {
                    this.saveBlock();
                }
            }
        });

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const panel = document.getElementById('on-canvas-editor-panel');
                if (!panel.classList.contains('hidden')) {
                    this.closeEditor();
                }
            }
        });
    }

    async saveBlock() {
        if (!this.activeElement || !this.activeBlockId || !this.activeField) {
            console.error('❌ Missing required data for save');
            return;
        }

        const newValue = document.getElementById('editor-input').value;

        // Show saving state
        const saveBtn = document.getElementById('editor-save-btn');
        const originalHTML = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        saveBtn.disabled = true;

        try {
            const response = await fetch('/admin/theme/save-block', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    block_id: this.activeBlockId,
                    field: this.activeField,
                    value: newValue
                })
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Server error: ${response.status}`);
            }

            const data = await response.json();

            // Update DOM immediately
            this.activeElement.textContent = newValue;

            // Show success feedback
            this.showNotification('✅ Saved successfully!', 'success');

            console.log('✅ Block saved:', {
                blockId: this.activeBlockId,
                field: this.activeField,
                value: newValue
            });

            // Close editor after short delay
            setTimeout(() => {
                this.closeEditor();
            }, 500);

        } catch (error) {
            console.error('❌ Save error:', error);
            this.showNotification(`❌ Save failed: ${error.message}`, 'error');

            // Restore button
            saveBtn.innerHTML = originalHTML;
            saveBtn.disabled = false;
        }
    }

    closeEditor() {
        const panel = document.getElementById('on-canvas-editor-panel');
        panel.classList.add('hidden');

        // Remove active class from all elements
        document.querySelectorAll('[data-field]').forEach(el => {
            el.classList.remove('active');
        });

        this.activeElement = null;
        this.activeField = null;
        this.activeBlockId = null;
        this.activeFieldType = null;

        console.log('📝 Editor closed');
    }

    showNotification(message, type = 'info') {
        // Remove existing toasts
        document.querySelectorAll('.editor-toast').forEach(t => t.remove());

        const toast = document.createElement('div');
        toast.className = `editor-toast fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-[10000] font-medium animate-slideIn ${type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                    'bg-blue-500 text-white'
            }`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize on page load (only in edit mode)
if (window.location.search.includes('edit_mode=1')) {
    document.addEventListener('DOMContentLoaded', () => {
        window.onCanvasEditor = new OnCanvasEditor();
        console.log('✅ On-canvas editor ready');
    });
}
