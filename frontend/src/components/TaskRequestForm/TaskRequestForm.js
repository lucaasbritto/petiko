import { reactive, ref, computed, watch } from 'vue'
import { useRequestStore } from '../../stores/requests'
import { Notify } from 'quasar'

export function useTaskRequestFormScript(emit, props) {
  const requestStore = useRequestStore()
  const loading = ref(false)

  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const minDate = today.toISOString().split('T')[0]

  const form = reactive({
    title: '',
    description: '',
    due_date: '',
    is_done: false,
    user_id: '',
  })

  watch(() => props.task, (newTask) => {
    if (newTask) {
      form.title = newTask.title || ''
      form.description = newTask.description || ''
      form.due_date = newTask.due_date ? newTask.due_date.split('T')[0] : ''
      form.is_done = newTask.is_done
      form.user_id = newTask.user_id
    } else {
      form.title = ''
      form.description = ''
      form.due_date = ''
    }
  }, { immediate: true })
 
  function validateDate(val) {
    if (!val) return 'Data é obrigatória'
    const dueDate = parseDateLocal(val)
    dueDate.setHours(0, 0, 0, 0)
    if (dueDate < today) return 'Data deve ser hoje ou posterior'
    return true
  }

  function parseDateLocal(dateString) {
    const [year, month, day] = dateString.split('T')[0].split('-')
    return new Date(year, month - 1, day)
  }

  const formIsValid = computed(() => {
    return (
      form.title.trim() !== '' &&
      form.description.trim() !== '' &&
      form.due_date &&
      validateDate(form.due_date) === true
    )
  })

  async function submitForm() {
    if (!formIsValid.value) {
      Notify.create({
        type: 'negative',
        message: 'Preencha todos os campos corretamente!'
      })
      return
    }

    loading.value = true
    try {
      if (props.task && props.task.id) {        
        await requestStore.updateRequest(props.task.id, form)
        Notify.create({
          type: 'positive',
          message: 'Tarefa atualizada com sucesso!'
        })
      } else {
        await requestStore.createRequest(form)
        Notify.create({
          type: 'positive',
          message: 'Tarefa criada com sucesso!'
        })
      }
      emit('saved')
    } catch (e) {
      console.error('Erro ao salvar:', e)
      Notify.create({
        type: 'negative',
        message: 'Erro ao salvar a tarefa.'
      })
    } finally {
      loading.value = false
    }
  }

  return {
    form,
    loading,
    formIsValid,
    validateDate,
    minDate,
    submitForm
  }
}
