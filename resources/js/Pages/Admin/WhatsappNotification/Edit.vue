<template>
  <AdminLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
          <h1 class="text-3xl font-semibold text-gray-900">Edit Template Notifikasi WhatsApp</h1>
          <p class="mt-1 text-sm text-gray-600">
            Perbarui template notifikasi WhatsApp.
          </p>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-4">
                  <InputLabel for="name" value="Nama Template" />
                  <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                  />
                  <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="sm:col-span-4">
                  <InputLabel for="event_type" value="Tipe Event" />
                  <SelectInput
                    id="event_type"
                    class="mt-1 block w-full"
                    v-model="form.event_type"
                    required
                  >
                    <option value="" disabled>Pilih tipe event</option>
                    <option value="document_uploaded">Dokumen Diunggah</option>
                    <option value="document_approved">Dokumen Disetujui</option>
                    <option value="document_rejected">Dokumen Ditolak</option>
                    <option value="custom">Custom</option>
                  </SelectInput>
                  <InputError class="mt-2" :message="form.errors.event_type" />
                </div>

                <div class="sm:col-span-6">
                  <InputLabel for="template" value="Template Pesan" />
                  <textarea
                    id="template"
                    v-model="form.template"
                    rows="6"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required
                  ></textarea>
                  <InputError class="mt-2" :message="form.errors.template" />
                  <p class="mt-2 text-sm text-gray-500">
                    Gunakan variabel seperti {{name}}, {{file_name}}, {{status}}, dan {{uploaded_at}} dalam template Anda.
                  </p>
                </div>

                <div class="sm:col-span-4">
                  <div class="flex items-center">
                    <input
                      id="is_active"
                      type="checkbox"
                      v-model="form.is_active"
                      class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktifkan template</label>
                  </div>
                </div>

                <div class="sm:col-span-6">
                  <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-sm font-medium text-gray-900">Variabel yang Tersedia</h3>
                    <div class="mt-2 grid grid-cols-1 gap-y-2 gap-x-4 sm:grid-cols-3">
                      <div v-for="(variable, index) in availableVariables" :key="index">
                        <div class="flex items-center">
                          <span class="text-xs font-medium text-gray-500 bg-gray-200 px-2 py-1 rounded">
                            {{ variable.name }}
                          </span>
                          <span class="ml-2 text-xs text-gray-500">{{ variable.description }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end mt-6 space-x-3">
                <Link
                  :href="route('admin.whatsapp-notifications.index')"
                  class="inline-flex justify-center rounded-md border border-gray-300 py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  Batal
                </Link>
                <PrimaryButton
                  type="submit"
                  :disabled="form.processing"
                  :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                >
                  {{ form.processing ? 'Menyimpan...' : 'Simpan Template' }}
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
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';

const props = defineProps({
  notification: Object,
});

const form = useForm({
  name: props.notification.name,
  template: props.notification.template,
  event_type: props.notification.event_type,
  is_active: props.notification.is_active,
  variables: props.notification.variables || [],
});

const availableVariables = [
  { name: '{{name}}', description: 'Nama pengguna' },
  { name: '{{file_name}}', description: 'Nama file dokumen' },
  { name: '{{status}}', description: 'Status dokumen' },
  { name: '{{uploaded_at}}', description: 'Tanggal dan waktu unggah' },
];

const submit = () => {
  // Set variables based on available variables if not already set
  if (!form.variables || form.variables.length === 0) {
    form.variables = availableVariables.map(variable => variable.name.replace('{{', '').replace('}}', ''));
  }
  
  // Gunakan axios.post dengan method spoofing PUT
  axios.post(`/admin/whatsapp-notifications/${props.notification.id}`, {
    _method: 'PUT',
    name: form.name,
    template: form.template,
    event_type: form.event_type,
    is_active: form.is_active,
    variables: form.variables,
  })
  .then(response => {
    // Redirect ke halaman index dengan pesan sukses
    window.location = route('admin.whatsapp-notifications.index');
  })
  .catch(error => {
    if (error.response?.data?.errors) {
      // Set form errors jika ada validasi error
      form.setError(error.response.data.errors);
    }
  });
};
</script> 