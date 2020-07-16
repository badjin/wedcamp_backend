<?php

namespace App\Http\Controllers;

use App\TagItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TagItemController extends Controller
{
    public function index()
    {
        $tags = TagItem::all();

        return response()->json([
            'tags' => $tags
        ],Response::HTTP_OK);
    }

    public function store () {

        $tags = TagItem::all();

        if ($tags->count() > 0) {

            $affected = DB::table('tag_items')
                ->update(['tags' => request()->all()]);
            if ($affected){
                return response()->json([
                    'success' => true,
                    'message'=> "정상적으로 분류항목을 업데이트 하였습니다."
                ],Response::HTTP_OK);
            }else{
                return response()->json([
                    'success' => false,
                    'message'=> "업데이트에 실패 하였습니다. 관리자에게 문의 하십시오."
                ],Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $tags = TagItem::create([
            'tags' => request()->all()
        ]);

        if (!$tags) {
            return response()->json([
                'success' => false,
                'message' => "저장에 실패 하였습니다. 관리자에게 문의 하십시오.",
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message'=> "정상적으로 분류항목이 저장 되었습니다.",
            'tags' => $tags
        ],Response::HTTP_OK);
    }
}
