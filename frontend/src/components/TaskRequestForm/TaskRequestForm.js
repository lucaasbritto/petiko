import { reactive, ref, computed } from 'vue'
import { useRequestStore } from '../../stores/requests'

export function useTaskRequestFormScript(emit) {
  const requestStore = useRequestStore()
  const loading = ref(false)

  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const minDate = today.toISOString().split('T')[0]

  const form = reactive({
    title: '',
    description: '',
    due_date: ''
  })

 
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
    if (!formIsValid.value) return

    loading.value = true
    try {
      await requestStore.createRequest(form)
      emit('saved')
    } catch (e) {
      console.error('Erro ao salvar:', e)
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
