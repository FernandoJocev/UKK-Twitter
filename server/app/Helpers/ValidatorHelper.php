<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;

class ValidatorHelper
{
 public static function LoginValidator($data)
 {
  $rules = [
   'email' => ['required', 'exists:users,email', 'email'],
   'password' => ['required', 'min:8'],
  ];

  $messages = [
   'email.required' => 'Harap masukkan email anda',
   'email.exists' => 'Email tidak ditemukan',
   'email.email' => 'Harap masukkan email yang valid',
   'password.required' => 'Harap masukkan password',
   'password.min' => 'Password minimal 8',
  ];

  return Validator::make($data, $rules, $messages);
 }

 public static function RegisterValidator($data)
 {
  $rules = [
   'email' => ['required', 'unique:users,email', 'email'],
   'password' => ['required', 'min:8'],
   'confirm' => ['required', 'min:8', 'same:password'],
   'username' => ['required', 'min:5']
  ];

  $messages = [
   'email.required' => 'Harap masukkan email anda',
   'email.unique' => 'Email sudah digunakan',
   'email.email' => 'Harap masukkan email yang valid',
   'password.required' => 'Harap masukkan password',
   'password.min' => 'Password minimal 8',
   'confirm.required' => 'Harap konfirmasi password',
   'confirm.min' => 'Konfirmasi password minimal 8',
   'confirm.same' => 'Password dan konfirmasi tidak sama',
   'username.required' => 'Harap masukkan username anda',
   'username.min' => 'Username minimal 5'
  ];

  return Validator::make($data, $rules, $messages);
 }

 public static function PostValidator($data)
 {
  $rules = [
   'tweet' => ['required', 'max:250', 'string'],
  ];

  $messages = [
   'tweet.required' => 'Tweet kosong',
   'tweet.max' => 'Tweet tidak bisa lebih dari 250 kalimat',
   'tweet.string' => 'Harap masukkan tweet yang valid',
  ];

  return Validator::make($data, $rules, $messages);
 }

 public static function UpdateProfileValidator($data)
 {
  $rules = [
   'user_id' => ['required'],
   'username' => ['required', 'min:5'],
   'email' => ['required', 'email'],
  ];

  $messages = [
   'user_id.required' => 'ID user harus ada',
   'username.required' => 'Username wajib di isi',
   'username.min' => 'Username minimal 5',
   'email.required' => 'Email wajib di isi',
   'email.email' => 'Email tidak valid',
   'email.unique' => 'Email sudah ada',
  ];

  return Validator::make($data, $rules, $messages);
 }
}
