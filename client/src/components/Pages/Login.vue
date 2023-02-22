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
            class="fa-regular fa-eye-slash"
            v-if="state.inputPassword == true"
            @click="iconChanger"
          ></i>
          <i class="fa-regular fa-eye" v-else @click="iconChanger"></i>
          <input
            :type="state.inputPassword == true ? 'password' : 'text'"
            placeholder="Password"
            name="password"
            v-model="FormData.password"
          />
        </div>
        <router-link to="/Forget-password">Forgot password?</router-link>
        <button type="submit" @click="Login">Log in</button>
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
      <p>
        Don't have an account? <router-link to="/Register">Sign up</router-link>
      </p>
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

    const state = reactive({ token: null, message: null, inputPassword: true })

    const FormData = reactive({
      email: '',
      password: '',
    })

    const Login = async (e) => {
      e.preventDefault()

      const { data } = await API.post('login', FormData).catch(function (
        error
      ) {
        if (error.response) {
          setTimeout(() => {
            state.message = null
          }, 3000)

          return (state.message = error.response.data.message)
        }
      })

      // if (data.response.data.message != null) {
      //   state.message = data?.response?.data?.message
      // }

      sessionStorage.setItem('token', data.token)
      return (window.location.href = '/Home')
    }

    const iconChanger = () => {
      if (state.inputPassword == true) {
        return (state.inputPassword = false)
      }
      return (state.inputPassword = true)
    }

    return { FormData, Login, state, iconChanger }
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

input::placeholder {
  font-weight: 550;
  font-size: 15px;
  color: white;
}

i {
  position: absolute;
  right: 20px;
  top: 29px;
  cursor: pointer;
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

.form-section .password {
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
  margin-top: 80px;
}
</style>
