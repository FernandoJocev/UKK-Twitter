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
      <div class="main-post">
        <img
          :src="'data:image/png;base64,' + state?.tweets?.media"
          alt="Post"
          width="750"
          height="450"
          v-if="state?.tweets?.media != null"
          style="border-radius: 5px"
        />
        <div class="posted-by">
          <h4>{{ state?.tweets?.user?.username }}</h4>
          <p style="text-transform: lowercase">
            @{{ state?.tweets?.user?.username }}
          </p>
          <p>{{ format_date(state?.tweets?.created_at) }}</p>
        </div>
        <p style="font-size: 20px; font-weight: 550">
          {{ state?.tweets?.tweet }}
        </p>
      </div>

      <div class="contents" v-for="data in state?.comments">
        <div class="content">
          <div class="profile-pict">
            <img
              :src="'data:image/png;base64,' + data?.user?.profile"
              :alt="data?.user?.username"
              v-if="data?.user?.profile != null"
            />
            <div class="no-profile" v-else>
              <span>
                {{ state.image }}
              </span>
            </div>
          </div>
          <div class="tweet">
            <div class="username-posted-on">
              <h4>{{ data?.user?.username }}</h4>
              <p style="text-transform: lowercase">
                @{{ data?.user?.username }}
              </p>
              <p>{{ format_date(data?.created_at) }}</p>
              <div class="edit" v-if="state?.user?.email === data?.user?.email">
                <label for="getEditComment">
                  <i class="fa-regular fa-pen-to-square"></i>
                </label>
                <button
                  id="getEditComment"
                  style="display: none"
                  @click="getEditComment(data?.id)"
                ></button>
                <label for="deleteComment">
                  <i class="fa-regular fa-square-minus"></i>
                </label>
                <button
                  id="deleteComment"
                  style="display: none"
                  @click="DeleteComment(data?.id)"
                ></button>
              </div>
            </div>
            <p>{{ data?.comments }}</p>
            <div class="tweet-text tweet-image" v-for="datas in data?.tags">
              <div
                class="tweet-text tweet-image"
                v-if="datas?.tag != null"
                style="display: flex; column-gap: 5px"
              >
                <p style="color: #0d6efd; cursor: pointer">
                  #{{ datas?.tag?.name }}
                </p>
              </div>
            </div>
            <img
              :src="'data:image/png;base64,' + data?.media"
              alt="Image"
              v-if="data?.media != null"
              width="450"
              height="250"
            />
          </div>
        </div>
      </div>

      <div class="comment-section">
        <form @submit="Comment" v-if="state?.updateData == false">
          <input
            type="text"
            placeholder="Write a commment"
            name="comment"
            v-model="FormData.comment"
          />

          <div class="image-upload">
            <label for="add-image">
              <i style="margin-right: 35px" class="fa-regular fa-image"></i>
            </label>
            <input
              type="file"
              id="add-image"
              @change="FileHandler"
              style="display: none"
              ref="file"
            />
          </div>

          <div class="image-preview" v-if="FormData.media != null">
            <img
              :src="'data:image/png;base64,' + FormData?.media"
              alt="Preview"
              width="450"
              height="250"
              v-if="FormData.media != null"
            />
            <div class="remove-image">
              <button @click="RemoveImage">
                <i class="fa-solid fa-ban"></i>
              </button>
            </div>
          </div>

          <label for="button">
            <i class="fa-solid fa-paper-plane"></i>
          </label>
          <button id="button" type="submit" style="display: none"></button>
        </form>

        <form @submit="updateComment" v-else="state?.updateData == true">
          <input
            type="text"
            placeholder="Write a commment"
            name="comment"
            v-model="FormData.comment"
          />

          <div class="image-upload">
            <label for="add-image">
              <i style="margin-right: 35px" class="fa-regular fa-image"></i>
            </label>
            <input
              type="file"
              id="add-image"
              @change="FileHandler"
              style="display: none"
              ref="file"
            />
          </div>

          <div class="image-preview" v-if="FormData.media != null">
            <img
              :src="'data:image/png;base64,' + FormData?.media"
              alt="Preview"
              width="450"
              height="250"
              v-if="FormData.media != null"
            />
            <div class="remove-image">
              <button @click="RemoveImage">
                <i class="fa-solid fa-ban"></i>
              </button>
            </div>
          </div>

          <label for="button">
            <i class="fa-solid fa-paper-plane"></i>
          </label>
          <button id="button" type="submit" style="display: none"></button>
        </form>
      </div>
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
    const route = useRoute()

    onMounted(async () => {
      const token = sessionStorage.getItem('token')

      if (token == null) {
        return (window.location.href = '/')
      }

      FormData.token = token

      try {
        const { data } = await API.get(`auth/getUser/${token}`)

        // console.log(data)

        if (data.profile == null) {
          state.image = data.username.charAt(0)
          state.user = data
          return getComments() && getPost()
        }
        state.imageNotNull = data.profile
        state.user = data
        return getComments() && getPost()
      } catch (e) {
        return (state.message = e.response.message)
      }
    })

    const state = reactive({
      message: null,
      comments: null,
      image: null,
      tweets: null,
      id_tweet: null,
      user: null,
      imageNotNull: null,
      updateData: false,
    })

    const FormData = reactive({
      id_comment: null,
      comment: null,
      token: null,
      media: null,
    })

    const file = ref(null)

    const getPost = async () => {
      try {
        const { data } = await API.get(`main/getPost/${route.params.id}`)

        return (state.tweets = data.data)
      } catch (e) {
        return (state.message = e.response.message)
      }
    }

    const getComments = async () => {
      try {
        const { data } = await API.get(`main/getComments/${route.params.id}`)

        console.log(data)

        return (state.comments = data)
      } catch (e) {
        return (state.message = e.response.message)
      }
    }

    const Comment = async (e) => {
      e.preventDefault()

      try {
        console.log(FormData.media)
        const { data } = await API.post(
          `main/comment/${route.params.id}`,
          FormData
        )
        console.log(data)
        getComments()
      } catch (e) {
        return (state.message = e.response.message)
      }
    }

    const getEditComment = async (id) => {
      try {
        const { data } = await API.get(`main/getEditComment/${id}`)

        FormData.comment = data.data.comments
        FormData.id_comment = data.data.id
        state.updateData = true
        return
      } catch (e) {
        return (state.message = e.response)
      }
    }

    const updateComment = async (e) => {
      e.preventDefault()
      try {
        await API.post('main/updateComment/', FormData)
        state.updateData = false
        return getComments()
      } catch (e) {
        return (state.message = e.response)
      }
    }

    const DeleteComment = async (id) => {
      try {
        await API.post(`main/deleteComment/${id}`)

        return getComments()
      } catch (e) {
        return (state.message = e.response)
      }
    }

    const FileHandler = () => {
      console.log(file.value)
      const image = file.value.files[0]
      let reader = new FileReader()
      reader.onloadend = function () {
        const cover = reader.result.replace('data:', '').replace(/^.+,/, '')

        FormData.media = cover
      }
      reader.readAsDataURL(image)
    }

    const RemoveImage = () => {
      return (FormData.media = null)
    }

    const Logout = () => {
      sessionStorage.removeItem('token')

      return (window.location.href = '/')
    }

    return {
      state,
      FormData,
      Logout,
      Comment,
      RemoveImage,
      getEditComment,
      updateComment,
      FileHandler,
      file,
      DeleteComment,
    }
  },

  methods: {
    format_date(value) {
      return moment(String(value)).format('DD MMM YYYY')
    },
  },
}
</script>

<style scoped>
img {
  border-radius: 5px;
}

a {
  text-decoration: none;
  color: white;
}

i {
  font-size: 30px;
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
  flex: 1;
  display: flex;
  flex-direction: column;
  height: 100vh;
  border-right: 1px solid rgba(255, 255, 255, 90%);
  height: 100vh;
  overflow-y: scroll;
  justify-content: flex-start;
}

.main-content .main-post {
  display: flex;
  align-items: center;
  flex-direction: column;
  row-gap: 20px;
  margin: 20px 20px;
  background-color: rgba(36, 35, 35, 0.8);
  padding: 20px 20px;
  border-radius: 5px;
}

.main-content .main-post .posted-by {
  display: flex;
  column-gap: 20px;
}

.main-content .main-post .posted-by p {
  opacity: 50%;
}

.contents {
  display: flex;
  flex-direction: column;
}

.contents h1 {
  text-align: center;
  border-bottom: 1px solid white;
  padding: 20px 20px;
}

.contents .content {
  display: flex;
  padding: 20px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 50%);
}

.edit {
  display: flex;
  opacity: 50%;
  column-gap: 20px;
  margin-left: 80px;
}

.profile-pict,
.image-section {
  display: flex;
  height: 100%;
  flex-direction: column;
  align-items: center;
  row-gap: 20px;
  margin: 0px 20px;
}

.profile-pict span,
.profile-pict img {
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255, 255, 255, 50%);
  cursor: pointer;
  transition: 0.5s;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 100%);
  width: 50px;
  height: 50px;
  color: black;
  font-weight: 700;
  font-size: 25px;
}

.image-section span,
.image-section img {
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255, 255, 255, 50%);
  cursor: pointer;
  transition: 0.5s;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 100%);
  width: 120px;
  height: 120px;
  color: black;
  font-weight: 700;
  font-size: 25px;
  margin: 20px 0px;
}

.profile-pict span:hover,
.profile-pict img:hover {
  cursor: pointer;
  background-color: rgb(103, 97, 97);
  color: rgb(255, 255, 255);
}

.image-section span:hover,
.image-section img:hover {
  cursor: pointer;
  background-color: rgb(103, 97, 97);
  color: rgb(255, 255, 255);
}

.content .tweet {
  display: flex;
  flex-direction: column;
  row-gap: 20px;
}

.content .tweet .username-posted-on {
  display: flex;
  column-gap: 20px;
}

.content .tweet .username-posted-on p {
  opacity: 50%;
}

.content .tweet .tweet-text {
  display: flex;
  flex-direction: column;
  row-gap: 10px;
}

.content .likes,
.content .comments {
  display: flex;
  align-items: center;
  column-gap: 20px;
}

.comment-section {
  display: flex;
  justify-content: center;
  margin: 20px 0px;
}

.comment-section form {
  display: flex;
  align-items: center;
}
.comment-section form {
  display: flex;
  align-items: center;
  flex-direction: column;
  row-gap: 20px;
  position: relative;
}

.comment-section form i {
  position: absolute;
  top: 20px;
  right: 20px;
  font-size: 22px;
}

.comment-section form input {
  width: 900px;
  padding: 20px 20px;
  background-color: transparent;
  border: 1px solid rgba(255, 255, 255, 90%);
  outline: none;
  color: white;
  font-size: 18px;
  font-weight: 550;
}

.comment-section form input::placeholder {
  font-size: 18px;
  font-weight: 550;
  color: white;
}

.image-preview img {
  position: relative;
}

.remove-image button {
  opacity: 0%;
  transition: 0.5s;
}

.image-preview:hover .remove-image button {
  opacity: 100%;
  cursor: pointer;
  position: absolute;
  top: 50%;
  left: 50%;
  color: white;
}
</style>
