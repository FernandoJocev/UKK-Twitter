<template>
  <div class="container">
    <div class="top-section">
      <img src="../icons/Twitter.png" alt="Twitter" width="156" height="134" />
      <h1>Welcome</h1>
    </div>

    <div class="form-section">
      <form>
        <input
          type="email"
          placeholder="Email"
          name="email"
          v-model="FormData.email"
        />
        <div class="password">
          <i
            class="fa-regular fa-eye-slash icon-1"
            @click="iconChanger"
            v-if="state.inputPassword == true"
          ></i>
          <i class="fa-regular fa-eye icon-1" v-else @click="iconChanger"></i>
          <input
            :type="state.inputPassword == true ? 'password' : 'text'"
            placeholder="Password"
            name="password"
            v-model="FormData.password"
          />
        </div>
        <div class="confirm">
          <i
            class="fa-regular fa-eye-slash icon-2"
            @click="iconChanger2"
            v-if="state.inputPassword2 == true"
          ></i>
          <i class="fa-regular fa-eye icon-2" @click="iconChanger2" v-else></i>
          <input
            :type="state.inputPassword2 == true ? 'password' : 'text'"
            placeholder="Confirm"
            name="confirm"
            v-model="FormData.confirm"
          />
        </div>
        <input
          type="text"
          placeholder="Username"
          name="username"
          v-model="FormData.username"
        />
        <button type="submit" @click="Register">Sign up</button>
      </form>

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
        v-if="state?.message != null"
      >
        <div class="title">
          <h1 style="font-weight: 700; font-size: 28px">
            {{ state?.message }}
          </h1>
        </div>
        <div class="message">{{ state?.message }}</div>
      </div>
    </div>

    <div class="sign-up-section">
      <p>Don't have an account? <router-link to="/">Sign in</router-link></p>
    </div>
  </div>
</template>

<script>
import { reactive, onMounted } from 'vue'
import axios from 'axios'

const API = axios.create({
  baseURL: 'http://127.0.0.1:8000/api/auth/',
})

export default {
  setup() {
    onMounted(() => {
      const token = sessionStorage.getItem('token')

      if (token != null) {
        return (window.location.href = '/Home')
      }
    })

    const state = reactive({
      token: null,
      message: null,
      inputPassword: true,
      inputPassword2: true,
    })

    const FormData = reactive({
      email: '',
      password: '',
      confirm: '',
      username: '',
    })

    const iconChanger = () => {
      if (state.inputPassword == true) {
        return (state.inputPassword = false)
      }

      return (state.inputPassword = true)
    }

    const iconChanger2 = () => {
      if (state.inputPassword2 == true) {
        return (state.inputPassword2 = false)
      }

      return (state.inputPassword2 = true)
    }

    const Register = async (e) => {
      e.preventDefault()

      try {
        await API.post('register', FormData)

        return (window.location.href = '/')
      } catch (error) {
        setTimeout(() => {
          state.message = null
        }, 3000)

        return (state.message = error.response.data.message)
      }

      // if (data.response.data.message != null) {
      //   state.message = data?.response?.data?.message
      // }
    }

    return { FormData, Register, state, iconChanger, iconChanger2 }
  },
}
</script>

<style scoped>
a {
  text-decoration: none;
  color: #3f4fdb;
  margin-top: 5px;
}

input {
  width: 750px;
  height: 54px;
  border-radius: 4px;
  background-color: transparent;
  outline: none;
  border: 1px solid white;
  margin-top: 10px;
  padding: 0px 15px;
  font-weight: 500;
  color: white;
}

i {
  position: absolute;
  top: 28px;
  right: 20px;
  cursor: pointer;
}

input::placeholder {
  font-weight: 550;
  font-size: 15px;
  color: white;
}

.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
}

.top-section {
  margin-bottom: 20px;
}

.form-section form {
  display: flex;
  flex-direction: column;
}

.password,
.confirm {
  position: relative;
}

.form-section button {
  background-color: #2aa9e0;
  border-radius: 4px;
  height: 54px;
  color: white;
  font-size: 22px;
  font-weight: 550;
  margin-top: 30px;
  cursor: pointer;
}

.sign-up-section {
  margin-top: 20px;
}
</style>
