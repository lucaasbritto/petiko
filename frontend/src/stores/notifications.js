import { defineStore } from 'pinia'
import { getNotifications, markAllNotificationsRead,markNotificationRead  } from '../api/notifications'
import { Notify } from 'quasar'

export const useNotificationStore = defineStore('notifications', {
  state: () => ({
    items: [],
    unreadCount: 0,
    lastFetchedIds: [],
  }),
  actions: {
    async fetch() {

      const data = await getNotifications()
      const newItems = data.filter(n => !this.lastFetchedIds.includes(n.id))

      this.items = data
      this.unreadCount = data.filter(n => !n.read_at).length
      this.lastFetchedIds = data.map(n => n.id)

      if (newItems.length > 0) {
        this.$emit?.('new-notification', newItems)
      }      
    },

    showToast(notification) {
      Notify.create({
        message: notification.data.title || 'Nova notificação',
        icon: 'notifications',
        color: 'primary',
        position: 'top-right',
        timeout: 4000
      })
    },

    playSound() {
      const audio = new Audio('/sounds/notification.mp3')
      audio.play().catch(() => {
      })
    },

    async markAllRead() {
      await markAllNotificationsRead()
      this.items = this.items.map(n => ({
        ...n,
        read_at: new Date().toISOString(),
      }))
      this.unreadCount = 0
    },

    async markOneRead(id) {
        const notif = this.items.find(n => n.id === id)
        if (notif && !notif.read_at) {
            notif.read_at = new Date().toISOString()
            this.unreadCount = this.items.filter(n => !n.read_at).length

            try {
            await markNotificationRead(id)
            } catch (error) {            
            notif.read_at = null
            this.unreadCount = this.items.filter(n => !n.read_at).length
            throw error
            }
        }
    }
  }
})
