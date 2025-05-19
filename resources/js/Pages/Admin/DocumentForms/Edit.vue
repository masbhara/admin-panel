<template>
  <AdminLayout title="Edit Form Dokumen">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Edit Form Dokumen
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Alert Success -->
            <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-md p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-800 dark:text-green-300">
                    {{ $page.props.flash.success }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Alert Error -->
            <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-md p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-500 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-red-800 dark:text-red-300">
                    {{ $page.props.flash.error }}
                  </p>
                </div>
              </div>
            </div>

            <form @submit.prevent="submit">
              <!-- Title -->
              <div class="mb-4">
                <InputLabel for="title" value="Judul Form" />
                <TextInput
                  id="title"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.title"
                  required
                  autofocus
                />
                <InputError :message="form.errors.title" class="mt-2" />
              </div>

              <!-- Description -->
              <div class="mb-4">
                <InputLabel for="description" value="Deskripsi (opsional)" />
                <TextArea
                  id="description"
                  class="mt-1 block w-full"
                  v-model="form.description"
                  rows="3"
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>

              <!-- Slug -->
              <div class="mb-4">
                <InputLabel for="slug" value="Slug URL" />
                <TextInput
                  id="slug"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.slug"
                />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung. Jika dibiarkan kosong, akan dibuat otomatis dari judul.
                </p>
                <InputError :message="form.errors.slug" class="mt-2" />
              </div>

              <!-- Submission Deadline -->
              <div class="mb-4">
                <InputLabel for="submission_deadline" value="Batas Waktu Pengumpulan (opsional)" />
                <TextInput
                  id="submission_deadline"
                  type="datetime-local"
                  class="mt-1 block w-full"
                  v-model="form.submission_deadline"
                />
                <InputError :message="form.errors.submission_deadline" class="mt-2" />
              </div>

              <!-- Closed Message -->
              <div class="mb-4">
                <InputLabel for="closed_message" value="Pesan Ketika Form Ditutup (opsional)" />
                <Textarea
                  id="closed_message"
                  class="mt-1 block w-full"
                  v-model="form.closed_message"
                  rows="2"
                />
                <InputError :message="form.errors.closed_message" class="mt-2" />
              </div>

              <!-- Active Status -->
              <div class="mb-6">
                <div class="flex items-center">
                  <Checkbox id="is_active" v-model="form.is_active" />
                  <InputLabel for="is_active" class="ml-2" value="Aktifkan Form" />
                </div>
                <InputError :message="form.errors.is_active" class="mt-2" />
              </div>

              <!-- Submit Button -->
              <div class="flex items-center justify-end mt-4">
                <Link
                  :href="route('admin.document-forms.index')"
                  class="mr-4 px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:bg-gray-400 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                  Kembali
                </Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  Simpan Perubahan
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import TextArea from '@/Components/TextArea.vue'

const props = defineProps({
  documentForm: Object,
});

const form = useForm({
  title: props.documentForm.title,
  description: props.documentForm.description || '',
  slug: props.documentForm.slug,
  submission_deadline: props.documentForm.formatted_submission_deadline || '',
  closed_message: props.documentForm.closed_message || 'Pengumpulan dokumen telah ditutup.',
  is_active: props.documentForm.is_active,
});

const submit = () => {
  // Gunakan axios.post dengan method spoofing PUT
  const formData = {
    _method: 'PUT',
    title: form.title,
    description: form.description,
    slug: form.slug,
    submission_deadline: form.submission_deadline,
    closed_message: form.closed_message,
    is_active: form.is_active
  };

  axios.post(`/admin/document-forms/${props.documentForm.id}`, formData)
    .then(response => {
      // Redirect ke halaman detail setelah berhasil update
      window.location.href = route('admin.document-forms.show', props.documentForm.id);
    })
    .catch(error => {
      if (error.response && error.response.data && error.response.data.errors) {
        const errors = error.response.data.errors;
        Object.keys(errors).forEach(key => {
          form.errors[key] = errors[key][0];
        });
      } else {
        console.error('Error updating document form:', error);
      }
    });
};
</script> 