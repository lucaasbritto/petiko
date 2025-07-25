import { reactive, watch, ref } from 'vue'
import { useRequestStore } from '../../stores/requests'

export function useDashboardScript() {
  const requestStore = useRequestStore()
  const filters = reactive(requestStore.filters)
  const pagination = reactive(requestStore.pagination)
  const editingRow = ref(null)
  const editedStatus = ref('')

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

  async function updateStatus(id, status) {
    try {
        await requestStore.updateStatus(id, status)

        const item = requestStore.requests.find(r => r.id === id)
        if (item) item.status = status
    } catch (e) {
        console.error('Erro ao atualizar status', e)
    }
   }

  return {
    requestStore,
    filters,
    pagination,
    applyFilter,
    formatDateBR,
    editingRow,
    editedStatus,
    updateStatus,
  }
}