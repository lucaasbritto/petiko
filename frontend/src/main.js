import { createApp } from 'vue'
import './assets/global.scss'
import App from './App.vue'
import { createPinia } from 'pinia'
import router from './router'
import { Quasar, Dialog, Notify, Loading  } from 'quasar'
import quasarIconSet from 'quasar/icon-set/material-icons'
import 'quasar/src/css/index.sass'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/dist/quasar.css'

import api from './api'

const app = createApp(App)
const pinia = createPinia()

app.config.globalProperties.$api = api

app.use(Quasar, {
  plugins: {
    Dialog,
    Notify,
    Loading
  },
  iconSet: quasarIconSet,
})

app.use(pinia)
app.use(router)
app.mount('#app')
