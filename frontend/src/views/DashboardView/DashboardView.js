import { reactive, computed, watch } from 'vue'
import { useRequestStore } from '../../stores/requests'

export function useDashboardScript() {
  const requestStore = useRequestStore()
  const filters = reactive(requestStore.filters)
  const pagination = computed(() => requestStore.pagination)

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
    const date = new Date(dateStr)
    if (isNaN(date)) return ''
    return new Intl.DateTimeFormat('pt-BR').format(date)
  }

  function criarTarefa() {
    //
  }

  return {
    requestStore,
    filters,
    pagination,
    applyFilter,
    formatDateBR,
    criarTarefa
  }
}