`<template>
  <div class="w-full">
    <div class="bg-white shadow-md rounded-lg">
      <!-- Header dan Filter -->
      <div class="p-6 border-b">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <h3 class="text-xl font-semibold">Log Aktivitas</h3>
          <div class="flex items-center space-x-4">
            <div class="w-64">
              <Input
                v-model="search"
                placeholder="Cari aktivitas..."
                type="search"
              >
                <template #prefix>
                  <IconSearch class="w-4 h-4 text-gray-400" />
                </template>
              </Input>
            </div>
            <Select v-model="selectedType" class="w-48">
              <option value="">Semua Tipe</option>
              <option v-for="type in activityTypes" :key="type" :value="type">
                {{ formatActivityType(type) }}
              </option>
            </Select>
            <Select v-model="selectedUser" class="w-48">
              <option value="">Semua Pengguna</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </Select>
          </div>
        </div>
      </div>

      <!-- Timeline Aktivitas -->
      <div class="p-6">
        <div v-if="filteredActivities.length === 0" class="text-center py-8">
          <p class="text-gray-500">Tidak ada aktivitas yang ditemukan</p>
        </div>
        
        <div v-else class="space-y-6">
          <div 
            v-for="(group, date) in groupedActivities" 
            :key="date" 
            class="space-y-4"
          >
            <div class="sticky top-0 bg-white z-10 py-2">
              <h4 class="text-sm font-medium text-gray-500">
                {{ formatDate(date) }}
              </h4>
            </div>

            <div 
              v-for="activity in group" 
              :key="activity.id" 
              class="flex space-x-4 p-4 hover:bg-gray-50 rounded-lg transition-colors"
            >
              <!-- Icon berdasarkan tipe aktivitas -->
              <div class="mt-1">
                <div 
                  class="w-8 h-8 rounded-full flex items-center justify-center"
                  :class="getActivityIconClass(activity.type)"
                >
                  <component 
                    :is="getActivityIcon(activity.type)"
                    class="w-4 h-4 text-white"
                  />
                </div>
              </div>

              <!-- Detail Aktivitas -->
              <div class="flex-1">
                <div class="flex items-center space-x-2">
                  <span class="font-medium">{{ activity.causer?.name || 'System' }}</span>
                  <span class="text-gray-500">•</span>
                  <span class="text-sm text-gray-500">{{ formatTime(activity.created_at) }}</span>
                </div>
                
                <p class="mt-1">
                  {{ formatActivityDescription(activity) }}
                </p>

                <!-- Detail Perubahan -->
                <div v-if="activity.properties?.changes" class="mt-2">
                  <Collapsible>
                    <CollapsibleTrigger class="flex items-center text-sm text-primary hover:underline">
                      <IconChevronRight class="w-4 h-4" />
                      Lihat Detail Perubahan
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                      <div class="mt-2 space-y-2">
                        <div 
                          v-for="(change, field) in activity.properties.changes" 
                          :key="field"
                          class="grid grid-cols-3 gap-4 text-sm"
                        >
                          <span class="font-medium">{{ formatFieldName(field) }}</span>
                          <div class="text-red-500 line-through">
                            {{ formatValue(change.old) }}
                          </div>
                          <div class="text-green-500">
                            {{ formatValue(change.new) }}
                          </div>
                        </div>
                      </div>
                    </CollapsibleContent>
                  </Collapsible>
                </div>

                <!-- Meta Info -->
                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                  <span>{{ activity.subject_type }}</span>
                  <span>•</span>
                  <span>IP: {{ activity.properties?.ip || 'N/A' }}</span>
                  <span>•</span>
                  <span>{{ activity.properties?.user_agent || 'N/A' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="activities.length >= perPage" class="mt-6">
          <Button 
            variant="outline" 
            class="w-full"
            :loading="loading"
            @click="loadMore"
          >
            Muat Lebih Banyak
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { format, parseISO } from 'date-fns'
import { id } from 'date-fns/locale'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Select } from '@/Components/ui/select'
import { Collapsible, CollapsibleTrigger, CollapsibleContent } from '@/Components/ui/collapsible'
import { 
  IconSearch, 
  IconChevronRight,
  IconPlus,
  IconEdit,
  IconTrash,
  IconLogin,
  IconLogout,
  IconSettings,
  IconFile
} from '@/Components/icons'

const props = defineProps({
  initialActivities: {
    type: Array,
    required: true
  },
  users: {
    type: Array,
    required: true
  },
  activityTypes: {
    type: Array,
    required: true
  }
})

const activities = ref(props.initialActivities)
const search = ref('')
const selectedType = ref('')
const selectedUser = ref('')
const loading = ref(false)
const perPage = 20
const page = ref(1)

const filteredActivities = computed(() => {
  return activities.value.filter(activity => {
    const matchesSearch = search.value === '' || 
      activity.description.toLowerCase().includes(search.value.toLowerCase()) ||
      (activity.causer?.name || '').toLowerCase().includes(search.value.toLowerCase())
    
    const matchesType = selectedType.value === '' || 
      activity.type === selectedType.value
    
    const matchesUser = selectedUser.value === '' || 
      activity.causer_id === selectedUser.value

    return matchesSearch && matchesType && matchesUser
  })
})

const groupedActivities = computed(() => {
  const groups = {}
  filteredActivities.value.forEach(activity => {
    const date = format(parseISO(activity.created_at), 'yyyy-MM-dd')
    if (!groups[date]) {
      groups[date] = []
    }
    groups[date].push(activity)
  })
  return groups
})

const formatDate = (date) => {
  return format(parseISO(date), 'EEEE, d MMMM yyyy', { locale: id })
}

const formatTime = (datetime) => {
  return format(parseISO(datetime), 'HH:mm')
}

const formatActivityType = (type) => {
  return type.split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

const formatFieldName = (field) => {
  return field.split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

const formatValue = (value) => {
  if (value === null || value === undefined) return '-'
  if (typeof value === 'boolean') return value ? 'Ya' : 'Tidak'
  return value.toString()
}

const getActivityIcon = (type) => {
  const icons = {
    created: IconPlus,
    updated: IconEdit,
    deleted: IconTrash,
    login: IconLogin,
    logout: IconLogout,
    settings: IconSettings
  }
  return icons[type] || IconFile
}

const getActivityIconClass = (type) => {
  const classes = {
    created: 'bg-green-500',
    updated: 'bg-blue-500',
    deleted: 'bg-red-500',
    login: 'bg-purple-500',
    logout: 'bg-orange-500',
    settings: 'bg-gray-500'
  }
  return classes[type] || 'bg-gray-500'
}

const formatActivityDescription = (activity) => {
  let description = activity.description

  // Tambahkan format khusus sesuai kebutuhan
  if (activity.type === 'login') {
    description = 'Melakukan login ke sistem'
  } else if (activity.type === 'logout') {
    description = 'Keluar dari sistem'
  }

  return description
}

const loadMore = async () => {
  loading.value = true
  try {
    const response = await axios.get(route('activity-log.index'), {
      params: {
        page: page.value + 1,
        per_page: perPage
      }
    })
    activities.value = [...activities.value, ...response.data.data]
    page.value++
  } catch (error) {
    console.error('Error loading more activities:', error)
  } finally {
    loading.value = false
  }
}
</script>`
