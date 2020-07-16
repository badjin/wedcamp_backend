<?php

namespace App\Http\Controllers;

use App\Vod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class VodController extends Controller
{
    public function index()
    {
        $videos = Vod::latest()->paginate(24);

        return response()->json([
            'videos' => $videos
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        if (Gate::denies('edit-vod')){
            return response()->json([],Response::HTTP_UNAUTHORIZED);
        }

        request()->validate([
            'title' => 'required | min:3',
            'video_url' => 'required'
        ]);

        $value = request(['tags', 'title', 'video_id', 'video_url']);

        $vod = Vod::create($value);

        if (!$vod) {
            return response()->json([
                'success' => false,
                'message' => "저장에 실패 하였습니다. 관리자에게 문의 하십시오.",
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message'=> "정상적으로 영상이 저장 되었습니다.",
            'vod' => $vod
        ],Response::HTTP_OK);
    }

    public function update()
    {
        if (Gate::denies('edit-vod')){
            return response()->json([],Response::HTTP_UNAUTHORIZED);
        }

        request()->validate([
            'title' => 'required',
            'video_url' => 'required',
            'video_id' => 'required',
        ]);

        if (request()->id == null)
            $vod = Vod::all()->where('video_id', request()->video_id)->first();
        else
            $vod = Vod::all()->find(request()->id);

        if ($vod->update(request()->all())){
            return response()->json([
                'success' => true,
                'message'=> "정상적으로 영상의 정보가 수정 되었습니다."
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
        if (Gate::denies('edit-vod')){
            return response()->json([],Response::HTTP_UNAUTHORIZED);
        }

        $vod = Vod::all()->find(request()->id);

        if ($vod->delete()) {
            return response()->json([
                'message' => '영상이 정상적으로 삭제 되었습니다.',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => '삭제에 실패 하였습니다. 관리자에게 문의 하십시오.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
