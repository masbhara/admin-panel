<template>
  <AdminLayout title="Buat Form Dokumen">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Buat Form Dokumen
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
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
                <Textarea
                  id="description"
                  class="mt-1 block w-full"
                  v-model="form.description"
                  rows="3"
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>

              <!-- Slug -->
              <div class="mb-4">
                <InputLabel for="slug" value="Slug URL (opsional)" />
                <TextInput
                  id="slug"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.slug"
                  placeholder="akan-dibuat-otomatis-dari-judul"
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
                  placeholder="Pengumpulan dokumen telah ditutup."
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
                  Batal
                </Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  Buat Form
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

const form = useForm({
  title: '',
  description: '',
  slug: '',
  submission_deadline: '',
  closed_message: 'Pengumpulan dokumen telah ditutup.',
  is_active: true,
});

const submit = () => {
  form.post(route('admin.document-forms.store'));
};
</script> 