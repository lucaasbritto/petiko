<template>
  <q-layout>    
    <q-drawer
      show-if-above
      bordered
      :width="280"
      class="bg-filter text-white"
    >
      <div class="q-pa-md">
          <div class="row items-center q-mb-md" style="color: white; font-weight: bold;">
            <q-icon name="filter_list" size="20px" class="q-mr-sm" />
            <div class="text-h6">Filtro</div>
          </div>

        <q-input
          v-model="filters.search"
          label="Título ou Descrição"
          class="inputFilter"
          dense
          outlined
          clearable
          autofocus
          @update:model-value="applyFilter('search', filters.search)"
        />

        
        <q-input
          v-model="filters.due_date"
          label="Data de Vencimento"
          type="date"
          class="inputFilter q-mt-sm"
          dense
          outlined
          clearable
          autofocus
          @update:model-value="applyFilter('due_date', filters.due_date)"
        />

        <q-select
          v-model="filters.is_done"
          label="Situação"
          dense
          outlined
          emit-value
          map-options
          autofocus
          class="inputFilter q-mt-sm"
          :options="[
            { label: 'Todos', value: '' },
            { label: 'Concluída', value: '1' },
            { label: 'Pendente', value: '0' }
          ]"
          @update:model-value="applyFilter('is_done', filters.is_done)"
        />

        <q-select
          v-if="userStore.isAdmin"
          v-model="filters.user_id"
          label="Usuário"
          dense
          outlined
          emit-value
          map-options
          clearable
          class="inputFilter q-mt-sm"
          :options="userOptions"
          @update:model-value="applyFilter('user_id', filters.user_id)"
        />
      </div>
    </q-drawer>
    
    <q-page-container class="pageContainer">      
      <div class="row justify-center">        
        <div class="col-12 col-md-8 q-mt-md">
         <div class="row justify-end q-mb-md">
        <q-btn
          v-if="userStore.isAdmin"
          label="Nova Tarefa"
          icon="add"
          style="background: #0083a0; color: white"
          @click="openTaskDialog"
          unelevated
          rounded
          dense
        />
      </div>

          <q-card flat bordered class="q-pt-md" style="">
            <q-table
              :rows="requestStore.requests.filter(r => r && r.id)"
              title="Tarefas"
              :columns="columns"
              row-key="id"
              dense
              flat bordered
              class="shadow-1 my-table"
              :loading="requestStore.loading"
              :pagination.sync="pagination"
              :rows-per-page="10"
              :rows-per-page-options="[10, 20, 50, 100]"
              style="color: #0083a0"
            >
              <template v-slot:body-cell-title="props">
                <q-td :props="props">
                  <div class="truncate-text">
                    {{ props.row.title }}
                  </div>
                </q-td>
              </template>

              <template v-slot:body-cell-description="props">
                <q-td :props="props">
                  <div class="truncate-text">
                    {{ props.row.description }}
                  </div>
                </q-td>
              </template>

              <template v-slot:body-cell-is_done="props">
                <q-td :props="props">
                  <q-badge :color="getStatusColor(props.row)" rounded>
                    {{ getStatusLabel(props.row) }}
                  </q-badge>
                </q-td>
              </template>

              <template v-slot:body-cell-actions="props">
                <q-td class="q-pa-none" align="center">
                   <div class="row no-wrap items-center justify-center q-gutter-xs">
                    <q-btn
                      flat
                      dense
                      icon="visibility"
                      color="primary"
                      @click="openTaskView(props.row)"
                      size="sm"
                    />

                    <q-separator vertical inset/>
                    
                    <q-btn
                      v-if="userStore.isAdmin" 
                      dense
                      flat
                      icon="edit"
                      color="blue"
                      @click="openEditDialog(props.row)"
                      title="Editar tarefa"
                      size="sm"
                    />
                    
                    <q-separator v-if="userStore.isAdmin" vertical inset/>
                                     
                    <q-btn
                      v-if="!props.row.is_done"
                      dense
                      flat
                      icon="done"
                      color="green"
                      @click="confirmUpdated(props.row.id)"
                      title="Marcar como concluída"
                      size="sm"
                    />
                    
                    <q-separator v-if="!props.row.is_done" vertical inset/>

                  <q-btn
                    dense
                    flat
                    icon="delete"
                    color="red"
                    @click="confirmRemove(props.row.id)"
                    title="Remover tarefa"
                    size="sm"
                  />
                  </div>
                </q-td>
              </template>

              <template v-slot:no-data>
                <div class="text-center text-grey q-pa-md">
                  Nenhuma tarefa encontrada.
                </div>
              </template>
            </q-table>
          </q-card>
        </div>
      </div>

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
    </q-page-container>
  </q-layout>

  <TaskViewDialog v-model="taskViewOpen" :task="taskToView" :is-admin="Boolean(userStore.isAdmin)"/>
</template>

<script setup>
import { useDashboardScript } from './DashboardView.js'
import TaskViewDialog from '../../components/TaskViewDialog.vue'

const {
  userStore,
  requestStore,
  filters,
  pagination,
  columns,
  applyFilter,  
  getStatusColor,
  getStatusLabel,
  openTaskDialog,
  openEditDialog,
  confirmUpdated,
  confirmRemove,
  taskViewOpen,
  taskToView,
  openTaskView,
  userOptions,
} = useDashboardScript()
</script>

<style lang="scss" >
    @use './DashboardView.scss';  
</style>