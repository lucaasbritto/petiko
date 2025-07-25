import { reactive, computed, watch } from 'vue'
import { useRequestStore } from '../../stores/requests'

export function useDashboardScript() {
  const requestStore = useRequestStore()
  const filters = reactive(requestStore.filters)
  const pagination = reactive(requestStore.pagination)

  let debounceTimeout
  watch(() => filters.search, (newVal) => {
    clearTimeout(debounceTimeout)
    debounceTimeout = setTimeout(() => {
      requestStore.setFilter('search', newVal)
    }, 500)
  })

  function applyFilter(key, value) {
    requestStore.setFilter(key, value)
  }

  function formatDateBR(dateStr) {
    if (!dateStr) return ''
    
    const [year, month, day] = dateStr.split('T')[0].split('-')
    return `${day}/${month}/${year}`
  }

  return {
    requestStore,
    filters,
    pagination,
    applyFilter,
    formatDateBR,
  }
}