import { defineStore } from 'pinia'
import { getRequests, createRequest  } from '../api/requests'

export const useRequestStore = defineStore('requests', {
  state: () => ({
    requests: [],
    pagination: {
      currentPage: 1,
      lastPage: 1,
      perPage: 10,
      total: 0
    },
    loading: false,
    filters: {
      search: '',
      due_date: '',
      is_done: '',
    },
  }),

  actions: {    
    async fetchRequests(page = 1) {
      this.loading = true;
      try {
        const payload = { ...this.filters, page };
        const res = await getRequests(payload);
        this.requests = res.data;
        this.pagination.currentPage = res.current_page;
        this.pagination.lastPage = res.last_page;
        this.pagination.perPage = res.per_page;
        this.pagination.total = res.total;
      } catch (e) {
        console.error('Erro ao buscar as tarefas:', e)
      } finally {
        this.loading = false;
      }
    },

     async createRequest(payload) {
      try {
        const created = await createRequest(payload)
        this.requests.unshift(created)
        
        if (this.requests.length > this.pagination.perPage) {
            this.requests.pop()
        }
        
        return created
      } catch (e) {
        console.error('Erro ao criar a tarefa:', e)
        throw e
      }
    },

    setFilter(key, value) {
      this.filters[key] = value
      this.fetchRequests(1)
    },
    changePage(page) {
      this.fetchRequests(page);
    },
  }
})