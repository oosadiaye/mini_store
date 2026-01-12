<div 
    x-show="isEditMode"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-y-full opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-full opacity-0"
    class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-full shadow-2xl z-[100] flex items-center gap-6 border border-gray-700"
    style="display: none;"
>
    <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full" :class="isDirty ? 'bg-yellow-400 animate-pulse' : 'bg-green-400'"></span>
        <span class="font-bold text-sm tracking-wide">CMS EDITOR</span>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-3">
        <button 
            @click="toggleEditMode()" 
            class="text-xs text-gray-400 hover:text-white transition"
        >
            Exit
        </button>
        
        <div class="h-4 w-px bg-gray-700"></div>

        <button 
            @click="discardChanges()" 
            x-show="isDirty"
            class="text-xs text-red-400 hover:text-red-300 transition"
        >
            Discard
        </button>
        
        <button 
            @click="saveChanges()"
            :disabled="!isDirty"
            class="bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed text-white px-5 py-1.5 rounded-full text-sm font-bold transition-all shadow-lg flex items-center gap-2"
        >
            <span x-text="isDirty ? 'Save Changes' : 'Saved'"></span>
        </button>
    </div>
</div>
