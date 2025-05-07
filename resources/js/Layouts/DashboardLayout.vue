<template>
  <div class="min-h-screen bg-background-primary">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-background-primary border-r border-border-light">
      <div class="flex items-center h-16 px-4 border-b border-border-light">
        <h1 class="text-xl font-semibold text-text-primary">Agri Dom</h1>
      </div>
      <nav class="p-4 space-y-2">
        <router-link
          v-for="item in navigation"
          :key="item.name"
          :to="item.href"
          :class="[
            item.current
              ? 'bg-background-secondary text-text-primary'
              : 'text-text-secondary hover:bg-background-secondary hover:text-text-primary',
            'group flex items-center px-3 py-2 text-sm font-medium rounded-md'
          ]"
        >
          <component
            :is="item.icon"
            :class="[
              item.current ? 'text-text-primary' : 'text-text-tertiary group-hover:text-text-secondary',
              'mr-3 flex-shrink-0 h-6 w-6'
            ]"
          />
          {{ item.name }}
        </router-link>
      </nav>
    </aside>

    <!-- Main content -->
    <div class="pl-64">
      <!-- Top navigation -->
      <header class="bg-background-primary shadow border-b border-border-light">
        <div class="px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 items-center justify-between">
            <h2 class="text-xl font-semibold text-text-primary">
              <slot name="header" />
            </h2>
            <div class="flex items-center space-x-4">
              <ThemeToggleSimple />
              <slot name="actions" />
            </div>
          </div>
        </div>
      </header>

      <!-- Main content -->
      <main class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ThemeToggleSimple from '@/Components/ThemeToggleSimple.vue'
import {
  HomeIcon,
  ChartBarIcon,
  DocumentTextIcon,
  CogIcon,
  UserGroupIcon
} from '@heroicons/vue/24/outline'

const navigation = ref([
  { name: 'Tableau de bord', href: '/', icon: HomeIcon, current: true },
  { name: 'Parcelles', href: '/parcelles', icon: ChartBarIcon, current: false },
  { name: 'Cultures', href: '/cultures', icon: DocumentTextIcon, current: false },
  { name: 'Inventaire', href: '/inventaire', icon: UserGroupIcon, current: false },
  { name: 'Finances', href: '/finances', icon: CogIcon, current: false },
  { name: 'Statistiques', href: '/statistiques', icon: ChartBarIcon, current: false },
  { name: 'Rapports', href: '/rapports', icon: DocumentTextIcon, current: false },
  { name: 'Paramètres', href: '/parametres', icon: CogIcon, current: false },
])
</script>
