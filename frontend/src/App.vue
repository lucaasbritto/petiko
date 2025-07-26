<template>
  <q-layout view="hHh lpR fFf">
    <q-header v-if="!isLoginPage && userStore.isAuthenticated" elevated class="my-header">
      <q-toolbar class="q-px-md q-py-sm justify-between">
          <q-img
            src="/images/logo-petiko.svg"
            alt="Logo"
            style="height: 32px; width: 100px;"
            no-spinner
            no-transition
            class="q-mr-auto"
          />

        <div class="row items-center q-gutter-sm">          
          <div class="q-mr-md">Bem-vindo, {{ userStore.userName }}</div>
          <q-btn round dense flat icon="notifications" >
            <q-badge v-if="notificationStore.unreadCount > 0" color="red" floating>
              {{ notificationStore.unreadCount }}
            </q-badge>

            <q-menu v-model="menuOpen" anchor="bottom end" self="top end">
              <q-list style="min-width: 280px; max-width: 360px; max-height: 400px;" bordered>
                <q-item-label header>Notificações</q-item-label>

                <q-item
                  v-for="n in notificationStore.items"
                  :key="n.id"
                  clickable
                  @click="markAndNavigate(n)"
                  class="q-py-sm"
                  :class="{ 'bg-blue-1': !n.read_at }"
                >
                  <q-item-section avatar>
                    <q-icon name="notifications_active" :color="!n.read_at ? 'primary' : 'grey'" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-weight-medium">
                      {{ n.data.title }}
                    </q-item-label>
                    <q-item-label caption>{{ formatDate(n.created_at) }}</q-item-label>
                  </q-item-section>
                </q-item>

                <q-item v-if="notificationStore.items.length === 0">
                  <q-item-section>Sem notificações</q-item-section>
                </q-item>

                <q-separator spaced />
                <q-item clickable @click="notificationStore.markAllRead">
                  <q-item-section avatar>
                    <q-icon name="done_all" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Marcar todas como lidas</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
          <q-btn flat dense round icon="logout" @click="logout" title="Sair" />
        </div>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { computed, watch, onMounted, ref } from 'vue'
import { useUserStore } from './stores/user'
import { useNotificationStore } from './stores/notifications'
import { Notify } from 'quasar'

const router = useRouter()
const route = useRoute()
const userStore = useUserStore()
const notificationStore = useNotificationStore()
const menuOpen = ref(false)

const isLoginPage = computed(() => route.name === 'Login')


function logout () {
  userStore.logout()
  router.push('/login')
}



function markAndNavigate(notification) {
  notificationStore.markOneRead(notification.id)
}

let firstFetchDone = false
  onMounted(async () => {
    if (userStore.isAuthenticated) {
      if (!userStore.user) {
        await userStore.fetchUser()
      }
      await notificationStore.fetch()
      
    }

    setInterval(async () => {
      if (!userStore.isAuthenticated) return

      const oldIds = notificationStore.items.map(n => n.id)
      await notificationStore.fetch()

      const newOnes = notificationStore.items.filter(n => !oldIds.includes(n.id))

      if (!firstFetchDone) {
        firstFetchDone = true
        return
      }

      if (newOnes.length > 0) {
        
        new Audio('/sounds/notification.ogg').play().catch(() => {})
        
        Notify.create({
          type: 'info',
          message: "Você tem uma nova tarefa",
          timeout: 3000,
          position: 'top-right',
          icon: 'notifications_active'
        })
      }
    }, 30000)
  })

  watch(() => userStore.isAuthenticated, async (newVal) => {
    if (newVal) {
      if (!userStore.user) {
        await userStore.fetchUser()
      }
      await notificationStore.fetch()
    }
  })

  function formatDate (dateStr) {
    const date = new Date(dateStr)
    return date.toLocaleString('pt-BR', {
      dateStyle: 'short',
      timeStyle: 'short',
    })
  }
</script>

<style>
.scroll {
  overflow-y: auto;
}

.my-header {
  background-color: white !important;
  color: #0083a0 !important;
  font-weight: bold;
}
</style>
