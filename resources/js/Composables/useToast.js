import { ref } from 'vue'
import { createGlobalState } from '@vueuse/core'

export const useToast = createGlobalState(() => {
  const toasts = ref([])
  const defaultDuration = 5000 // 5 detik default
  
  /**
   * Menambahkan toast baru
   * @param {string} message - Pesan yang akan ditampilkan
   * @param {string} type - Tipe toast (success, error, warning, info)
   * @param {number} duration - Durasi tampil dalam ms
   * @returns {object} - Object toast yang ditambahkan
   */
  function add(message, type = 'info', duration = defaultDuration) {
    // Buat ID unik untuk toast
    const id = `toast-${Date.now()}-${Math.floor(Math.random() * 1000)}`
    
    // Buat toast baru
    const newToast = {
      id,
      message,
      type,
      duration,
      createdAt: Date.now()
    }
    
    // Tambahkan ke array toasts
    toasts.value.push(newToast)
    
    // Set timeout untuk hapus toast
    if (duration > 0) {
      setTimeout(() => {
        remove(id)
      }, duration)
    }
    
    return newToast
  }
  
  /**
   * Menghapus toast berdasarkan ID
   * @param {string} id - ID toast yang akan dihapus
   */
  function remove(id) {
    toasts.value = toasts.value.filter(toast => toast.id !== id)
  }
  
  /**
   * Menghapus semua toast
   */
  function clear() {
    toasts.value = []
  }
  
  /**
   * Helper methods untuk berbagai tipe toast
   */
  const success = (message, duration) => add(message, 'success', duration)
  const error = (message, duration) => add(message, 'error', duration)
  const warning = (message, duration) => add(message, 'warning', duration)
  const info = (message, duration) => add(message, 'info', duration)
  
  return {
    toasts,
    add,
    remove,
    clear,
    success,
    error,
    warning,
    info
  }
}) 