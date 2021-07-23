<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function getSingleRating(Request $request){
    	$companyId = $request->id;
    	
    	$rating = Rating::where('firm_id',$request->id)->get();

    	if(is_null($rating) || empty($rating)) {
    		return response()->json(['rating' => 0],200);
    	}

    	return response()->json(['rating' => $rating],200);
    	
    }

    /**
     * Types in return 1 -> update | 2 -> insert | 3 -> fail
     */
     public function setRating(Request $request){
    	
    	$rating = Rating::where('user_id',$request->user_id)->where('firm_id',$request->firm_id)->first();

    	if($rating) {
    		$rating->update(['rate' => $request->rate]);

    		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Успешно обновихте гласа си!' ,
	        			'type' => 1, 
	        			'active' => 1
	        		],
        		'type' => 1,
	            ], 200);
    	} else {
	    	$rating = Rating::create($request->all());

	    	if($rating) {
				return response()->json(
		        	['notification' => 
		        		[
		        			'msg' => 'Успешно гласувахте!' ,
		        			'type' => 1, 
		        			'active' => 1
		        		] ,
		        	'rating' => $rating,
        			'type' => 2 
		            ], 200);
	    	} 
	    	
	    	return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Неуспешно гласувахте!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		],
    		'type' => 3
	            ], 422);
    	}

	}
}
