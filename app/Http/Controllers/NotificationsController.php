<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class NotificationsController extends Controller
{
	/**
	 * Get unread notifications and show them on UI
	 */
    public function getNotifications(Request $request) 
    {
    	$userId = $request->id;

    	$unreadNotifications = Message::where([['reciever_id','=',$userId]])
            ->join('users as usersA', 'messages.sender_id', '=', 'usersA.id')
            ->select('usersA.name','messages.created_at')
            ->where('status','=','0')
            ->groupBy('sender_id')
            ->get()
            ->toArray();
            
		return response()->json(['unreadNotifications' => $unreadNotifications], 200);
    }

    /**
     * Function on open of notification window update
     * the status to 1 -> message is read
     */
    public function updateNotifications(Request $request)
    {
    	$userId = $request->id;
        $status = $request->status;
        $messageId = $request->message_id;

    	$updateNotifications = Message::where('status','!=',$status)
    		->where('reciever_id',$userId)
            ->when($messageId, function ($query, $messageId) {
                    return $query->where('id', $messageId);
                })
            ->update(['status' => $status]);


		return response()->json(['message' => 'Успешно извличане на информацията'], 200);
    }
}
