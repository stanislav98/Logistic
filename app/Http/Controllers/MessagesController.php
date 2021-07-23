<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Events\PrivateMessage;
use DB;
class MessagesController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }
    
    public function fetchMessages()
    {
        return Message::with('user')->get();
    }
   
    public function privateMessages(Request $request)
    {

        $userId = $request->id;
        // $messages = Message::where([['sender_id','=',$request->id],['reciever_id','=',$request->reciever_id]])
        // ->orwhere([['sender_id','=',$request->reciever_id],['reciever_id','=',$request->id]])
        // ->join('users as usersA', 'messages.sender_id', '=', 'usersA.id')
        // ->join('users as usersB', 'messages.reciever_id', '=', 'usersB.id')
        // ->select('messages.*', 'usersA.name as senderName','usersB.name as recieverName')
        // ->get();

        //  return response(['status'=>'Messages loaded','messages'=>$messages]);
        $messages = Message::where([['reciever_id','=',$userId]])
            ->orwhere([['sender_id','=',$userId]])
            // ->join('users as usersA', 'messages.sender_id', '=', 'usersA.id')
            // ->join('users as usersB', 'messages.reciever_id', '=', 'usersB.id')
            // ->select('messages.*', 'usersA.name as senderName','usersB.name as recieverName')
            ->orderBy('id', 'ASC')
            ->get()
            ->toArray();

        // $formatedMessages = [];

        // foreach($messages as $k => $v) {
            
        //     if (!isset($formatedMessages[$v['sender_id']]['messages']) || 
        //         !isset($formatedMessages[$v['reciever_id']]['messages'])
        //     )
        //     {
        //         $formatedMessages[$v['sender_id']]['messages'] = [];
        //         $formatedMessages[$v['reciever_id']]['messages'] = [];
        //     }

        //     if($v['sender_id'] == $userId){
        //         array_push($formatedMessages[$v['reciever_id']]['messages'],$v);
        //     } else if($v['reciever_id'] == $userId) {
        //         array_push($formatedMessages[$v['sender_id']]['messages'],$v);
        //     }

        // }

        // unset($formatedMessages[$userId]);

        return response(['status'=>'Messages loaded','messages'=>$messages]);
    }


    public function sendPrivateMessage(Request $request)
    {
        
        $message = Message::create(
            [
                'message' => $request->message, 
                'sender_id' =>$request->sender_id, 
                'reciever_id' => $request->reciever_id,
                'status' => $request->status
            ]
        );
        try {
            broadcast(new PrivateMessage($message->load('user')))->toOthers();
        } catch (Exception $e) {
            return response(['error' => 'failed']);            
        }
        
        return response(['status'=>'Message private sent successfully','message'=>$message],200);

    }
}
