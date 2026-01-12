<template>
  <div class="bg-white rounded-lg shadow p-8">
    <!-- Upload Zone -->
    <div 
        @dragover.prevent="dragOver = true"
        @dragleave="dragOver = false"
        @drop.prevent="handleDrop"
        @click="$refs.fileInput.click()"
        :class="dragOver ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'"
        class="border-2 border-dashed rounded-lg p-12 text-center cursor-pointer transition-colors duration-200"
    >
        <div v-show="!uploading && files.length === 0">
            <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
            <p class="text-lg font-medium text-gray-700 mb-2">Drag and drop images here</p>
            <p class="text-sm text-gray-500">or click to browse</p>
        </div>
        
        <div v-show="uploading" class="text-center">
            <i class="fas fa-spinner fa-spin text-6xl text-indigo-600 mb-4"></i>
            <p class="text-lg font-medium text-gray-700">Uploading images...</p>
            <p class="text-sm text-gray-500 mt-2">Processing {{ uploadProgress }} of {{ files.length }} images</p>
        </div>
        
        <input type="file" ref="fileInput" @change="handleFileSelect" accept="image/*" multiple class="hidden">
    </div>

    <!-- Preview Grid -->
    <div v-if="files.length > 0 && !uploading" class="mt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Selected Images ({{ files.length }})</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div v-for="(file, index) in files" :key="index" class="relative group">
                <img :src="file.preview" class="w-full h-24 object-cover rounded border">
                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded flex items-center justify-center">
                    <button @click.stop="removeFile(index)" class="text-white hover:text-red-400">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-600 mt-1 truncate">{{ file.name }}</p>
                <p class="text-xs font-mono text-indigo-600">{{ file.sku }}</p>
            </div>
        </div>
        
        <button @click="uploadImages" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-lg transition">
            <i class="fas fa-upload mr-2"></i> Upload {{ files.length }} Images
        </button>
    </div>
  </div>

  <!-- Results -->
  <div v-if="results" class="mt-6 bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Results</h3>
    
    <!-- Summary -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-green-600 font-medium">Matched</p>
            <p class="text-2xl font-bold text-green-700">{{ results.summary.matched || 0 }}</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm text-yellow-600 font-medium">Unmatched</p>
            <p class="text-2xl font-bold text-yellow-700">{{ results.summary.unmatched || 0 }}</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg">
            <p class="text-sm text-red-600 font-medium">Errors</p>
            <p class="text-2xl font-bold text-red-700">{{ results.summary.errors || 0 }}</p>
        </div>
    </div>

    <!-- Matched Images -->
    <div v-if="results.results.matched && results.results.matched.length > 0" class="mb-6">
        <h4 class="font-medium text-green-700 mb-2">✓ Successfully Uploaded</h4>
        <div class="space-y-2">
            <div v-for="item in results.results.matched" :key="item.filename" class="flex items-center justify-between bg-green-50 p-3 rounded">
                <div class="flex items-center">
                    <img :src="item.url" class="w-12 h-12 object-cover rounded mr-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ item.product_name }}</p>
                        <p class="text-xs text-gray-500">SKU: {{ item.sku }} • {{ item.filename }}</p>
                    </div>
                </div>
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Unmatched Images -->
    <div v-if="results.results.unmatched && results.results.unmatched.length > 0" class="mb-6">
        <h4 class="font-medium text-yellow-700 mb-2">⚠ No Matching Product Found</h4>
        <div class="space-y-2">
            <div v-for="item in results.results.unmatched" :key="item.filename" class="flex items-center justify-between bg-yellow-50 p-3 rounded">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ item.filename }}</p>
                    <p class="text-xs text-gray-500">Extracted SKU: {{ item.sku }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
            </div>
        </div>
    </div>

    <!-- Errors -->
    <div v-if="results.results.errors && results.results.errors.length > 0">
        <h4 class="font-medium text-red-700 mb-2">✗ Upload Errors</h4>
        <div class="space-y-2">
            <div v-for="item in results.results.errors" :key="item.filename" class="bg-red-50 p-3 rounded">
                <p class="text-sm font-medium text-gray-900">{{ item.filename }}</p>
                <p class="text-xs text-red-600">{{ item.error }}</p>
            </div>
        </div>
    </div>

    <button @click="reset" class="mt-6 w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 rounded-lg transition">
        Upload More Images
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  uploadUrl: { type: String, required: true },
  csrfToken: { type: String, required: true }
});

const files = ref([]);
const dragOver = ref(false);
const uploading = ref(false);
const uploadProgress = ref(0);
const results = ref(null);
const fileInput = ref(null);

const extractSku = (filename) => {
    const name = filename.replace(/\.[^/.]+$/, '');
    const patterns = [
        /^([A-Z0-9\-_]+)$/i,
        /^([A-Z0-9\-_]+?)[\-_](front|back|side|image|photo|main|\d+)/i,
        /[_\-]([A-Z0-9\-_]+)$/i
    ];
    
    for (let pattern of patterns) {
        const match = name.match(pattern);
        if (match) return match[1].toUpperCase();
    }
    return name.toUpperCase();
};

const addFiles = (newFiles) => {
    const imageFiles = newFiles.filter(f => f.type.startsWith('image/'));
    
    imageFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            files.value.push({
                file: file,
                name: file.name,
                preview: e.target.result,
                sku: extractSku(file.name)
            });
        };
        reader.readAsDataURL(file);
    });
};

const handleDrop = (event) => {
    dragOver.value = false;
    const droppedFiles = Array.from(event.dataTransfer.files);
    addFiles(droppedFiles);
};

const handleFileSelect = (event) => {
    const selectedFiles = Array.from(event.target.files);
    addFiles(selectedFiles);
    // Reset input so same file can be selected again if needed
    event.target.value = '';
};

const removeFile = (index) => {
    files.value.splice(index, 1);
};

const uploadImages = async () => {
    if (files.value.length === 0) return;
    
    uploading.value = true;
    uploadProgress.value = 0;
    
    const formData = new FormData();
    files.value.forEach((fileObj, index) => {
        formData.append(`images[${index}]`, fileObj.file);
    });
    
    // Simulate progress (since Fetch API doesn't support progress events easily without wrapper)
    // Or we just show "Processing count..."
    uploadProgress.value = files.value.length;
    
    try {
        const response = await fetch(props.uploadUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': props.csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            results.value = data;
            files.value = []; // Clear queue
        } else {
             alert('Upload failed: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Upload error:', error);
        alert('Upload failed. Please try again.');
    } finally {
        uploading.value = false;
    }
};

const reset = () => {
    files.value = [];
    results.value = null;
    uploadProgress.value = 0;
};
</script>
