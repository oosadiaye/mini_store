@props(['path', 'type' => 'text', 'placeholder' => 'Edit this text'])

<div x-data="{
    editing: false,
    value: $el.innerText.trim(), // capture initial text if no x-model passed, or use parent binding logic
    
    startEdit() {
        if (!this.editMode) return;
        this.editing = true;
        this.$nextTick(() => {
            if(this.$refs.input) this.$refs.input.focus();
        });
    },
    
    finishEdit() {
        this.editing = false;
        // Call global updater
        this.updateField('{{ $path }}', this.value);
    },
    
    // Computed helper to access global edit mode
    get editMode() {
        return this.isEditMode; // Accessing from parent cmsEditor scope
    }
}" 
class="relative group"
:class="{'cursor-pointer hover:ring-2 hover:ring-blue-400 hover:ring-offset-2 rounded': editMode}"
@click.stop="startEdit()"
>
    <!-- Edit Overlay Hint -->
    <div x-show="editMode && !editing" class="absolute -top-3 -right-3 bg-blue-500 text-white p-1 rounded-full shadow-sm z-50 opacity-0 group-hover:opacity-100 transition-opacity">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
    </div>

    <!-- Display Mode -->
    <div x-show="!editing">
        {{ $slot }}
    </div>

    <!-- Edit Mode -->
    <template x-if="editing">
        <div @click.stop>
            @if($type === 'textarea')
                <textarea 
                    x-ref="input"
                    x-model="value" 
                    @blur="finishEdit()" 
                    @keydown.escape="editing = false"
                    class="w-full bg-white border border-blue-500 rounded p-1 shadow-sm text-gray-900 z-50 relative"
                    rows="3"
                ></textarea>
            @elseif($type === 'image')
                <div class="absolute inset-0 bg-black/50 flex items-center justify-center z-50 rounded">
                   <button class="bg-white text-gray-900 px-3 py-1 rounded text-sm font-bold shadow hover:bg-gray-100">Upload New Image</button>
                   <!-- Image upload implementation would go here -->
                </div>
            @else
                <input 
                    x-ref="input"
                    type="text" 
                    x-model="value" 
                    @blur="finishEdit()" 
                    @keydown.enter="finishEdit()"
                    class="w-full bg-white border border-blue-500 rounded px-1 py-0 shadow-sm text-gray-900 z-50 relative"
                >
            @endif
        </div>
    </template>
</div>
