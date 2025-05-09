<template>
  <AdminLayout :user="auth?.user">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Setting</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="form.post(route('admin.settings.store'))">
              <div class="space-y-6">
                <!-- Setting Information -->
                <div>
                  <h3 class="text-lg font-medium leading-6 text-gray-900">Setting Information</h3>
                  <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                      <InputLabel for="key" value="Setting Key" />
                      <div class="mt-1">
                        <TextInput
                          id="key"
                          v-model="form.key"
                          type="text"
                          class="block w-full"
                          required
                        />
                        <InputError :message="form.errors.key" class="mt-2" />
                      </div>
                    </div>

                    <div class="sm:col-span-3">
                      <InputLabel for="type" value="Setting Type" />
                      <div class="mt-1">
                        <Select
                          id="type"
                          v-model="form.type"
                          class="block w-full"
                          required
                        >
                          <option value="text">Text</option>
                          <option value="textarea">Text Area</option>
                          <option value="boolean">Boolean</option>
                          <option value="number">Number</option>
                          <option value="select">Select</option>
                        </Select>
                        <InputError :message="form.errors.type" class="mt-2" />
                      </div>
                    </div>

                    <div class="sm:col-span-6">
                      <InputLabel for="value" value="Setting Value" />
                      <div class="mt-1">
                        <component
                          :is="getInputComponent"
                          id="value"
                          v-model="form.value"
                          class="block w-full"
                          required
                        />
                        <InputError :message="form.errors.value" class="mt-2" />
                      </div>
                    </div>

                    <div class="sm:col-span-6">
                      <InputLabel for="description" value="Description" />
                      <div class="mt-1">
                        <TextArea
                          id="description"
                          v-model="form.description"
                          rows="3"
                          class="block w-full"
                        />
                        <InputError :message="form.errors.description" class="mt-2" />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                  <Link
                    :href="route('admin.settings.index')"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 mr-4"
                  >
                    Cancel
                  </Link>
                  <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Create Setting
                  </PrimaryButton>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import InputError from '@/Components/InputError.vue'
import Select from '@/Components/Select.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const form = useForm({
  key: '',
  type: 'text',
  value: '',
  description: '',
})

const getInputComponent = computed(() => {
  switch (form.type) {
    case 'textarea':
      return TextArea
    case 'boolean':
      return 'input'
    case 'number':
      return TextInput
    case 'select':
      return Select
    default:
      return TextInput
  }
})
</script>
