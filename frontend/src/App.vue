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
import { computed, watch, onMounted } from 'vue'
import { useUserStore } from './stores/user'

const router = useRouter()
const route = useRoute()
const userStore = useUserStore()

const isLoginPage = computed(() => route.name === 'Login')


function logout () {
  userStore.logout()
  router.push('/login')
}

onMounted(async () => {
  if (userStore.isAuthenticated) {
    if (!userStore.user) {
      await userStore.fetchUser()
    }
  }  
})

watch(() => userStore.isAuthenticated, async (newVal) => {
  if (newVal) {
    if (!userStore.user) {
      await userStore.fetchUser()
    }
  }
})
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
