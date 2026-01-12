/**
 * Editable UI Component
 * Usage: <div x-data="editable('hero.title', 'text')">...</div>
 */
export default (path, type = 'text') => ({
    path: path,
    type: type,
    isEditing: false,
    originalContent: '',

    init() {
        // Get initial content from DOM or Store
        // We assume the innerText is the initial value if not explicitly passed
        this.originalContent = this.$el.innerText.trim();
    },

    get isEditMode() {
        return Alpine.store('editor').isEditMode;
    },

    get content() {
        // Retrieve value from global store using path
        // For simplicity, we navigate draftConfig
        // Real implementation might need a 'get' utility if paths are deep
        // But for now, let's rely on correct data binding or just return what we have

        // Actually, to display the *current* draft value, we need to read from store
        // But reading deep paths dynamically in Alpine can be tricky without a helper.
        // For this MVP, we will rely on the element's existing content for display 
        // and only update store on save.
        return this.$el.innerText;
    },

    startEditing() {
        if (!this.isEditMode) return;
        this.isEditing = true;
        this.$nextTick(() => {
            if (this.$refs.input) this.$refs.input.focus();
        });
    },

    save() {
        const newValue = this.$refs.input.value;
        this.isEditing = false;

        // Update Store
        Alpine.store('editor').updateField(this.path, newValue);

        // Optimistically update DOM (optional, as Store logic might re-render parent)
        // ensure we don't duplicate logic if parent is re-rendering
    },

    cancel() {
        this.isEditing = false;
    }
});
