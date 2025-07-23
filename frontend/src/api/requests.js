import api from './index'

export async function getRequests(filters = {}) {
  const response = await api.get('/task', { params: filters });
  return response.data;
}