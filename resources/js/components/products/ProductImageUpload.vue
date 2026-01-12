<template>
  <div class="relative group flex-shrink-0">
    <!-- Image/Placeholder -->
    <img v-if="localImageUrl" :src="localImageUrl" :alt="productName" class="h-10 w-10 md:h-16 md:w-16 rounded object-cover">
    <div v-else class="h-10 w-10 md:h-16 md:w-16 bg-gray-200 rounded flex items-center justify-center text-gray-400">
      <span class="text-xl md:text-2xl">📦</span>
    </div>

    <!-- Drag & Drop Overlay -->
    <div 
      @dragover.prevent="dragOver = true"
      @dragleave="dragOver = false"
      @drop.prevent="handleDrop"
      @click="$refs.fileInput.click()"
      :class="dragOver ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
      class="absolute inset-0 bg-black bg-opacity-60 rounded flex items-center justify-center cursor-pointer transition-opacity duration-200"
    >
      <i class="fas fa-upload text-white text-xs md:text-sm" v-if="!uploading"></i>
      <i class="fas fa-spinner fa-spin text-white text-xs md:text-sm" v-else></i>
    </div>

    <!-- Hidden File Input -->
    <input type="file" ref="fileInput" @change="handleFileSelect" accept="image/*" class="hidden">
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  productId: {
    type: Number,
    required: true
  },
  initialImageUrl: {
    type: String,
    default: null
  },
  productName: {
    type: String,
    default: 'Product Image'
  },
  uploadUrl: {
    type: String,
    required: true
  },
  csrfToken: {
    type: String,
    required: true
  }
});

const localImageUrl = ref(props.initialImageUrl);
const uploading = ref(false);
const dragOver = ref(false);

const handleDrop = (event) => {
  dragOver.value = false;
  const file = event.dataTransfer.files[0];
  if (file && file.type.startsWith('image/')) {
    uploadImage(file);
  }
};

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    uploadImage(file);
  }
};

const uploadImage = async (file) => {
  uploading.value = true;
  const formData = new FormData();
  formData.append('image', file);
  formData.append('_token', props.csrfToken);

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
      localImageUrl.value = data.url;
      // Optional: Emit success event
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
</script>
