<?php

namespace App\Http\Controllers;

use App\Playlist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = DB::table('playlists')
            ->orderByDesc('on_air')
            ->paginate(5);

        return response()->json([
            'playlists' => $playlists,
        ],Response::HTTP_OK);
    }

    public function store()
    {
        if (Gate::denies('edit-content')){
            return response()->json([],Response::HTTP_UNAUTHORIZED);
        }

        request()->validate([
            'episode' => 'required | numeric',
            'song_list' => 'required',
            'on_air' => 'required | date'
        ]);

        $playlist = Playlist::create(request()->all());

        if (!$playlist) {
            return response()->json([
                'success' => false,
                'message' => "저장에 실패 하였습니다. 관리자에게 문의 하십시오.",
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message'=> "정상적으로 선곡표를 생성 하였습니다.",
            'playlist' => $playlist
        ],Response::HTTP_OK);
    }

    public function update()
    {
        if (Gate::denies('edit-content')){
            return response()->json([],Response::HTTP_UNAUTHORIZED);
        }

        request()->validate([
            'episode' => 'required | numeric',
            'song_list' => 'required',
            'on_air' => 'required | date'
        ]);

        $playlist = Playlist::all()->find(request()->id);

        if ($playlist->update(request()->all())){
            return response()->json([
                'success' => true,
                'message'=> "정상적으로 선곡표의 정보가 수정 되었습니다."
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
        if (Gate::denies('edit-content')){
            return response()->json([],Response::HTTP_UNAUTHORIZED);
        }

        $playlist = Playlist::all()->find(request()->id);

        if ($playlist->delete()) {
            return response()->json([
                'message' => '선곡표가 정상적으로 삭제 되었습니다.',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => '삭제에 실패 하였습니다. 관리자에게 문의 하십시오.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
