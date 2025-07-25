<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide" persistent>
        <q-card style="min-width: 400px; max-width: 600px; width: 100%">
            <q-card-section class="row items-center q-pb-none">
            <div class="text-h6">Nova Tarefa</div>
            <q-space />
            <q-btn icon="close" flat round dense @click="onDialogCancel" />
            </q-card-section>

            <q-separator />

            <q-card-section>
            <TaskRequestForm @saved="handleSaved" @close="onDialogCancel" />
            </q-card-section>
        </q-card>
  </q-dialog>

</template>

<script setup>
import { defineEmits } from 'vue'
import { useQuasar } from 'quasar'
import TaskRequestForm from './TaskRequestForm/TaskRequestForm.vue'
import { useDialogPluginComponent } from 'quasar'

defineEmits([
  ...useDialogPluginComponent.emits
])

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()
const $q = useQuasar()

function handleSaved () {
    onDialogOK()
    $q.notify({
    color: 'green',
    icon: 'check',
    message: 'Tarefa salva com sucesso!',
    position: 'top-right',
    timeout: 3000,
    actions: [{ icon: 'close', color: 'white' }]
  })
}
</script>