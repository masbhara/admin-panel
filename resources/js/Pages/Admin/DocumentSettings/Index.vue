<template>
  <AdminLayout>
    <Head title="Pengaturan Dokumen" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
          Pengaturan Waktu Pengumpulan Dokumen
        </h2>

        <form @submit.prevent="submit" class="space-y-6">
          <!-- Batas Waktu -->
          <div>
            <Input
              v-model="form.submission_deadline"
              type="datetime-local"
              label="Batas Waktu Pengumpulan"
              required
              :error="form.errors.submission_deadline"
            />
          </div>

          <!-- Pesan Ketika Ditutup -->
          <div>
            <Textarea
              v-model="form.closed_message"
              label="Pesan Ketika Ditutup"
              required
              :error="form.errors.closed_message"
              placeholder="Masukkan pesan yang akan ditampilkan ketika waktu pengumpulan telah berakhir"
              rows="4"
            />
          </div>

          <!-- Status Aktif -->
          <div>
            <Checkbox
              v-model="form.is_active"
              label="Aktifkan Pengumpulan Dokumen"
              :error="form.errors.is_active"
              description="Jika dinonaktifkan, form pengumpulan akan ditutup terlepas dari batas waktu"
            />
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end">
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              :disabled="form.processing"
            >
              <span v-if="form.processing">Menyimpan...</span>
              <span v-else>Simpan Pengaturan</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Input from '@/Components/Forms/Input.vue';
import Textarea from '@/Components/Forms/Textarea.vue';
import Checkbox from '@/Components/Forms/Checkbox.vue';

const props = defineProps({
  settings: {
    type: Object,
    required: true
  }
});

const form = useForm({
  submission_deadline: props.settings.submission_deadline || '',
  closed_message: props.settings.closed_message || 'Maaf, waktu pengumpulan dokumen telah berakhir.',
  is_active: props.settings.is_active ?? true
});

const submit = () => {
  form.put(route('admin.document-settings.update'));
};
</script> 