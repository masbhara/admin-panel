`<template>
  <div class="w-full">
    <div class="bg-white shadow-md rounded-lg">
      <!-- Header dan Filter -->
      <div class="p-6 border-b">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <h3 class="text-xl font-semibold">Manajemen Peran & Izin</h3>
          <div class="flex items-center space-x-4">
            <Button @click="showCreateRoleDialog = true">
              <IconPlus class="w-4 h-4 mr-2" />
              Tambah Peran
            </Button>
          </div>
        </div>
      </div>

      <!-- Tabel Peran dan Izin -->
      <div class="p-6">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left p-4 font-medium">Peran</th>
                <th 
                  v-for="group in permissionGroups" 
                  :key="group"
                  class="text-center p-4 font-medium"
                >
                  {{ formatGroupName(group) }}
                </th>
                <th class="text-right p-4 font-medium">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="role in roles" :key="role.id" class="border-b">
                <td class="p-4">
                  <div class="font-medium">{{ role.name }}</div>
                  <div class="text-sm text-gray-500">{{ role.description }}</div>
                </td>
                <td 
                  v-for="group in permissionGroups" 
                  :key="group"
                  class="text-center p-4"
                >
                  <div class="flex flex-col gap-2">
                    <Checkbox
                      v-for="permission in getPermissionsByGroup(group)"
                      :key="permission.id"
                      :id="'permission-${role.id}-${permission.id}'"
                      :checked="hasPermission(role, permission)"
                      @update:checked="togglePermission(role, permission)"
                      :disabled="role.name === 'Super Admin'"
                    />
                  </div>
                </td>
                <td class="p-4">
                  <div class="flex justify-end space-x-2">
                    <Button
                      variant="outline"
                      size="sm"
                      @click="editRole(role)"
                      :disabled="role.name === 'Super Admin'"
                    >
                      <IconEdit class="w-4 h-4" />
                    </Button>
                    <Button
                      variant="destructive"
                      size="sm"
                      @click="deleteRole(role)"
                      :disabled="role.name === 'Super Admin'"
                    >
                      <IconTrash class="w-4 h-4" />
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Dialog Tambah/Edit Peran -->
    <Dialog :open="showCreateRoleDialog" @close="closeCreateRoleDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ editingRole ? 'Edit Peran' : 'Tambah Peran Baru' }}</DialogTitle>
        </DialogHeader>
        <form @submit.prevent="saveRole" class="space-y-4">
          <div class="space-y-2">
            <Label for="role-name">Nama Peran</Label>
            <Input 
              id="role-name"
              v-model="roleForm.name"
              :error="roleFormErrors.name"
            />
          </div>
          <div class="space-y-2">
            <Label for="role-description">Deskripsi</Label>
            <Textarea
              id="role-description"
              v-model="roleForm.description"
              :error="roleFormErrors.description"
            />
          </div>
          <DialogFooter>
            <Button type="button" variant="outline" @click="closeCreateRoleDialog">
              Batal
            </Button>
            <Button type="submit" :loading="saving">
              {{ editingRole ? 'Simpan' : 'Tambah' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Dialog Konfirmasi Hapus -->
    <Dialog :open="showDeleteDialog" @close="closeDeleteDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Hapus Peran</DialogTitle>
        </DialogHeader>
        <p>Apakah Anda yakin ingin menghapus peran ini? Tindakan ini tidak dapat dibatalkan.</p>
        <DialogFooter>
          <Button type="button" variant="outline" @click="closeDeleteDialog">
            Batal
          </Button>
          <Button 
            type="button" 
            variant="destructive" 
            :loading="deleting"
            @click="confirmDelete"
          >
            Hapus
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Checkbox } from '@/Components/ui/checkbox'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/Components/ui/dialog'
import { IconPlus, IconEdit, IconTrash } from '@/Components/icons'
import { useToast } from '@/Components/ui/toast'

const props = defineProps({
  roles: {
    type: Array,
    required: true
  },
  permissions: {
    type: Array,
    required: true
  }
})

const { toast } = useToast()
const showCreateRoleDialog = ref(false)
const showDeleteDialog = ref(false)
const saving = ref(false)
const deleting = ref(false)
const editingRole = ref(null)
const roleToDelete = ref(null)

const roleForm = useForm({
  name: '',
  description: '',
  permissions: []
})

const permissionGroups = computed(() => {
  return [...new Set(props.permissions.map(p => p.group))]
})

const getPermissionsByGroup = (group) => {
  return props.permissions.filter(p => p.group === group)
}

const formatGroupName = (group) => {
  return group.split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

const hasPermission = (role, permission) => {
  return role.permissions.some(p => p.id === permission.id)
}

const togglePermission = async (role, permission) => {
  try {
    await axios.post(route('roles.toggle-permission'), {
      role_id: role.id,
      permission_id: permission.id
    })
    
    toast({
      title: 'Sukses',
      description: 'Izin berhasil diperbarui'
    })
  } catch (error) {
    toast({
      title: 'Error',
      description: 'Gagal memperbarui izin',
      variant: 'destructive'
    })
  }
}

const editRole = (role) => {
  editingRole.value = role
  roleForm.name = role.name
  roleForm.description = role.description
  showCreateRoleDialog.value = true
}

const deleteRole = (role) => {
  roleToDelete.value = role
  showDeleteDialog.value = true
}

const saveRole = async () => {
  saving.value = true
  try {
    if (editingRole.value) {
      await roleForm.patch(route('roles.update', editingRole.value.id), {
        onSuccess: () => {
          toast({
            title: 'Sukses',
            description: 'Peran berhasil diperbarui'
          })
          closeCreateRoleDialog()
        }
      })
    } else {
      await roleForm.post(route('roles.store'), {
        onSuccess: () => {
          toast({
            title: 'Sukses',
            description: 'Peran baru berhasil ditambahkan'
          })
          closeCreateRoleDialog()
        }
      })
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = async () => {
  if (!roleToDelete.value) return
  
  deleting.value = true
  try {
    await axios.delete(route('roles.destroy', roleToDelete.value.id))
    toast({
      title: 'Sukses',
      description: 'Peran berhasil dihapus'
    })
    closeDeleteDialog()
  } catch (error) {
    toast({
      title: 'Error',
      description: 'Gagal menghapus peran',
      variant: 'destructive'
    })
  } finally {
    deleting.value = false
  }
}

const closeCreateRoleDialog = () => {
  showCreateRoleDialog.value = false
  editingRole.value = null
  roleForm.reset()
  roleForm.clearErrors()
}

const closeDeleteDialog = () => {
  showDeleteDialog.value = false
  roleToDelete.value = null
}
</script>`
