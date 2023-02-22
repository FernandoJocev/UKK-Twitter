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
            :src="'data:image/png;base64,' + state.imageNotNull"
            alt="Username"
            width="50"
            height="50"
            v-if="state.imageNotNull != null"
          />
          <span v-else>
            {{ state.image }}
          </span>
        </router-link>
        <i class="fa-solid fa-right-from-bracket" @click="Logout"></i>
      </div>
    </div>

    <div class="main-content">
      <form @submit="updatePost">
        <input
          type="text"
          placeholder="Tweet"
          name="tweet"
          v-model="FormData.tweet"
        />
        <input type="file" @change="FileHandler" name="profile" ref="file" />
        <button type="submit">Edit tweet</button>
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
import axios from 'axios'
import { onMounted, reactive, ref } from 'vue'
import moment from 'moment'
import { useRoute } from 'vue-router'

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
          state.image = data.username.charAt(0)
          state.user = data
          return getPost()
        }
        state.imageNotNull = data.profile
        state.user = data
        return getPost()
      } catch (e) {
        return (state.message = e.response)
      }
    })

    const file = ref(null)

    const FormData = reactive({
      tweet: null,
      media: null,
    })

    const state = reactive({
      successMessage: null,
      errorMessage: null,
      image: null,
      tweets: null,
      user: null,
      imageNotNull: null,
    })

    const route = useRoute()

    const getPost = async () => {
      try {
        const { data } = await API.get(`main/getPost/${route.params.id}`)
        console.log(data.data.tags)

        const arr = []

        if (data.data.tags.length > 0) {
          for (let i = 0; i < data.data.tags.length; i++) {
            arr.push(data.data.tags[i].tag.name)
          }
          console.log(arr.join(' #'))
          return FormData.tweet = data.data.tweet + arr.join(' #')
        }
        FormData.tweet = data.data.tweet
        return
      } catch (e) {
        return (state.message = e.response)
      }
    }

    const updatePost = async (e) => {
      e.preventDefault()

      try {
        const { data } = await API.post(
          `main/updatePost/${route.params.id}`,
          FormData
        )
        return (state.successMessage = data.message)
      } catch (e) {
        return (state.errorMessage = e.response.message)
      }
    }

    const Logout = () => {
      sessionStorage.removeItem('token')

      return (window.location.href = '/')
    }

    const FileHandler = () => {
      const image = file.value.files[0]
      let reader = new FileReader()
      reader.onloadend = function () {
        const cover = reader.result.replace('data:', '').replace(/^.+,/, '')

        FormData.media = cover
      }
      reader.readAsDataURL(image)
    }

    return { state, Logout, FormData, updatePost, FileHandler, file }
  },

  methods: {
    format_date(value) {
      return moment(String(value)).format('DD MMM YYYY')
    },
  },
}
</script>

<style scoped>
button {
  cursor: pointer;
}

a {
  text-decoration: none;
  color: white;
}

i {
  font-size: 30px;
  cursor: pointer;
}

.likes i,
.comments i {
  opacity: 50%;
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
