`<template>
  <div class="w-full max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
      <!-- Profile Header -->
      <div class="flex items-center space-x-6 mb-6">
        <div class="relative">
          <img 
            :src="avatarUrl || defaultAvatar" 
            class="w-24 h-24 rounded-full object-cover border-4 border-gray-100"
            alt="User avatar"
          />
          <button 
            @click="$refs.fileInput.click()" 
            class="absolute bottom-0 right-0 bg-primary text-white p-2 rounded-full shadow-lg hover:bg-primary/90"
          >
            <IconCamera class="w-4 h-4" />
          </button>
          <input 
            ref="fileInput"
            type="file"
            class="hidden"
            accept="image/*"
            @change="handleAvatarUpload"
          />
        </div>
        <div>
          <h2 class="text-2xl font-bold text-gray-800">{{ user.name }}</h2>
          <p class="text-gray-600">{{ user.email }}</p>
          <div class="flex space-x-2 mt-2">
            <Badge v-for="role in user.roles" :key="role" :value="role" />
          </div>
        </div>
      </div>

      <!-- Profile Form -->
      <form @submit.prevent="updateProfile" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-2">
            <Label for="name">Nama</Label>
            <Input id="name" v-model="form.name" type="text" />
            <p v-if="errors.name" class="text-sm text-red-500">{{ errors.name }}</p>
          </div>

          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input id="email" v-model="form.email" type="email" />
            <p v-if="errors.email" class="text-sm text-red-500">{{ errors.email }}</p>
          </div>

          <div class="space-y-2">
            <Label for="phone">Nomor Telepon</Label>
            <Input id="phone" v-model="form.phone" type="tel" />
            <p v-if="errors.phone" class="text-sm text-red-500">{{ errors.phone }}</p>
          </div>

          <div class="space-y-2">
            <Label for="language">Bahasa</Label>
            <Select id="language" v-model="form.language">
              <option value="id">Indonesia</option>
              <option value="en">English</option>
            </Select>
          </div>
        </div>

        <div class="flex justify-end space-x-4">
          <Button type="button" variant="outline" @click="resetForm">
            Reset
          </Button>
          <Button type="submit" :loading="loading">
            Simpan Perubahan
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Select } from '@/Components/ui/select'
import { Badge } from '@/Components/ui/badge'
import { IconCamera } from '@/Components/icons'
import { useToast } from '@/Components/ui/toast'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const { toast } = useToast()
const loading = ref(false)
const fileInput = ref(null)
const defaultAvatar = '/images/default-avatar.png'
const avatarUrl = computed(() => props.user.avatar_url)

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  language: props.user.language || 'id',
  avatar: null
})

const handleAvatarUpload = (e) => {
  const file = e.target.files[0]
  if (!file) return

  if (!file.type.includes('image/')) {
    toast({
      title: 'Error',
      description: 'File harus berupa gambar',
      variant: 'destructive'
    })
    return
  }

  form.avatar = file
  uploadAvatar()
}

const uploadAvatar = async () => {
  loading.value = true
  try {
    await form.post(route('user.avatar.update'), {
      preserveScroll: true,
      onSuccess: () => {
        toast({
          title: 'Sukses',
          description: 'Avatar berhasil diperbarui'
        })
      },
      onError: () => {
        toast({
          title: 'Error',
          description: 'Gagal mengupload avatar',
          variant: 'destructive'
        })
      }
    })
  } finally {
    loading.value = false
  }
}

const updateProfile = async () => {
  loading.value = true
  try {
    await form.patch(route('user.profile.update'), {
      preserveScroll: true,
      onSuccess: () => {
        toast({
          title: 'Sukses',
          description: 'Profil berhasil diperbarui'
        })
      }
    })
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.reset()
  form.clearErrors()
}
</script>`
