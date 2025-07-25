<template>
  <div class="q-pa-md" style="">
    <q-form @submit.prevent="submitForm" class="" >      
      <q-input
        v-model="form.title"
        label="Título"
        outlined
        dense
        clearable
        class="full-width"
        :rules="[val => !!val || 'Título é obrigatório']"
      />

      <q-input
        v-model="form.description"
        label="Descrição"
        type="textarea"
        outlined
        dense
        clearable
        class="full-width"
        :rules="[val => !!val || 'Descrição é obrigatória']"
      />

      <q-input
        v-model="form.due_date"
        label="Data de Vencimento"
        type="date"
        outlined
        dense
        clearable
        class="full-width"
        :rules="[validateDate]"
        :min="minDate"
      />

      <div class=" row justify-end q-gutter-sm q-mt-md">
        <q-btn
          label="Cancelar"
          flat
          color="secondary"
          @click="$emit('close')"
          :disable="loading"
        />
        <q-btn
          label="Salvar"
          color="primary"
          type="submit"
          dense
          :loading="loading"
          :disable="!formIsValid || loading"
        />
      </div>
    </q-form>
  </div>
</template>

<script setup>
import { defineEmits } from 'vue'
import { useTaskRequestFormScript } from './TaskRequestForm.js'

const emit = defineEmits(['close', 'saved'])
const {
  form,
  loading,
  formIsValid,
  validateDate,
  minDate,
  submitForm
} = useTaskRequestFormScript(emit)
</script>

<style scoped>

</style>
