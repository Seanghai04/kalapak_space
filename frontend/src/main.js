import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import AOS from 'aos'
import 'aos/dist/aos.css'
import './assets/styles/animations.css'
import './assets/styles/glassmorphism.css'
import './assets/styles/main.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

AOS.init({
  duration: 800,
  easing: 'ease-out-cubic',
  once: true,
  offset: 50,
})

app.mount('#app')

// Hide static HTML footer once Vue has mounted (it exists for Google's crawler)
const staticFooter = document.getElementById('static-footer')
if (staticFooter) staticFooter.style.display = 'none'
