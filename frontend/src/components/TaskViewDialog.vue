<template>
  <q-dialog 
    :model-value="modelValue"     
    persistent
    transition-show="scale"
    transition-hide="scale">
        <q-card
        class="q-pa-md"
        style="min-width: 420px; max-width: 600px; border-radius: 12px;"
        flat
        bordered
        >
        <q-card-section class="row items-center q-pb-sm" style="border-bottom: 1px solid #eee;">
            <q-icon name="assignment" size="28px" color="primary" class="q-mr-sm" />
            <div class="text-h6 text-primary">Detalhes da Tarefa</div>
            <q-space />
            <q-btn dense flat round icon="close" color="primary" @click="close" aria-label="Fechar" />
        </q-card-section>

        <q-card-section class="q-pt-md q-pb-md" style="line-height: 1.6;">
            <div><strong>Título:</strong> <span class="text-weight-medium">{{ task.title }}</span></div>
            <div class="q-mt-sm"><strong>Descrição:</strong> <span class="text-body2">{{ task.description }}</span></div>
            <div class="q-mt-sm"><strong>Data:</strong> {{ formatDateBR(task.due_date) }}</div>
            <div v-if="isAdmin" class="q-mt-sm">
                <strong>Responsável:</strong> {{ task.user?.name || '—' }}
            </div>
            <div class="q-mt-sm"><strong>Situação:</strong>
                <q-chip
                    :color="task.is_done ? 'green' : (isVencida(task) ? 'red' : 'orange')"
                    text-color="white"
                    dense
                    square
                    class="q-ml-sm"
                    >
                    {{ task.is_done ? 'Concluída' : (isVencida(task) ? 'Pendente (Vencida)' : 'Pendente') }}
                </q-chip>
            </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pt-none">
            <q-btn
            label="Fechar"
            color="primary"
            unelevated
            rounded
            @click="close"
            />
        </q-card-actions>
        </q-card>
  </q-dialog>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import { isVencida, formatDateBR } from '../utils/taskUtils'

const props = defineProps({
  modelValue: Boolean,
  task: Object,
  isAdmin: Boolean
})

const emit = defineEmits(['update:modelValue'])

function close() {
  emit('update:modelValue', false)
}

</script>

<style scoped>
.text-weight-medium {
  font-weight: 500;
}
</style>