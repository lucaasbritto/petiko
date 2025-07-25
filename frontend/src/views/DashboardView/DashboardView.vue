<template>
  <div class="q-pa-md dashboard flex flex-center">
    <div class="full-width" style="max-width: 1000px">
      <q-card class="q-pa-md q-mb-md shadow-2">
        <div class="row q-col-gutter-sm items-end">
          <div class="col-md-5 col-12">
            <q-input
              v-model="filters.search"
              label="Título ou Descrição"
              dense
              outlined
              rounded
              debounce="300"
              @update:model-value="applyFilter('search', filters.search)"
            />
          </div>
          <div class="col-md-4 col-12">
            <q-input
              v-model="filters.due_date"
              label="Data de Vencimento"
              type="date"
              dense
              outlined
              rounded
              @update:model-value="applyFilter('due_date', filters.due_date)"
            />
          </div>
          <div class="col-md-3 col-12">
            <q-select
              v-model="filters.is_done"
              label="Situação"
              dense
              outlined
              rounded
              emit-value
              map-options
              :options="[
                { label: 'Todos', value: '' },
                { label: 'Concluída', value: '1' },
                { label: 'Pendente', value: '0' }
              ]"
              @update:model-value="applyFilter('is_done', filters.is_done)"
            />
          </div>
        </div>
      </q-card>

      <div class="q-mb-md text-right">
        <q-btn
          label="Nova Tarefa"
          icon="add"
          color="primary"
          size="sm"
          dense
          rounded
          unelevated
          @click="criarTarefa"
        />
      </div>

      <q-card flat bordered>
        <q-table
          :rows="requestStore.requests"
          :columns="columns"
          row-key="id"
          dense
          flat
          bordered
          class="shadow-1"
          :loading="requestStore.loading"
          hide-bottom
        >
          <template v-slot:body-cell-is_done="props">
            <q-td :props="props">
              <q-badge :color="props.row.is_done == 1 ? 'green' : 'orange'" rounded>
                {{ props.row.is_done == 1 ? 'concluída' : 'pendente' }}
              </q-badge>
            </q-td>
          </template>

          <template v-slot:no-data>
            <div class="text-center text-grey q-pa-md">Nenhuma tarefa encontrada.</div>
          </template>
        </q-table>
      </q-card>

      <div class="q-mt-md flex justify-center" v-if="pagination.lastPage > 1">
        <q-pagination
          v-model="pagination.currentPage"
          :max="pagination.lastPage"
          direction-links
          outline
          color="primary"
          @update:model-value="requestStore.changePage"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useDashboardScript } from './DashboardView.js'

const {
  requestStore,
  filters,
  pagination,
  applyFilter,
  formatDateBR,
  criarTarefa
} = useDashboardScript()

const columns = [
  { name: 'id', label: '#', field: 'id', align: 'left' },
  { name: 'title', label: 'Título', field: 'title', align: 'left' },
  { name: 'description', label: 'Descrição', field: 'description', align: 'left' },
  { name: 'due_date', label: 'Data', field: row => formatDateBR(row.due_date), align: 'left' },
  { name: 'is_done', label: 'Situação', field: 'is_done', align: 'left' }
]

onMounted(() => {
  requestStore.fetchRequests()
})
</script>

<style lang="scss" scoped>
@use './DashboardView.scss';
</style>