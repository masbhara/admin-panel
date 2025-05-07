<template>
  <AdminLayout title="Demo Komponen">
    <Toast />
    
    <div class="space-y-12">
      <!-- Header -->
      <div>
        <h1 class="text-2xl font-semibold text-text-primary">Demo Komponen UI</h1>
        <p class="mt-1 text-text-secondary">
          Contoh penggunaan komponen-komponen UI yang telah diimplementasikan
        </p>
      </div>
      
      <!-- Toast demo -->
      <Card>
        <CardHeader>
          <CardTitle>Toast Notifications</CardTitle>
          <CardDescription>Sistem notifikasi toast yang dapat dikustomisasi</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="flex flex-wrap gap-3">
            <button
              @click="showSuccessToast"
              class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500"
            >
              Success Toast
            </button>
            <button
              @click="showErrorToast"
              class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
            >
              Error Toast
            </button>
            <button
              @click="showWarningToast"
              class="rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500"
            >
              Warning Toast
            </button>
            <button
              @click="showInfoToast"
              class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
            >
              Info Toast
            </button>
          </div>
        </CardContent>
      </Card>
      
      <!-- Combobox demo -->
      <Card>
        <CardHeader>
          <CardTitle>Combobox (Autocomplete)</CardTitle>
          <CardDescription>Komponen pencarian dengan fungsi autocomplete</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="max-w-md">
            <Combobox
              v-model="selectedPerson"
              :items="people"
              label="Pilih Pengguna"
              placeholder="Cari pengguna..."
              hint="Ketik untuk mencari berdasarkan nama"
              @change="handlePersonChange"
            />
            
            <div v-if="selectedPerson" class="mt-4 rounded-md bg-background-secondary p-3">
              <p class="text-sm font-medium">Dipilih: {{ selectedPerson.name }} ({{ selectedPerson.email }})</p>
            </div>
          </div>
        </CardContent>
      </Card>
      
      <!-- MultiSelect demo -->
      <Card>
        <CardHeader>
          <CardTitle>MultiSelect</CardTitle>
          <CardDescription>Komponen untuk memilih beberapa opsi sekaligus</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="max-w-md">
            <MultiSelect
              v-model="selectedCategories"
              :items="categories"
              label="Kategori"
              placeholder="Pilih kategori..."
              hint="Anda dapat memilih beberapa kategori"
              @change="handleCategoriesChange"
            />
            
            <div v-if="selectedCategories.length > 0" class="mt-4 rounded-md bg-background-secondary p-3">
              <p class="text-sm font-medium">Dipilih {{ selectedCategories.length }} kategori:</p>
              <ul class="mt-2 list-inside list-disc text-sm">
                <li v-for="category in selectedCategories" :key="category.id">
                  {{ category.label }}
                </li>
              </ul>
            </div>
          </div>
        </CardContent>
      </Card>
      
      <!-- Accordion demo -->
      <Card>
        <CardHeader>
          <CardTitle>Accordion</CardTitle>
          <CardDescription>Komponen untuk menampilkan konten yang dapat dikolapskan</CardDescription>
        </CardHeader>
        <CardContent>
          <Accordion :items="faqItems" :defaultOpen="true" />
        </CardContent>
      </Card>
      
      <!-- Tabs demo -->
      <Card>
        <CardHeader>
          <CardTitle>Tabs</CardTitle>
          <CardDescription>Komponen tab untuk mengorganisir konten</CardDescription>
        </CardHeader>
        <CardContent>
          <Tabs defaultValue="account" class="w-full">
            <TabsList>
              <TabsTrigger value="account">Akun</TabsTrigger>
              <TabsTrigger value="password">Password</TabsTrigger>
              <TabsTrigger value="settings">Pengaturan</TabsTrigger>
            </TabsList>
            <TabsContent value="account" class="p-4 mt-4 bg-background-secondary rounded-md">
              <h3 class="text-lg font-medium">Pengaturan Akun</h3>
              <p class="mt-1 text-text-secondary">Kelola informasi akun Anda.</p>
            </TabsContent>
            <TabsContent value="password" class="p-4 mt-4 bg-background-secondary rounded-md">
              <h3 class="text-lg font-medium">Pengaturan Password</h3>
              <p class="mt-1 text-text-secondary">Ubah password Anda.</p>
            </TabsContent>
            <TabsContent value="settings" class="p-4 mt-4 bg-background-secondary rounded-md">
              <h3 class="text-lg font-medium">Pengaturan Umum</h3>
              <p class="mt-1 text-text-secondary">Kelola preferensi aplikasi.</p>
            </TabsContent>
          </Tabs>
        </CardContent>
      </Card>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useToast, Toast } from '@/Components/ui/toast'
import { Combobox } from '@/Components/ui/combobox'
import { MultiSelect } from '@/Components/ui/multiselect'
import { Accordion } from '@/Components/ui/accordion'
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  Tabs,
  TabsList,
  TabsTrigger,
  TabsContent
} from '@/Components/ui'

// Setup for toast demo
const toast = useToast()
const showSuccessToast = () => toast.success('Operasi berhasil dilakukan!')
const showErrorToast = () => toast.error('Terjadi kesalahan saat memproses permintaan.')
const showWarningToast = () => toast.warning('Perhatian: Tindakan ini tidak dapat diurungkan.')
const showInfoToast = () => toast.info('Aplikasi telah diperbarui ke versi terbaru.')

// Setup for combobox demo
const selectedPerson = ref(null)
const people = [
  { id: 1, name: 'John Doe', email: 'john@example.com' },
  { id: 2, name: 'Jane Smith', email: 'jane@example.com' },
  { id: 3, name: 'Robert Johnson', email: 'robert@example.com' },
  { id: 4, name: 'Emily Davis', email: 'emily@example.com' },
  { id: 5, name: 'Michael Wilson', email: 'michael@example.com' },
]
const handlePersonChange = (person) => {
  console.log('Selected person:', person)
}

// Setup for multiselect demo
const selectedCategories = ref([])
const categories = [
  { id: 1, label: 'Marketing' },
  { id: 2, label: 'Sales' },
  { id: 3, label: 'Engineering' },
  { id: 4, label: 'Design' },
  { id: 5, label: 'Customer Support' },
  { id: 6, label: 'Product Management' },
]
const handleCategoriesChange = (categories) => {
  console.log('Selected categories:', categories)
}

// Setup for accordion demo
const faqItems = [
  {
    title: 'Bagaimana cara mengubah password?',
    content: 'Untuk mengubah password, buka halaman profil kemudian klik tab "Keamanan". Di sana Anda dapat mengganti password dengan mengisi formulir yang tersedia.'
  },
  {
    title: 'Bagaimana cara menambahkan pengguna baru?',
    content: 'Buka menu "Manajemen Pengguna" kemudian klik tombol "Tambah Pengguna". Isi formulir dengan data pengguna baru dan klik "Simpan".'
  },
  {
    title: 'Apakah data saya aman?',
    content: 'Ya, kami menggunakan teknologi enkripsi terkini untuk memastikan keamanan data Anda. Semua komunikasi dilakukan melalui koneksi HTTPS aman.'
  },
  {
    title: 'Bagaimana cara mengekspor data?',
    content: 'Anda dapat mengekspor data dalam format CSV atau Excel dari halaman yang relevan menggunakan tombol "Ekspor" yang tersedia di bagian atas tabel.'
  }
]
</script> 