<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cmsEditor', (initialConfig = {}) => ({
            isEditMode: false,
            isDirty: false,
            config: initialConfig,
            originalConfig: JSON.parse(JSON.stringify(initialConfig)),
            
            init() {
                console.log('CMS Editor Initialized');
            },

            toggleEditMode() {
                this.isEditMode = !this.isEditMode;
            },

            // The JSON Patcher Utility
            updateField(path, value) {
                this.isDirty = true;
                
                // Deep set functionality
                const keys = path.replace(/\[(\d+)\]/g, '.$1').split('.');
                let current = this.config;
                
                for (let i = 0; i < keys.length - 1; i++) {
                    let key = keys[i];
                    if (!current[key]) current[key] = {};
                    current = current[key];
                }
                
                current[keys[keys.length - 1]] = value;
                
                console.log('Updated path:', path, 'to:', value);
            },

            async saveChanges() {
                // Send to backend
                try {
                    const response = await fetch('/admin/cms/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ theme_settings: this.config })
                    });
                    
                    if (response.ok) {
                        this.isDirty = false;
                        this.originalConfig = JSON.parse(JSON.stringify(this.config));
                        alert('Changes saved successfully!');
                    } else {
                        alert('Failed to save changes.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error saving changes.');
                }
            },
            
            discardChanges() {
                this.config = JSON.parse(JSON.stringify(this.originalConfig));
                this.isDirty = false;
                window.location.reload(); // Simple reload to reset DOM state for now
            }
        }));
    });
</script>
