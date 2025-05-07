`<template>
  <div class="w-full max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
      <h3 class="text-xl font-semibold mb-6">Preferensi Pengguna</h3>

      <form @submit.prevent="savePreferences" class="space-y-6">
        <!-- Notifikasi -->
        <div class="space-y-4">
          <h4 class="font-medium text-gray-900">Pengaturan Notifikasi</h4>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <div>
                <Label for="email_notifications">Notifikasi Email</Label>
                <p class="text-sm text-gray-500">Terima notifikasi melalui email</p>
              </div>
              <Switch 
                id="email_notifications"
                v-model="form.notifications.email"
              />
            </div>

            <div class="flex items-center justify-between">
              <div>
                <Label for="browser_notifications">Notifikasi Browser</Label>
                <p class="text-sm text-gray-500">Terima notifikasi di browser</p>
              </div>
              <Switch 
                id="browser_notifications"
                v-model="form.notifications.browser"
              />
            </div>
          </div>
        </div>

        <!-- Tampilan -->
        <div class="space-y-4">
          <h4 class="font-medium text-gray-900">Tampilan</h4>
          <div class="space-y-3">
            <div class="space-y-2">
              <Label for="theme">Tema</Label>
              <Select id="theme" v-model="form.appearance.theme">
                <option value="light">Light</option>
                <option value="dark">Dark</option>
                <option value="system">System</option>
              </Select>
            </div>

            <div class="space-y-2">
              <Label for="density">Kepadatan Tampilan</Label>
              <Select id="density" v-model="form.appearance.density">
                <option value="comfortable">Nyaman</option>
                <option value="compact">Padat</option>
              </Select>
            </div>
          </div>
        </div>

        <!-- Privasi -->
        <div class="space-y-4">
          <h4 class="font-medium text-gray-900">Privasi</h4>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <div>
                <Label for="activity_tracking">Pelacakan Aktivitas</Label>
                <p class="text-sm text-gray-500">Izinkan sistem melacak aktivitas Anda</p>
              </div>
              <Switch 
                id="activity_tracking"
                v-model="form.privacy.activity_tracking"
              />
            </div>

            <div class="flex items-center justify-between">
              <div>
                <Label for="public_profile">Profil Publik</Label>
                <p class="text-sm text-gray-500">Tampilkan profil Anda ke pengguna lain</p>
              </div>
              <Switch 
                id="public_profile"
                v-model="form.privacy.public_profile"
              />
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-4">
          <Button type="button" variant="outline" @click="resetForm">
            Reset
          </Button>
          <Button type="submit" :loading="loading">
            Simpan Preferensi
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'
import { Select } from '@/Components/ui/select'
import { Switch } from '@/Components/ui/switch'
import { useToast } from '@/Components/ui/toast'

const props = defineProps({
  preferences: {
    type: Object,
    required: true
  }
})

const { toast } = useToast()
const loading = ref(false)

const form = useForm({
  notifications: {
    email: props.preferences?.notifications?.email ?? true,
    browser: props.preferences?.notifications?.browser ?? true
  },
  appearance: {
    theme: props.preferences?.appearance?.theme ?? 'light',
    density: props.preferences?.appearance?.density ?? 'comfortable'
  },
  privacy: {
    activity_tracking: props.preferences?.privacy?.activity_tracking ?? true,
    public_profile: props.preferences?.privacy?.public_profile ?? false
  }
})

const savePreferences = async () => {
  loading.value = true
  try {
    await form.patch(route('user.preferences.update'), {
      preserveScroll: true,
      onSuccess: () => {
        toast({
          title: 'Sukses',
          description: 'Preferensi berhasil disimpan'
        })
      }
    })
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.reset()
}
</script>`
