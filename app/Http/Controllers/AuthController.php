<?php

namespace App\Http\Controllers;

use App\models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{   
    public function register(Request $request)
    {   
        //validasi menggunakan function bawaan laravel Validator
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'username' => 'required|min:8|unique:users',
            'role_id' => 'required|integer',
            'password' => 'required|min:6|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/' //validasi mengandung huruf dan angka
        ], 

        //custom validation
        [
            'name.required' => 'Nama Harus Diisi!',
            'name.string' => 'Nama Hanya boleh Mengandung Huruf!',

            'email.required' => 'Email Harus Diisi!',
            'email.email' => 'Harus Bertipe Email!',
            'email.unique' => 'Email Sudah Terdaftar, Gunakan Email Lain!',

            'username.required' => 'Username Harus Diisi!',
            'username.unique' => 'Username Sudah Tersedia, Silahkan Buat yang Lain!',
            'username.min' => 'Username Minimal 8 Karakter',
            
            'role_id.required' => 'Role Harus Diisi!',
            
            'password.required' => 'Password Harus Diisi!',
            'password.min' => 'Username Minimal 6 Karakter',
            'password.regex' => 'Password Harus Mengandung Kombinasi Angka dan Huruf',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()],400);
        }
        // check if the role exists in the roles table
        $role = Role::find($request->role_id);
        if (!$role) {
            return response()->json(['error' => 'The role does not exist'], 400);
        }

        try{
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->role_id = $request->role_id; 
            $user->status = 'Pending';
            $user->password = Hash::make($request->password);
            $user->save();
        }catch (Exception $e) {
            return response()->json(['message' => 'Gagal membuat user baru.'], 500);
        }

        return response()->json(['message' => 'Akun berhasil dibuat, silahkan login!'],201);
    }

    public function login(Request $request)
    {   
        //untuk proses validasi menggunakan fungsi validator bawaan laravel 
        $validator = Validator::make($request->all(), [
            'username_or_email' => 'required', //validasi untuk pengecekan inputan kosong 
            'password' => 'required' //validasi untuk pengecekan inputan kosong 
        ]);

        //jika data inputan kosong, akan mengembalikan pesan error bawaan validator
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()],400);
        }

        //pengecekan apakah user menginputkan email atau username untuk proses loginnya, di cek ke database
        $user = User::where('email', $request->username_or_email)
            ->orWhere('username', $request->username_or_email)
            ->first();
        
        //pengecekan username dan password apakah benar tersedia di database? jika tidak response json login gagal
        if(!$user||!hash::check($request->password, $user->password)){
            return response()->json(['message' => 'Login Gagal. Mohon pastikan informasi yang Anda masukkan benar!'], 401);
        }
        
        try {
            $token = JWTAuth::fromUser($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }



        return $this->respondWithToken($token,$user);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout Berhasil!']);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token,$user)
    {
        return response()->json([

            'user_name' => $user->name,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'message' => 'Login Berhasil'
        ],200);
    }

    protected function usersss($user)
    {
        return response()->json([
            'user_email' => $user->emai,
        ],200);
    }
}
