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
          v-if="userStore.isAdmin"
          label="Nova Tarefa"
          icon="add"
          color="primary"
          size="sm"
          dense
          rounded
          unelevated
          @click="openTaskDialog"
        />
      </div>

      <q-card flat bordered>
        <q-table
          :rows="requestStore.requests.filter(r => r && r.id)"
          :columns="columns"
          row-key="id"
          dense
          flat
          bordered
          class="shadow-1"
          :loading="requestStore.loading"
          :pagination.sync="pagination"
          :rows-per-page="10"
          :rows-per-page-options="[10,20,50, 100]"          
        >
          <template v-slot:body-cell-is_done="props">
            <q-td :props="props">
              <q-badge
                :color="getStatusColor(props.row)"
                rounded
              >
                {{ getStatusLabel(props.row) }}
              </q-badge>
            </q-td>
            <q-td align="center">
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
              <q-btn
                v-if="!props.row.is_done"
                dense
                flat
                round
                icon="done"
                color="green"
                @click="confirmUpdated(props.row.id)"
                :title="props.row.is_done ? 'Desmarcar como concluída' : 'Marcar como concluída'"
              />
              <q-btn
                dense
                flat
                round
                icon="delete"
                color="red"
                @click="confirmRemove(props.row.id)"
                title="Remover tarefa"
              />
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
import { Dialog, Loading, Notify   } from 'quasar'
import { useQuasar } from 'quasar'
import TaskRequestFormDialog from '../../components/TaskRequestFormDialog.vue'
import { useUserStore } from '../../stores/user'

const editingTask = ref(null)
const userStore = useUserStore()

const $q = useQuasar()

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

onMounted(() => {
  requestStore.fetchRequests()
})

function openTaskDialog() {  
  Dialog.create({
  component: TaskRequestFormDialog,
  componentProps: {
    persistent: true,
  }
}).onOk(() => {
}).onCancel(() => {
}).onDismiss(() => {
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
  }).onOk(() => {
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
      ok: {
        label: 'Confirmar',
        color: 'primary'
      },
      cancel: {
        label: 'Cancelar',
        color: 'negative'
      }
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
    Notify.create({
      type: 'positive',
      message: 'Situação alterada com sucesso!',
      timeout: 2500
    })
  } catch (e) {
    console.error('Erro ao atualizar status', e)
    Notify.create({
      type: 'negative',
      message: 'Erro ao atualizar a tarefa.',
      timeout: 3000
    })
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
    Notify.create({
      type: 'positive',
      message: 'Tarefa removida com sucesso!',
      timeout: 2500
    })
  } catch (e) {
    console.error('Erro ao remover tarefa', e)
    Notify.create({
      type: 'negative',
      message: 'Erro ao remover a tarefa.',
      timeout: 3000
    })
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

<style lang="scss" scoped>
@use './DashboardView.scss';
</style>