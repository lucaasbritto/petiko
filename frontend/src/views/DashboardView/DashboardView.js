import { reactive, ref, computed, onMounted } from 'vue'
import { useRequestStore } from '../../stores/requests'
import { useUserStore } from '../../stores/user'
import { Dialog, Loading, Notify } from 'quasar'
import TaskRequestFormDialog from '../../components/TaskRequestFormDialog.vue'

export function useDashboardScript() {
  const requestStore = useRequestStore()
  const userStore = useUserStore()

  const filters = reactive(requestStore.filters)
  const pagination = reactive(requestStore.pagination)

  const editingTask = ref(null)
  const taskToView = ref(null)
  const taskViewOpen = ref(false)

  const columns = computed(() => {
    const base = [
      { name: 'id', label: 'ID', field: 'id', align: 'left' },
      { name: 'title', label: 'Título', field: 'title', align: 'left' },
      { name: 'description', label: 'Descrição', field: 'description', align: 'left' },
      { name: 'due_date', label: 'Data', field: row => formatDateBR(row.due_date), align: 'left' },
      { name: 'is_done', label: 'Situação', field: 'is_done', align: 'left' },
      { name: 'actions', label: 'Ações', field: 'actions', align: 'center' }
    ]

    if (userStore.isAdmin) {
      base.splice(5, 0, {
        name: 'user_id',
        label: 'Responsável',
        field: row => row.user?.name || '-',
        align: 'left',
      })
    }

    return base
  })

  function applyFilter(key, value) {
    requestStore.setFilter(key, value)
  }

  function formatDateBR(dateStr) {
    if (!dateStr) return ''
    const [year, month, day] = dateStr.split('T')[0].split('-')
    return `${day}/${month}/${year}`
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

  async function confirmAction(message = 'Tem certeza?') {
    return new Promise(resolve => {
      Dialog.create({
        title: 'Confirmação',
        message,
        cancel: true,
        persistent: true,
        ok: { label: 'Confirmar', color: 'primary' },
        cancel: { label: 'Cancelar', color: 'negative' }
      }).onOk(() => resolve(true))
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
      Notify.create({ type: 'positive', message: 'Situação alterada com sucesso!' })
    } catch (e) {
      Notify.create({ type: 'negative', message: 'Erro ao atualizar a tarefa.' })
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
      Notify.create({ type: 'positive', message: 'Tarefa removida com sucesso!' })
    } catch (e) {
      Notify.create({ type: 'negative', message: 'Erro ao remover a tarefa.' })
    } finally {
      Loading.hide()
    }
  }

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
    }).onDismiss(() => {
      editingTask.value = null
    })
  }

  function openTaskView(task) {
    taskToView.value = task
    taskViewOpen.value = true
  }


  onMounted(() => {
    requestStore.fetchRequests()
    if (userStore.isAdmin) userStore.fetchUsuarios()
  })

  return {
    userStore,
    requestStore,
    filters,
    pagination,
    columns,
    formatDateBR,
    applyFilter,
    getStatusColor,
    getStatusLabel,
    openTaskDialog,
    openEditDialog,
    confirmUpdated,
    confirmRemove,
    taskToView,
    editingTask,
    taskViewOpen,
    openTaskView,
  }
}