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
      <div class="title">
        <h1>Home</h1>
      </div>

      <div class="form-section">
        <form @submit="Tweet">
          <div class="preview-tweets">
            <input
              type="text"
              placeholder="What's happening?"
              v-model="FormData.tweet"
            />
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
          </div>
          <div class="image-upload tweet">
            <label for="file-upload">
              <i class="fa-regular fa-image"> </i>
            </label>
            <input
              type="file"
              id="file-upload"
              ref="file"
              name="media"
              @change="FileHandler"
            />
            <button>Tweet</button>
          </div>
        </form>

        <div
          class="alert"
          style="
            display: flex;
            flex-direction: column;
            background-color: #ff0f0f;
            border-radius: 5px;
            margin: 20px 20px;
            padding: 10px 10px;
          "
          v-if="state?.message"
        >
          <div class="title-error">
            <h1 style="font-weight: 700; font-size: 28px">
              {{ state?.message }}
            </h1>
          </div>
          <div class="message">{{ state?.message }}</div>
        </div>
      </div>

      <hr />

      <div class="content-container">
        <div class="contents" v-for="data in state?.posts">
          <div class="profile-pict">
            <img
              :src="'data:image/png;base64,' + data?.user?.profile"
              :alt="state?.user?.username"
              style="width: 50px; height: 50px; border-radius: 50%"
              v-if="data?.user?.profile != null"
            />

            <div class="no-profile" v-else>
              <span>
                {{ data?.user?.username.charAt(0) }}
              </span>
            </div>
          </div>
          <div class="content">
            <div class="top-content">
              <div class="username-posted-on">
                <h4>{{ data?.user?.username }}</h4>
                <p style="text-transform: lowercase">
                  @{{ data?.user?.username }}
                </p>
                <p>{{ format_date(data?.created_at) }}</p>
              </div>
              <div class="edit" v-if="state?.user?.email === data?.user?.email">
                <router-link :to="'/Edit-tweet/' + data?.id">
                  <i class="fa-regular fa-pen-to-square"></i>
                </router-link>
                <label for="deletePost">
                  <i class="fa-regular fa-square-minus"></i>
                </label>
                <button
                  @click="deletePost(data?.id)"
                  style="display: none"
                  id="deletePost"
                ></button>
              </div>
            </div>
            <div class="tweet-text tweet-image">
              <div
                class="tweet-caption"
                style="display: flex; column-gap: 5px; flex-direction: column"
              >
                <p>{{ data?.tweet }}</p>
                <div v-for="datas in data?.tags">
                  <p
                    style="color: #0d6efd; cursor: pointer"
                    v-if="state?.tags != 0"
                    v-for="tags in datas?.tag?.name.split(' ')"
                  >
                    #{{ tags }}
                  </p>
                </div>
              </div>
              <img
                :src="'data:image/png;base64,' + data?.media"
                alt="image"
                width="450"
                height="250"
                v-if="data?.media != null"
              />
            </div>
            <div class="comments likes">
              <i class="fa-regular fa-heart"></i>
              <router-link :to="'/Comments/' + data?.id">
                <i class="fa-regular fa-comment"></i>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tags-search">
      <div class="search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input
          type="text"
          placeholder="Search"
          name="search"
          v-model="FormData.query"
          @keyup="Search"
        />
      </div>

      <div class="tags">
        <h1>Tags</h1>
        <div class="tag" v-for="data in state?.posts">
          <div class="tag-category" v-for="datas in data?.tags">
            <h1 v-for="tags in datas?.tag?.name.split(' ')">#{{ tags }}</h1>
          </div>
        </div>
      </div>
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
        // console.log(data)
        if (data.profile == null) {
          state.image = data.username.charAt(0)
          state.user = data
          return getUsers()
        }
        state.imageNotNull = data.profile
        state.user = data
        return getUsers()
      } catch (e) {
        return (state.message = e.response)
      }
    })

    const FormData = reactive({
      tweet: null,
      user_id: null,
      query: null,
    })

    const state = reactive({
      message: null,
      user: null,
      posts: null,
      image: null,
      imageNotNull: null,
    })

    const file = ref(null)

    const getUsers = async () => {
      try {
        const { data } = await API.get('auth/getUsers')
        // console.log(data.data)
        // for (let i = 0; i < data.data.length; i++) {
        //   let tags = data.data[i].tags[0].tag.name.split(' ')
        //   console.log(tags[0])
        //   state.tags = tags
        // }
        state.posts = data.data
        if (state.image == null) {
          state.image = data.data.username.charAt(0)
        }
        return
      } catch (e) {
        state.message = e.response
      }
    }

    const Tweet = async (e) => {
      e.preventDefault()

      const id = sessionStorage.getItem('token')
      FormData.user_id = id
      try {
        const { data } = await API.post('main/post', FormData)

        setTimeout(() => {
          state.message = null
        }, 3000)
        getUsers()
        return (state.message = data.message)
      } catch (e) {
        setTimeout(() => {
          state.message = null
        }, 3000)
        return (state.message = e.response.data.message)
      }
    }

    const Search = async (e) => {
      if (e.key === 'Enter') {
        try {
          window.location.href = `/Search?query=${FormData.query}`
        } catch (err) {
          state.message = err.response
        }
      }
    }

    const deletePost = async (id) => {
      try {
        await API.post(`main/deletePost/${id}`)

        return getUsers()
      } catch (e) {
        return (state.message = e.response.message)
      }
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

    const Logout = () => {
      sessionStorage.clear('token')

      window.location.href = '/'
    }

    const RemoveImage = () => {
      return (FormData.media = null)
    }

    setTimeout(() => (state.message = ''), 3000)
    return {
      getUsers,
      Tweet,
      Logout,
      FormData,
      FileHandler,
      state,
      file,
      RemoveImage,
      deletePost,
      Search,
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
.profile-logout img {
  cursor: pointer;
  background-color: rgb(45, 44, 44);
  color: white;
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  row-gap: 30px;
  height: 100vh;
  border-right: 1px solid rgba(255, 255, 255, 90%);
  height: 100vh;
  overflow-y: scroll;
}

.title {
  width: 100%;
  display: flex;
  justify-content: center;
  padding: 20px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 90%);
}

.form-section {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  row-gap: 20px;
}

.form-section .preview-tweets {
  border-bottom: 1px solid rgba(255, 255, 255, 90%);
  padding: 0px 20px 55px 20px;
  margin-bottom: 20px;
}

.form-section .preview-tweets input {
  background-color: transparent;
  width: 100%;
  border: none;
  color: white;
  font-weight: 500;
  font-size: 20px;
}

.form-section .preview-tweets input::placeholder {
  font-size: 20px;
  font-weight: 550;
}

.form-section .preview-tweets .image-preview {
  /* display: none; */
  position: relative;
}

.form-section .preview-tweets .image-preview img {
  margin-top: 20px;
}

.remove-image {
  top: 50%;
  left: 200px;
  position: absolute;
  opacity: 0%;
  transition: 0.5s;
  background-color: transparent;
}

.remove-image button {
  position: absolute;
  margin: 0px;
  padding: 0px;
  border: none;
  background-color: transparent;
  color: white;
}

.form-section .preview-tweets .image-preview:hover .remove-image {
  opacity: 100%;
  position: absolute;
  color: white;
  top: 50%;
  left: 200px;
}

.image-upload {
  display: flex;
  justify-content: space-between;
  padding: 0px 20px;
}

.image-upload input[type='file'] {
  display: none;
}

.tweet button {
  cursor: pointer;
  padding: 5px 20px;
  font-weight: 550;
  font-size: 18px;
  border: none;
  border-radius: 50px;
  background-color: #59c2ef;
  color: white;
}

.content-container {
  display: flex;
  flex-direction: column;
}

.contents {
  display: flex;
  border-bottom: 1px solid rgba(255, 255, 255, 90%);
  width: 100%;
}

.profile-pict {
  display: flex;
  height: 100%;
  flex-direction: column;
  align-items: center;
  row-gap: 20px;
  margin: 20px 20px;
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

.profile-pict span:hover,
.profile-pict img:hover {
  cursor: pointer;
  background-color: rgb(103, 97, 97);
  color: rgb(255, 255, 255);
}

.content {
  display: flex;
  justify-content: space-between;
  flex-direction: column;
  margin: 20px 0px;
  row-gap: 10px;
}

.top-content {
  display: flex;
  justify-content: space-between;
  column-gap: 70px;
  align-items: center;
}

.top-content .edit {
  display: flex;
  opacity: 50%;
  column-gap: 20px;
}

.username-posted-on {
  display: flex;
  column-gap: 20px;
}

.username-posted-on p {
  opacity: 50%;
}

.username-posted-on h4 {
  font-size: 15px;
  margin-right: 20px;
}

.tweet-text {
  font-size: 15px;
  row-gap: 10px;
  display: flex;
  flex-direction: column;
  margin-top: 5px;
  margin-bottom: 25px;
}

.comments,
.likes {
  display: flex;
  column-gap: 20px;
  opacity: 50%;
}

.tags-search {
  display: flex;
  flex-direction: column;
  margin: 20px 20px 0px 20px;
}

.search {
  position: relative;
  margin-bottom: 30px;
}

.search input {
  background-color: #424242;
  color: white;
  border: none;
  border-radius: 20px;
  width: 350px;
  height: 20px;
  padding: 20px 20px 20px 35px;
}

.search i {
  position: absolute;
  font-size: 18px;
  top: 10px;
  left: 7px;
  opacity: 40%;
  margin-right: 20px;
}

.tags {
  display: flex;
  flex-direction: column;
  background-color: #2e2e2e;
  padding: 20px 10px 70px 10px;
  height: 80%;
  border-radius: 5px;
  row-gap: 12px;
  overflow-y: scroll;
}

.tag-category {
  background-color: rgba(0, 0, 0, 50%);
  padding: 10px 10px;
  border-radius: 5px;
  cursor: pointer;
}

.tag h1 {
  font-size: 20px;
}

.tag p {
  font-size: 15px;
  font-weight: 500;
  opacity: 50%;
}

input::placeholder {
  font-weight: 500;
  font-size: 15px;
}

/* @media (max-width: 376px) {
  .search input {
    width: 100%;
  }
} */
</style>
