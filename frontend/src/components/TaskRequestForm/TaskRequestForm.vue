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
        autofocus
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

      <q-select
        v-if="props.task"
        v-model="form.is_done"
        label="Status"
        :options="[
          { label: 'Pendente', value: false },
          { label: 'Concluído', value: true }
        ]"
        emit-value
        map-options
        outlined
        dense
        autofocus
        class="q-mb-md"
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
          :label="props.task ? 'Editar' : 'Salvar'"
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
import { defineEmits, defineProps } from 'vue'
import { useTaskRequestFormScript } from './TaskRequestForm.js'

const emit = defineEmits(['close', 'saved'])
const props = defineProps({
  task: Object
})

const {
  form,
  loading,
  formIsValid,
  validateDate,
  minDate,
  submitForm
} = useTaskRequestFormScript(emit, props)

</script>

<style scoped>

</style>
