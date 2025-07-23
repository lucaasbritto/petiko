<template>
  <div class="dashboard container py-5">
    <div class="card shadow-sm mb-4 border-0">
      <div class="card-body">
        <div class="row g-3 align-items-end">          
          <div class="col-md-5">
            <label class="form-label fw-semibold">Titulo ou Descrição</label>
            <input v-model="filters.search" class="form-control" placeholder="Buscar por título ou descrição" />
          </div>          
          <div class="col-md-4">
            <label class="form-label fw-semibold">Data de Vencimento</label>
            <input type="date" v-model="filters.due_date" @change="applyFilter('due_date', filters.due_date)" class="form-control" />
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Situação</label>
            <select v-model="filters.is_done" @change="applyFilter('is_done', filters.is_done)" class="form-select">
              <option value="" selected>Todos</option>
              <option value="1">Concluida</option>
              <option value="0">Pendente</option>
            </select>
          </div>          
        </div>
      </div>
    </div>

    <div class="text-end mb-2">
        <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" @click="criarTarefa">
            <i class="bi bi-plus-circle"></i> Nova Tarefa
        </button>
    </div>


    <div v-if="requestStore.loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <table v-else class="table table-hover shadow-sm">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Titulo</th>
          <th>Descrição</th>
          <th>Data</th>
          <th>Situação</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="req in requestStore.requests" :key="req.id">
          <td>{{ req.id }}</td>
          <td>{{ req.title }}</td>
          <td>{{ req.description }}</td>
          <td>{{ formatDateBR(req.due_date) }}</td>
          <td>
            <span :class="req.is_done ? 'badge bg-success' : 'badge bg-warning'">
              {{ req.is_done ? 'concluída' : 'pendente' }}
            </span>
        </td>
        </tr>
        <tr v-if="!requestStore.loading && requestStore.requests.length === 0">
          <td colspan="5" class="text-center text-muted">Nenhuma tarefa encontrada.</td>
        </tr>
      </tbody>
    </table>

    <nav v-if="pagination.lastPage > 1" aria-label="Page navigation">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
          <button class="page-link" @click="requestStore.changePage(pagination.currentPage - 1)">Anterior</button>
        </li>
        <li class="page-item"
            v-for="page in [...Array(pagination.lastPage).keys()].map(i => i + 1)"
            :key="page"
            :class="{ active: page === pagination.currentPage }">
          <button class="page-link" @click="requestStore.changePage(page)">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: pagination.currentPage === pagination.lastPage }">
          <button class="page-link" @click="requestStore.changePage(pagination.currentPage + 1)">Próxima</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useDashboardScript } from './DashboardView.js'
import './DashboardView.scss'

const {
  requestStore,
  filters,
  pagination,
  applyFilter,
  formatDateBR,
  criarTarefa
} = useDashboardScript()

onMounted(() => {
  requestStore.fetchRequests()
})
</script>