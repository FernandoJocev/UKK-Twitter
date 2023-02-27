<template>
  <div class="container">
    <div class="sidebar">
      <div class="twitter">
        <router-link to="/Home">
          <i class="fa-brands fa-twitter"></i>
        </router-link>
      </div>

      <div class="main-nav">
        <router-link to="/Home">
          <i class="fa-solid fa-house"></i>
        </router-link>
        <i class="fa-solid fa-hashtag"></i>
        <i class="fa-regular fa-heart"></i>
      </div>

      <div class="profile-logout">
        <router-link to="/Profile">
          <img
            :src="'data:image/png;base64,' + state.image"
            alt=""
            width="50"
            height="50"
            v-if="FormData.profile != null"
          />
          <span v-else>
            {{ state.image }}
          </span>
        </router-link>
        <i class="fa-solid fa-right-from-bracket" @click="Logout"></i>
      </div>
    </div>

    <div class="main-content">
      <form @submit="updateProfile">
        <input
          type="text"
          placeholder="Username"
          v-model="FormData.username"
          name="username"
        />
        <input
          type="email"
          placeholder="Email"
          v-model="FormData.email"
          name="email"
        />
        <input type="file" @change="FileHandler" name="profile" ref="file" />
        <input
          type="text"
          placeholder="Bio"
          v-model="FormData.bio"
          name="bio"
        />
        <button type="submit">Edit profile</button>
        <div
          class="alert"
          style="
            display: flex;
            flex-direction: column;
            background-color: #ff0f0f;
            border-radius: 5px;
            margin-top: 20px;
            padding: 10px 10px;
          "
          v-if="state?.errorMessage != null"
        >
          <div class="title-error">
            <h1 style="font-weight: 700; font-size: 28px">
              {{ state?.errorMessage }}
            </h1>
          </div>
          <div class="message">{{ state?.errorMessage }}</div>
        </div>

        <div
          class="alert"
          style="
            display: flex;
            flex-direction: column;
            background-color: #14a44d;
            border-radius: 5px;
            margin-top: 20px;
            padding: 10px 10px;
          "
          v-if="state?.successMessage != null"
        >
          <div class="title-error">
            <h1 style="font-weight: 700; font-size: 28px">
              {{ state?.successMessage }}
            </h1>
          </div>
          <div class="message">{{ state?.successMessage }}</div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import moment from 'moment'

const API = axios.create({
  baseURL: 'http://127.0.0.1:8000/api/',
})
export default {
  setup() {
    onMounted(async () => {
      const token = sessionStorage.getItem('token')

      if (token == null) {
        return (window.location.href = '/')
      }

      try {
        const { data } = await API.get(`auth/getUser/${token}`)
        if (data.profile == null) {
          FormData.username = data.username
          FormData.email = data.email
          FormData.profile = data.profile
          FormData.bio = data.bio
          state.image = data.username.charAt(0)
          state.user = data
          return getUser()
        }
        FormData.username = data.username
        FormData.email = data.email
        FormData.profile = data.profile
        FormData.bio = data.bio
        state.image = data.profile
        state.user = data
        return getUser()
      } catch (e) {
        return (state.message = e.response.message)
      }
    })

    const FormData = reactive({
      user_id: null,
      username: null,
      email: null,
      profile: null,
      bio: null,
    })

    const state = reactive({
      errorMessage: null,
      successMessage: null,
      user: null,
      image: null,
    })

    const file = ref(null)

    const getUser = async () => {
      try {
        const { data } = await API.get('auth/getUsers')
        state.user = data.data
        console.log(data.data)
        if (state.image == null) {
          state.image = data.data.username.charAt(0)
        }
      } catch (e) {
        state.errorMessage = e.response.message
      }
    }

    const updateProfile = async (e) => {
      e.preventDefault()

      const id = sessionStorage.getItem('token')
      FormData.user_id = id
      try {
        const { data } = await API.post('auth/editProfile', FormData)

        state.successMessage = data.message

        setTimeout(() => {
          ;(state.errorMessage = null), (state.successMessage = null)
        }, 3000)
        getUser()

        window.location.href = '/Profile' 
        return (state.message = data.message)
      } catch (e) {
        setTimeout(() => {
          ;(state.errorMessage = null), (state.successMessage = null)
        }, 3000)
        return (state.message = e.response.data.message)
      }
    }

    const FileHandler = () => {
      const image = file.value.files[0]
      let reader = new FileReader()
      reader.onloadend = function () {
        const cover = reader.result.replace('data:', '').replace(/^.+,/, '')

        FormData.profile = cover
      }
      reader.readAsDataURL(image)
    }

    const Logout = () => {
      sessionStorage.clear('token')

      window.location.href = '/'
    }

    const RemoveImage = () => {
      return (FormData.media = null)
    }

    setTimeout(() => {
      ;(state.errorMessage = null), (state.successMessage = null)
    }, 3000)
    return {
      getUser,
      updateProfile,
      Logout,
      FormData,
      FileHandler,
      state,
      file,
      RemoveImage,
    }
  },

  methods: {
    format_date(value) {
      if (value != null) {
        return moment(String(value)).format('DD MMM YYYY')
      }
    },
  },
}
</script>

<style scoped>
a {
  color: white;
  text-decoration: none;
}

input {
  outline: none;
}

i {
  font-size: 30px;
  cursor: pointer;
}

button {
  cursor: pointer;
}

.container {
  display: flex;
  justify-content: space-between;
}

.sidebar {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100vh;
  row-gap: 50px;
  border-right: 1px solid rgba(255, 255, 255, 90%);
  padding: 20px 20px;
}

.twitter {
  margin-bottom: 20px;
}

.main-nav {
  display: flex;
  flex-direction: column;
  margin-bottom: 200px;
  row-gap: 40px;
}

.profile-logout {
  margin-bottom: 20px;
  display: flex;
  height: 100%;
  flex-direction: column;
  align-items: center;
  row-gap: 20px;
}

.profile-logout span,
.profile-logout img {
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255, 255, 255, 50%);
  cursor: pointer;
  transition: 0.5s;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 100%);
  width: 35px;
  height: 35px;
  color: black;
  font-weight: 700;
  font-size: 25px;
}

.profile-logout span:hover,
.profile-logout img:hover {
  cursor: pointer;
  background-color: rgb(45, 44, 44);
  color: white;
}

.main-content {
  height: 100vh;
  flex: 1;
  display: flex;
  justify-content: center;
}

.main-content form {
  row-gap: 20px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.main-content input {
  outline: none;
  border: 1px solid rgba(255, 255, 255, 90%);
  background-color: transparent;
  border-radius: 20px;
  padding: 20px 40px;
  width: 700px;
  color: white;
  font-weight: 500;
  font-size: 18px;
}

.main-content input::placeholder {
  font-size: 18px;
  font-weight: 550;
}

.main-content button {
  background-color: #2aa9e0;
  border-radius: 5px;
  color: white;
  padding: 20px 0px;
  font-weight: 550;
  font-size: 18px;
}
</style>
