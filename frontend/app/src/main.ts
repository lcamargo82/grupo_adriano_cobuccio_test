import './assets/main.css'
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';

import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia';

const app = createApp(App)

const pinia = createPinia();
app.use(pinia);

app.use(Toast, {
  position: POSITION.TOP_RIGHT,
});

app.use(router)

app.mount('#app')
