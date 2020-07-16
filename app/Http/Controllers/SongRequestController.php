<?php

namespace App\Http\Controllers;

use App\Http\Resources\SongRequestResource;
use App\SongRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SongRequestController extends Controller
{

    public function index()
    {
        $search = request()->all();
        if ($search['table'] == 'id') {
            $songRequests = DB::table('song_requests')
                ->join('users', 'song_requests.user_id', '=', 'users.id')
                ->select('song_requests.*', 'users.avatar_id', 'users.avatar_image', 'users.name')
                ->latest()->paginate(10);
        } else {
            $songRequests = DB::table('song_requests')
                ->join('users', 'song_requests.user_id', '=', 'users.id')
                ->select('song_requests.*', 'users.avatar_id', 'users.avatar_image', 'users.name')
                ->where($search['table'], 'LIKE', '%'.$search['keyword'].'%')
                ->latest()->paginate(10);
        }

        return response()->json([
            'song_reqs' => $songRequests,
        ],Response::HTTP_OK);
    }

    public function store()
    {
        request()->validate([
            'title' => 'required | min:3',
            'description' => 'required'
        ]);

        $user = auth()->user();

        $value = request(['title', 'real_name', 'mobile', 'is_mfgc', 'description']);
        $value['user_id'] = $user['id'];

        $songRequest = SongRequest::create($value);
        $songRequest['name'] = $user['name'];
        $songRequest['avatar_id'] = $user['avatar_id'];
        $songRequest['avatar_image'] = $user['avatar_image'];

        if (!$songRequest) {
            return response()->json([
                'success' => false,
                'message' => "저장에 실패 하였습니다. 관리자에게 문의 하십시오.",
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message'=> "정상적으로 게시글이 추가 되었습니다.",
            'song_req' => $songRequest
        ],Response::HTTP_OK);
    }

    public function update()
    {
        $user = auth()->user();
        if ($user['id'] != request()->user_id) {
            if (Gate::denies('edit-content')){
                return response()->json([],Response::HTTP_UNAUTHORIZED);
            }
        }

        request()->validate([
            'title' => 'required',
            'description' => 'required'
        ]);

        $songRequest = SongRequest::all()->find(request()->id);

        if ($songRequest->update(request()->all())){
            return response()->json([
                'success' => true,
                'message'=> "정상적으로 게시글의 정보가 수정 되었습니다."
            ],Response::HTTP_OK);
        }else{
            return response()->json([
                'success' => false,
                'message'=> "업데이트에 실패 하였습니다. 관리자에게 문의 하십시오."
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy()
    {
        $user = auth()->user();

        if ($user->id != request()->user_id) {
            if (Gate::denies('edit-content')){
                return response()->json([],Response::HTTP_UNAUTHORIZED);
            }
        }

        $songRequest = SongRequest::all()->find(request()->id);

        if ($songRequest->delete()) {
            return response()->json([
                'message' => '게시글이 정상적으로 삭제 되었습니다.',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => '삭제에 실패 하였습니다. 관리자에게 문의 하십시오.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
