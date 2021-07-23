<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function getReportsById(Request $request) {
    	$firmId = $request->id;

    	$reports = Report::where('firm_id',$firmId)
        ->orderByDesc('id')
        ->get();

        if($reports) {
        	return response()->json(['reports' => $reports], 200);
        } else {
        	return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Нещо се обърка при зареждането на нередностите!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		] 
	            ], 422);
        }
    }

    public function setReport(ReportRequest $request) {

    	$report = new Report;

    	if($request->file()) {
    		  $file_name = time().'_'.$request->file->getClientOriginalName();
	          $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');

	          $report->name = time().'_'.$request->file->getClientOriginalName();
	          $report->path = '/storage/' . $file_path;
    	}

		$report->reason = $request->reason;
		$report->body = $request->body;
		$report->active = 0;
		$report->user_id = $request->user_id;
		$report->firm_id = $request->firm_id;
		$report->save();

    	if($report) {
    		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Успешно добавихте докладването си!' ,
	        			'type' => 1, 
	        			'active' => 1
	        		],
				'report' => $report 
	            ], 200);
    	} else {
    		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Нещо се обърка при добавянето на докладването!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		] 
	            ], 422);
    	}
    }

    public function approve(Request $request) {
    	$reportId = $request->id;

    	$report = Report::find($reportId);
    	$updated = $report->update(['active' => true]);
    	
    	if($updated) {
    		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Успешно одобрихте докладването!' ,
	        			'type' => 1, 
	        			'active' => 1
	        		],
				'report' => $report 
	            ], 200);
    	} 

		return response()->json(
        	['notification' => 
        		[
        			'msg' => 'Нещо се обърка при одобряването на докладването!' ,
        			'type' => 0, 
        			'active' => 1
        		] 
            ], 422);
    }

    public function update(ReportRequest $request) {

    	$reportId = $request->id;
    	$report = Report::find($reportId);

    	$filePath = null;
    	$name = null;

    	//check if we need to delete the image or we have presented new image
    	if((int)$request->toDeleteImage == 1 || $request->file()) {
    		if($report->path) {
	    		Storage::delete('/public/uploads/'.$report->name);
			}
    	}

    	//if we have new presented file we need to insert it
    	if($request->file()) {
    		  $file_name = time().'_'.$request->file->getClientOriginalName();
	          $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');

	          $name = time().'_'.$request->file->getClientOriginalName();
	          $filePath = '/storage/' . $file_path;
    	}

    	$reason = $request->reason;
		$body = $request->body;

    	if($report->update(
    		['body' => $body,'reason' => $reason, 'name' => $name, 'path' => $filePath]
    	)) {
    		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Успешно обновихте докладването си!' ,
	        			'type' => 1, 
	        			'active' => 1
	        		],
				'report' => $report 
	            ], 200);
    	} else {
    		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Нещо се обърка при обновяването на докладването!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		] 
	            ], 422);
    	}

    }

    public function destroy(Request $request) 
    {

    	$reportId = $request->id;

		$report = Report::find($reportId);

		if($report->path) {
    		Storage::delete('/public/uploads/'.$report->name);
		}

		if($report->delete()) {
			return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Успешно изтрихте докладването си!' ,
	        			'type' => 1, 
	        			'active' => 1
	        		] 
	            ], 200);
		}  

		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Докладването ви не беше изтрито!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		] 
	            ], 422);
	    
    }
}
