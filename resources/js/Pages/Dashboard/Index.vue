<template>
  <DashboardLayout>
    <template #header>
      Statistiques et Analyses
    </template>
    
    <template #actions>
      <HeaderActions />
    </template>

    <div class="space-y-6">
      <!-- Description -->
      <div class="text-sm text-gray-600">
        Visualisez et analysez les données de votre exploitation en Guadeloupe
      </div>

      <!-- Indicators Table -->
      <DataTable
        title="Indicateurs de performance agricole en Guadeloupe"
        :headers="[
          { key: 'indicateur', label: 'Indicateur' },
          { key: 'valeur_actuelle', label: 'Valeur actuelle' },
          { key: 'objectif', label: 'Objectif' },
          { key: 'unite', label: 'Unité' }
        ]"
        :data="indicators"
        @add="addIndicator"
        @delete="deleteIndicator"
      />
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import HeaderActions from '@/Components/Dashboard/HeaderActions.vue'
import DataTable from '@/Components/Dashboard/DataTable.vue'

const indicators = ref([
  {
    indicateur: 'Rendement Canne à Sucre',
    valeur_actuelle: '75',
    objectif: '85',
    unite: 't/ha'
  },
  {
    indicateur: 'Qualité Banane Export',
    valeur_actuelle: '88',
    objectif: '95',
    unite: '%'
  },
  {
    indicateur: 'Rentabilité Ananas',
    valeur_actuelle: '70',
    objectif: '80',
    unite: '%'
  },
  {
    indicateur: 'Certification Bio',
    valeur_actuelle: '75',
    objectif: '90',
    unite: '%'
  },
  {
    indicateur: 'Innovation Igname',
    valeur_actuelle: '65',
    objectif: '75',
    unite: '%'
  }
])

const addIndicator = () => {
  // Implementasi logika penambahan indikator
}

const deleteIndicator = (indicator) => {
  // Implementasi logika penghapusan indikator
}
</script>
