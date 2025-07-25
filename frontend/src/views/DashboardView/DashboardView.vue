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
                      v-if="userStore.isAdmin" 
                      dense
                      flat
                      round
                      icon="edit"
                      color="blue"
                      @click="openEditDialog(props.row)"
                      title="Editar tarefa"
                    />
                    
                    <q-separator v-if="userStore.isAdmin" vertical inset/>
                                     
                    <q-btn
                      v-if="!props.row.is_done"
                      dense
                      flat
                      round
                      icon="done"
                      color="green"
                      @click="confirmUpdated(props.row.id)"
                      title="Marcar como concluída"
                    />
                    
                    <q-separator v-if="!props.row.is_done" vertical inset/>

                  <q-btn
                    dense
                    flat
                    round
                    icon="delete"
                    color="red"
                    @click="confirmRemove(props.row.id)"
                    title="Remover tarefa"
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
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useDashboardScript } from './DashboardView.js'
import { Dialog, Loading, Notify } from 'quasar'
import TaskRequestFormDialog from '../../components/TaskRequestFormDialog.vue'
import { useUserStore } from '../../stores/user'

const editingTask = ref(null)
const userStore = useUserStore()

const {
  requestStore,
  filters,
  pagination,
  applyFilter,
  formatDateBR,
} = useDashboardScript()

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' },
  { name: 'title', label: 'Título', field: 'title', align: 'left' },
  { name: 'description', label: 'Descrição', field: 'description', align: 'left' },
  { name: 'due_date', label: 'Data', field: row => formatDateBR(row.due_date), align: 'left' },
  { name: 'is_done', label: 'Situação', field: 'is_done', align: 'left' },
  { name: 'actions', label: 'Ações', field: 'actions', align: 'center' }
]

if (userStore.isAdmin) {
  columns.splice(5, 0, {
    name: 'user_id',
    label: 'Responsável',
    field: row => row.user?.name || '-',
    align: 'left',
  })
}

onMounted(() => {
  requestStore.fetchRequests()

  if (userStore.isAdmin) {
    userStore.fetchUsuarios()
  }
})

function openTaskDialog() {
  Dialog.create({
    component: TaskRequestFormDialog,
    componentProps: { persistent: true }
  })
}

function openEditDialog(task) {
  editingTask.value = task
  Dialog.create({
    component: TaskRequestFormDialog,
    componentProps: {
      persistent: true,
      task: editingTask.value
    }
  }).onCancel(() => {
    editingTask.value = null
  }).onDismiss(() => {
    editingTask.value = null
  })
}

function confirmAction(message = 'Tem certeza?') {
  return new Promise((resolve) => {
    Dialog.create({
      title: 'Confirmação',
      message,
      cancel: true,
      persistent: true,
      ok: { label: 'Confirmar', color: 'primary' },
      cancel: { label: 'Cancelar', color: 'negative' }
    })
      .onOk(() => resolve(true))
      .onCancel(() => resolve(false))
      .onDismiss(() => resolve(false))
  })
}

async function confirmUpdated(id) {
  const confirmed = await confirmAction('Deseja marcar esta tarefa como concluída?')
  if (!confirmed) return

  Loading.show({ message: 'Atualizando tarefa...' })

  try {
    await requestStore.updateStatus(id)
    Notify.create({ type: 'positive', message: 'Situação alterada com sucesso!', timeout: 2500 })
  } catch (e) {
    console.error('Erro ao atualizar status', e)
    Notify.create({ type: 'negative', message: 'Erro ao atualizar a tarefa.', timeout: 3000 })
  } finally {
    Loading.hide()
  }
}

async function confirmRemove(id) {
  const confirmed = await confirmAction('Deseja remover esta tarefa?')
  if (!confirmed) return

  Loading.show({ message: 'Removendo tarefa...' })

  try {
    await requestStore.removeTask(id)
    Notify.create({ type: 'positive', message: 'Tarefa removida com sucesso!', timeout: 2500 })
  } catch (e) {
    console.error('Erro ao remover tarefa', e)
    Notify.create({ type: 'negative', message: 'Erro ao remover a tarefa.', timeout: 3000 })
  } finally {
    Loading.hide()
  }
}

function isVencida(task) {
  return !task.is_done && new Date(task.due_date) < new Date()
}

function getStatusLabel(task) {
  if (isVencida(task)) return 'Vencida'
  return task.is_done ? 'Concluída' : 'Pendente'
}

function getStatusColor(task) {
  if (isVencida(task)) return 'red'
  return task.is_done ? 'green' : 'orange'
}
</script>

<style>
.bg-filter {
  background-color: #0083a0 !important;
  color: white;
}

.my-table thead tr {
  background-color: #0083a0; 
  color: white;
}

.table-wrapper {
  max-width: 300px !important; 
  margin: 0 auto;
}

.my-table {
  font-size: 0.85rem; 
}

.inputFilter {
  background:  white !important;
  color: white !important;
}

.pageContainer {
  min-height: 100vh;
  position: relative;
  background-image:
    linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)),
    url('/images/bg-petiko.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}
</style>