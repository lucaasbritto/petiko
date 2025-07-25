<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide" persistent>
        <q-card style="min-width: 400px; max-width: 600px; width: 100%">
            <q-card-section class="row items-center q-pb-none">
            <div class="text-h6">{{ task ? 'Editar Tarefa' : 'Nova Tarefa' }}</div>
            <q-space />
            <q-btn icon="close" flat round dense @click="onDialogCancel" />
            </q-card-section>

            <q-separator />

            <q-card-section>
            <TaskRequestForm :task="task" @saved="handleSaved" @close="onDialogCancel" />
            </q-card-section>
        </q-card>
  </q-dialog>

</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import TaskRequestForm from './TaskRequestForm/TaskRequestForm.vue'
import { useDialogPluginComponent } from 'quasar'

const props = defineProps({
  task: Object 
})

defineEmits([
  ...useDialogPluginComponent.emits
])

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()

function handleSaved () {
    onDialogOK()
}
</script>