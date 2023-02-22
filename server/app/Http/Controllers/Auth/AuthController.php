<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ValidatorHelper;
use App\Http\Controllers\Controller;
use App\Models\Tweets;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = ValidatorHelper::LoginValidator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'token' => Crypt::encrypt(Auth::user()->id),
            ], 200);
        };

        return response()->json([
            'message' => 'Error'
        ], 500);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = ValidatorHelper::RegisterValidator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $user = User::create(array_merge($request->only('email', 'username'), ['password' => bcrypt($request->password)]));

        if ($user) {
            return response()->json([
                'message' => 'Berhasil register'
            ], 200);
        }

        return response()->json([
            'message' => 'Error'
        ], 500);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUsers()
    {
        $users = Tweets::with('tags', 'user')->latest()->get();

        return response()->json([
            'data' => $users,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUser($token)
    {
        $id = Crypt::decrypt($token);

        $user = User::findorfail($id);

        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTweets($token)
    {
        $id = Crypt::decrypt($token);

        $tweets = Tweets::with('tags', 'user')->where('user_id', $id)->latest()->get();

        return response()->json([
            'data' => $tweets,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $id = Crypt::decrypt($request->user_id);

        $validator = ValidatorHelper::UpdateProfileValidator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ]);
        }

        $finduser = User::findorfail($id);

        $finduser->username = $request->username;
        $finduser->email = $request->email;
        $finduser->profile = $request->profile;
        $finduser->bio = $request->bio;
        $finduser->save();

        return response()->json([
            'message' => 'Berhasil update profil'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
