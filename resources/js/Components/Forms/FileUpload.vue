<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Drop zone -->
    <div
      :class="[
        'flex justify-center rounded-lg border-2 border-dashed px-6 py-10',
        isDragOver ? 'border-primary-500 bg-primary-50' : 'border-gray-300',
        error ? 'border-red-300' : '',
        className
      ]"
      @dragover.prevent="handleDragOver"
      @dragleave.prevent="handleDragLeave"
      @drop.prevent="handleDrop"
    >
      <div class="text-center">
        <!-- Upload icon -->
        <DocumentArrowUpIcon v-if="!preview" class="mx-auto h-12 w-12 text-gray-400" />
        
        <!-- Preview image -->
        <img
          v-else-if="preview && isImage"
          :src="preview"
          class="mx-auto h-32 w-32 object-cover rounded"
          :alt="selectedFile?.name"
        />

        <!-- Preview document icon -->
        <DocumentIcon
          v-else
          class="mx-auto h-12 w-12 text-gray-400"
        />

        <!-- Upload button -->
        <div class="mt-4 flex items-center justify-center text-sm leading-6 text-gray-600">
          <label
            :for="id"
            class="relative cursor-pointer rounded-md font-semibold text-primary-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-primary-600 focus-within:ring-offset-2 hover:text-primary-500"
          >
            <span v-if="!selectedFile">Upload file</span>
            <span v-else>Ganti file</span>
            <input
              :id="id"
              type="file"
              class="sr-only"
              :accept="accept"
              :multiple="multiple"
              :disabled="disabled"
              @change="handleFileSelect"
            >
          </label>
          <p v-if="!selectedFile" class="pl-1">atau drag and drop</p>
        </div>

        <!-- File info -->
        <div v-if="selectedFile" class="mt-2 text-sm text-gray-600">
          {{ selectedFile.name }} ({{ formatFileSize(selectedFile.size) }})
          <button
            type="button"
            class="ml-2 text-red-600 hover:text-red-800"
            @click="removeFile"
          >
            Hapus
          </button>
        </div>

        <!-- Help text -->
        <p class="text-xs leading-5 text-gray-600 mt-2">
          {{ acceptedFileTypes }} hingga {{ formatFileSize(maxSize) }}
        </p>
      </div>
    </div>

    <!-- Error message -->
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <p v-if="hint && !error" class="mt-1 text-sm text-gray-500">{{ hint }}</p>

    <!-- Progress bar -->
    <div v-if="progress > 0" class="mt-2">
      <div class="h-2 bg-gray-200 rounded-full">
        <div
          class="h-2 bg-primary-600 rounded-full transition-all duration-300"
          :style="{ width: `${progress}%` }"
        ></div>
      </div>
      <p class="mt-1 text-xs text-gray-600">{{ progress }}% uploaded</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { DocumentArrowUpIcon, DocumentIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: {
    type: [File, Array],
    default: null
  },
  label: {
    type: String,
    default: ''
  },
  accept: {
    type: String,
    default: '*/*'
  },
  multiple: {
    type: Boolean,
    default: false
  },
  maxSize: {
    type: Number,
    default: 5 * 1024 * 1024 // 5MB
  },
  error: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  progress: {
    type: Number,
    default: 0
  },
  className: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue', 'error'])
const id = computed(() => `file-upload-${Math.random().toString(36).substr(2, 9)}`)
const isDragOver = ref(false)
const selectedFile = ref(null)
const preview = ref(null)

// Computed properties
const acceptedFileTypes = computed(() => {
  if (props.accept === '*/*') return 'Semua tipe file'
  return props.accept.split(',').map(type => type.trim()).join(', ')
})

const isImage = computed(() => {
  return selectedFile.value?.type.startsWith('image/')
})

// Methods
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const validateFile = (file) => {
  // Check file size
  if (file.size > props.maxSize) {
    emit('error', `File terlalu besar. Maksimal ${formatFileSize(props.maxSize)}`)
    return false
  }

  // Check file type
  if (props.accept !== '*/*') {
    const acceptedTypes = props.accept.split(',').map(type => type.trim())
    const fileType = file.type
    const fileExtension = '.' + file.name.split('.').pop()
    
    if (!acceptedTypes.some(type => {
      return fileType.match(type.replace('*', '.*')) || fileExtension === type
    })) {
      emit('error', 'Tipe file tidak didukung')
      return false
    }
  }

  return true
}

const handleFileSelect = (event) => {
  const files = event.target.files
  if (!files.length) return

  const file = files[0]
  if (!validateFile(file)) {
    event.target.value = null
    return
  }

  selectedFile.value = file
  emit('update:modelValue', props.multiple ? Array.from(files) : file)
  
  // Create preview for images
  if (file.type.startsWith('image/')) {
    const reader = new FileReader()
    reader.onload = (e) => {
      preview.value = e.target.result
    }
    reader.readAsDataURL(file)
  } else {
    preview.value = null
  }
}

const handleDragOver = (event) => {
  isDragOver.value = true
}

const handleDragLeave = (event) => {
  isDragOver.value = false
}

const handleDrop = (event) => {
  isDragOver.value = false
  const files = event.dataTransfer.files
  if (!files.length) return

  const file = files[0]
  if (!validateFile(file)) return

  selectedFile.value = file
  emit('update:modelValue', props.multiple ? Array.from(files) : file)

  // Create preview for images
  if (file.type.startsWith('image/')) {
    const reader = new FileReader()
    reader.onload = (e) => {
      preview.value = e.target.result
    }
    reader.readAsDataURL(file)
  } else {
    preview.value = null
  }
}

const removeFile = () => {
  selectedFile.value = null
  preview.value = null
  emit('update:modelValue', props.multiple ? [] : null)
}
</script>
