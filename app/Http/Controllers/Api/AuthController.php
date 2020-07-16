<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    public function imageProcess() {
        $image = request()->avatar_image;
        $email = request()->email;
        if (!$image) {
            File::deleteDirectory(public_path('/images/'.$email));
            return null;
        }

        if (!Str::startsWith($image, 'data:image')) return $image;
        $imageName = null;

        File::deleteDirectory(public_path('/images/'.$email));

        $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        File::makeDirectory(public_path('/images/'.$email), 0777,true,true);
        Image::make($image)->fit(100, 100)->save(public_path('/images/'.$email.'/').$name);
        $imageName = 'images/'.$email.'/'.$name;

        return url($imageName);
    }

    public function register () {

        request()->validate([
            'name' => 'required',
            'avatar_id' => 'required|integer',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8'
        ]);

        $imageName = $this->imageProcess();

        $user = User::create([
            'name' => request()->name,
            'avatar_id' => request()->avatar_id,
            'avatar_image' => $imageName,
            'email' => request()->email,
            'password' => Hash::make(request()->password)
        ]);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message'=> "Registration failed"
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $role = Role::select('id')->where('name', 'member')->first();
        $user->roles()->attach($role);

        return response()->json([
            'success' => true,
            'message'=> "회원가입이 정상적으로 처리되었습니다.",
            'email' => $user->email
        ],Response::HTTP_OK);
    }

    public function login() {

        $validated = request()->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        if (!Auth::attempt($validated)){
            return response()->json([
                'success' => false,
                'message' => '비밀번호를 확인해 주세요.'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (request()->rememberMe)
            Passport::personalAccessTokensExpireIn(now()->addHours(24));

        $user = request()->user();
        $token = $user->createToken('Personal Access Token')->accessToken;
        $modUser =  new UserResource($user);

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $modUser
        ],Response::HTTP_OK);
    }

    public function logout() {
        request()->user()->token()->revoke();
        return response()->json([
            'success' => true,
            'message' => 'The user has been successfully logged out'
        ],Response::HTTP_OK);
    }

    public function getUsers() {
        if (Gate::denies('edit-users')){
            return response()->json([],Response::HTTP_UNAUTHORIZED);
        }

        $users = DB::table('users')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->select('id', 'avatar_id','avatar_image','name','email','role_id')
//            ->paginate(10);
            ->get();

        return response()->json([
            'users' => $users
        ],Response::HTTP_OK);
    }

    public function getUser() {
        $user = auth()->user();
        $modUser =  new UserResource($user);

        return response()->json([
            'user' => $modUser
        ],Response::HTTP_OK);
    }

    public function updateUser() {
        $user = auth()->user();
        $getUser = request()->all();

        $imageName = $this->imageProcess();

        $user['name'] = $getUser['name'];
        $user['avatar_id'] = $getUser['avatar_id'];
        $user['avatar_image'] = $imageName;

        $user->update();
        $modUser =  new UserResource($user);

        return response()->json([
            'message' => '개인프로필이 정상적으로 수정되었습니다.',
            'user' => $modUser
        ],Response::HTTP_OK);
    }

    public function changePassword() {
        $user = auth()->user();
        $getPass = request()->all();

        $currentPassword = $getPass['current'];
        if (!Hash::check($currentPassword, $user->getAuthPassword())) {
            return response()->json([
                'success' => false,
                'message' => '비밀번호 맞지 않습니다.'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $newPassword = $getPass['new'];
        $user['password'] = Hash::make($newPassword);
        $user->update();

        return response()->json([
            'success' => true,
            'message' => '비밀번호가 정상적으로 변경 되었습니다.',
        ],Response::HTTP_OK);
    }

    public function update() {
        if (Gate::denies('edit-users')){
            return response()->json([],Response::HTTP_UNAUTHORIZED);
        }
        $getUser = request()->all();
        $user = User::all()->find($getUser['id']);

        $user['name'] = $getUser['name'];
        $user['avatar_id'] = $getUser['avatar_id'];

        $imageName = $this->imageProcess();

        $user['avatar_image'] = $imageName;
        $user->roles()->sync($getUser['role_id']);

        $user->update();

        return response()->json([
            'message' => '회원의 정보가 정상적으로 업데이트 되었습니다.',
        ],Response::HTTP_OK);
    }

    public function destroy()
    {
        if (Gate::denies('delete-users')){
            return response()->json([],Response::HTTP_UNAUTHORIZED);
        }

        $user = User::all()->find(request()->id);

        $user->roles()->detach();

        if ($user['avatar_image'])
            File::deleteDirectory(public_path('/images/'.$user['email']));

        if ($user->delete()){
            return response()->json([
                'message' => '선택한 회원이 정상적으로 삭제 되었습니다.',
            ],Response::HTTP_OK);
        }else{
            return response()->json([
                'message' => '삭제에 실패하였습니다. 관리자에게 문의 하십시오.',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
