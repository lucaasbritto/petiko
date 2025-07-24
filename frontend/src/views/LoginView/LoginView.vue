<template>
  <div class="q-pa-md flex flex-center login-page">    
    <q-card class="q-pa-xl login-card">
      <div class="text-center q-mb-md">
        <img src="/images/logo-petiko.svg" alt="Logo" class="logo q-mb-sm" />
        <div class="text-grey">Acesso ao portal do seu PET</div>
      </div>

      <q-form @submit.prevent="handleLogin" class="q-gutter-md">
        <q-input
          v-model="email"
          label="Email"
          type="email"
          filled
          :disable="loading"
          lazy-rules
          :rules="[val => !!val || 'Campo obrigatório']"
          stack-label
        />

        <q-input
          v-model="password"
          label="Senha"
          type="password"
          :disable="loading"
          filled
          lazy-rules
          :rules="[val => !!val || 'Campo obrigatório']"
          stack-label
        />

        <q-btn
          type="submit"
          label="Entrar"
          class="full-width btn-petiko"
          :loading="loading"
          :disable="loading"
          no-caps
        >
          <template v-slot:loading>
            <q-spinner-dots size="20px" class="q-mr-sm" />
            Entrando...
          </template>
        </q-btn>

        <q-banner
          v-if="error"
          class="bg-red-2 text-red q-mt-md"
          dense
          rounded
        >
          {{ error }}
        </q-banner>
      </q-form>

      <div class="text-center q-mt-md text-grey-6 text-caption">
        © 2025 Petiko
      </div>
    </q-card>
  </div>
</template>

<script setup>
import { loginViewScript } from './loginview.js'

  const {
    email,
    password,
    error,
    loading,
    handleLogin
  } = loginViewScript()
</script>

<style lang="scss" scoped>
    @use './LoginView.scss'; 
</style>