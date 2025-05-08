<template>
  <div class="bg-yellow-100 p-4 mb-4 rounded">
    <h3 class="font-semibold mb-2">Debug Permission</h3>
    <div>
      <div><strong>User:</strong> {{ auth?.user?.name }}</div>
      <div><strong>Email:</strong> {{ auth?.user?.email }}</div>
      <div><strong>Permission view-documents:</strong> {{ hasViewDocumentsPermission ? 'Ya' : 'Tidak' }}</div>
      <div class="mt-2">
        <strong>Semua Permission:</strong> 
        <pre class="bg-white p-2 mt-1 text-xs rounded">{{ JSON.stringify(auth?.user?.can || {}, null, 2) }}</pre>
      </div>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const auth = computed(() => page.props.auth);
const hasViewDocumentsPermission = computed(() => !!auth.value?.user?.can?.['view-documents']);
</script> 