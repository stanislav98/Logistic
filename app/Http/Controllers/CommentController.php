<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function getCommentsById(Request $request) {
    	$firmId = $request->id;

    	$comments = Comment::where('firm_id',$firmId)
        ->orderByDesc('id')
        ->get();

        if($comments) {
        	return response()->json(['comments' => $comments], 200);
        } else {
        	return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Нещо се обърка при зареждането на коментарите!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		] 
	            ], 422);
        }
    }

    public function setComment(CommentRequest $request) {
    	$comment = Comment::create($request->all());

    	if($comment) {
    		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Успешно добавихте коментара си!' ,
	        			'type' => 1, 
	        			'active' => 1
	        		],
				'comment' => $comment 
	            ], 200);
    	} else {
    		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Нещо се обърка при добавянето на коментара!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		] 
	            ], 422);
    	}
    }

    public function update(CommentRequest $request) {

    	$commentId = $request->id;

    	$comment = Comment::where('id',$commentId)->first();

    	if($comment->update(['body' => $request->body])) {
		    return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Успешно обновихте коментара си!' ,
	        			'type' => 1, 
	        			'active' => 1
	        		],
	        	'comment' => $comment
	            ], 200);
    	} else {
    		 return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Неуспешно обновяване на коментара!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		] 
	            ], 422);
    	}

    }

    public function destroy(Request $request) {

    	$commentId = $request->id;

		$comment = Comment::find($commentId);

		if($comment->delete()) {
			return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Успешно изтрихте коментара си!' ,
	        			'type' => 1, 
	        			'active' => 1
	        		] 
	            ], 200);
		}  

		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Коментара ви не беше изтрит!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		] 
	            ], 422);

	    
    }
}
