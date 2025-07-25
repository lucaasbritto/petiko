import { defineStore } from 'pinia'
import { login as apiLogin, logout as apiLogout } from '../api/auth'
import { getUserProfile, getUsers  } from '../api/user'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    usuarios: [],
    usuariosCarregados: false
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userName: (state) => state.user?.name || 'Visitante',
    isAdmin: (state) => state.user?.is_admin,  
  },

  actions: {
    async login(email, password) {
      const { token, user } = await apiLogin(email, password)
      this.token = token
      this.user = user

      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))
    },

    async fetchUser() {
      try {
        const user = await getUserProfile()
        this.user = user
        localStorage.setItem('user', JSON.stringify(user))
      } catch (err) {
        this.logout()
      }
    },

    async fetchUsuarios() {
      if (!this.usuariosCarregados) {
        const usuarios = await getUsers()
        this.usuarios = usuarios
        this.usuariosCarregados = true
      }
    },

    logout() {
      apiLogout()
      this.token = null
      this.user = null
    },
  },
})