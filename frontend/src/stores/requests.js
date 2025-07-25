import { defineStore } from 'pinia'
import { getRequests, createRequest, updateRequestStatus, deleteRequest, updateRequest   } from '../api/requests'

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
        await this.fetchRequests(this.pagination.currentPage)
        
        return created
      } catch (e) {
        console.error('Erro ao criar a tarefa:', e)
        throw e
      }
    },

    async updateStatus(id) {
      try {       
        const updated = await updateRequestStatus(id)
        const index = this.requests.findIndex((r) => r.id === id)
        if (index !== -1) {
          this.requests.splice(index, 1, updated)
        }
      } catch (e) {
        console.error('Erro ao atualizar status:', e)
        throw e
      }
    },

    async updateRequest(id, payload) {
      try {
        const updated = await updateRequest(id, payload)
        const index = this.requests.findIndex((r) => r.id === id)
        if (index !== -1) {
          this.requests.splice(index, 1, updated)
        }
        return updated
      } catch (e) {
        console.error('Erro ao atualizar a tarefa:', e)
        throw e
      }
    },

    async removeTask(id) {
      try {
        await deleteRequest(id)
        this.requests = this.requests.filter(r => r.id !== id)
      } catch (e) {
        console.error('Erro ao remover tarefa:', e)
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