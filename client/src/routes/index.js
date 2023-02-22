import { createRouter, createWebHistory } from 'vue-router'
import Home from '../components/Pages/Home.vue'
import Login from '../components/Pages/Login.vue'
import Register from '../components/Pages/Register.vue'
import Profile from '../components/Pages/Profile.vue'
import EditProfile from '../components/Pages/EditProfile.vue'
import Comments from '../components/Pages/Comments.vue'
import EditTweet from '../components/Pages/EditTweet.vue'
import Search from '../components/Pages/Search.vue'

const routes = [
  { path: '/', component: Login },
  { path: '/Register', component: Register },
  { path: '/Home', component: Home },
  { path: '/Profile', component: Profile },
  { path: '/Edit-profile', component: EditProfile },
  { path: '/Comments/:id', component: Comments },
  { path: '/Edit-tweet/:id', component: EditTweet },
  { path: '/Search', component: Search },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
