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
      <div class="image-section">
        <img
          :src="'data:image/png;base64,' + state.imageNotNull"
          :alt="state?.data?.username"
          v-if="state?.user?.profile != null"
        />

        <div class="no-profile" v-else>
          <span>
            {{ state.image }}
          </span>
        </div>
        <router-link
          to="/Edit-profile"
          style="font-weight: 550; font-size: 20px"
        >
          Edit profile
        </router-link>
      </div>

      <div class="personal-info">
        <h1>{{ state?.user?.username }}</h1>
        <p style="text-transform: lowercase">@{{ state?.user?.username }}</p>
        <div class="joined-on">
          <i class="fa-regular fa-calendar"></i>
          <p>{{ format_date(state?.user?.created_at) }}</p>
        </div>
        <textarea
          style="
            background-color: transparent;
            outline: none;
            border: none;
            color: white;
            font-size: 18px;
            font-weight: 550;
            pointer-events: none;
            resize: none;
            padding-top: 5px;
          "
          >{{ state?.user?.bio }}</textarea
        >
      </div>

      <div class="contents">
        <h1>Tweets</h1>
        <div class="content" v-for="data in state?.tweets">
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
            </div>
            <div class="tweet-text tweet-image">
              <p>{{ data?.tweet }}</p>
              <div v-for="datas in data?.tags">
                <p
                  v-for="tags in datas?.tag?.name.split(' ')"
                  style="color: #0d6efd; cursor: pointer"
                  v-if="data?.tags != 0"
                >
                  #{{ tags }}
                </p>
              </div>
              <img
                :src="'data:image/png;base64,' + data?.media"
                :alt="data?.tweet"
                width="450"
                height="250"
                v-if="data?.media != null"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tags-search">
      <div class="search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search" name="search" />
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
import axios from 'axios'
import { onMounted, reactive } from 'vue'
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
          state.image = data.username.charAt(0)
          state.user = data
          return getUser() && getUsers()
        }
        state.imageNotNull = data.profile
        state.user = data
        return getUser() && getUsers()
      } catch (e) {
        return (state.message = e.response.message)
      }
    })

    const state = reactive({
      message: null,
      image: null,
      tweets: null,
      user: null,
      imageNotNull: null,
      posts: null,
    })

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

    const getUser = async () => {
      const token = sessionStorage.getItem('token')
      try {
        const { data } = await API.get(`auth/getTweets/${token}`)
        console.log(data.data)

        return (state.tweets = data.data)
      } catch (e) {
        return (state.message = e.response.message)
      }
    }

    const Logout = () => {
      sessionStorage.removeItem('token')

      return (window.location.href = '/')
    }

    return { state, Logout }
  },

  methods: {
    format_date(value) {
      return moment(String(value)).format('DD MMM YYYY')
    },
  },
}
</script>

<style scoped>
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
  flex: 1;
  display: flex;
  flex-direction: column;
  height: 100vh;
  border-right: 1px solid rgba(255, 255, 255, 90%);
  height: 100vh;
  overflow-y: scroll;
  justify-content: flex-start;
}

.image-section {
  display: flex;
  flex-direction: column;
  row-gap: 20px;
  text-align: center;
  align-items: center;
}

.personal-info {
  display: flex;
  row-gap: 15px;
  flex-direction: column;
  margin: 40px 20px 0px 20px;
}

.personal-info h1 {
  font-size: 28px;
  font-weight: 550;
}

.personal-info p,
.personal-info i {
  opacity: 50%;
}

.joined-on {
  display: flex;
  column-gap: 10px;
  align-items: center;
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
  font-size: 50px;
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
  padding: 20px 10px 0px 10px;
  height: 80%;
  border-radius: 5px;
  row-gap: 12px;
  overflow-y: scroll;
}

.tag-category {
  margin-bottom: 5px;
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
</style>
