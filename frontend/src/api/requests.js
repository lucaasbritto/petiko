import api from './index'

export async function getRequests(filters = {}) {
  const response = await api.get('/task', { params: filters });
  return response.data;
}

export async function createRequest(data) {
  const response = await api.post('/task', data);
  return response.data.data;
}

export async function updateRequest(id, payload) {
  const response = await api.put(`/task/${id}`, payload)
  return response.data.data
}

export async function updateRequestStatus(id) {
  const response = await api.patch(`/task/${id}/updateStatus`)
  return response.data.data
}

export async function deleteRequest(id) {
   const response = await api.delete(`/task/${id}`)
   return response.data.data
}

export function exportTasks() {
  return api.get('/task/export', {
    responseType: 'blob',
  })
}