export function isVencida(task) {
  if (task.is_done) return false

  const today = new Date()
  const due = new Date(task.due_date)

  const todayStr = today.toISOString().slice(0, 10)
  const dueStr = due.toISOString().slice(0, 10)

  return dueStr < todayStr
}

export function formatDateBR(dateStr) {
  if (!dateStr) return ''
  const [year, month, day] = dateStr.split('T')[0].split('-')
  return `${day}/${month}/${year}`
}

export function getStatusLabel(task) {
    if (isVencida(task)) return 'Vencida'
    return task.is_done ? 'Concluída' : 'Pendente'
  }

export function getStatusColor(task) {
    if (isVencida(task)) return 'red'
    return task.is_done ? 'green' : 'orange'
  }