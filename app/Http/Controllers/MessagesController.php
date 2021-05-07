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

        $messages = Message::where([['sender_id','=',$request->id],['reciever_id','=',$request->reciever_id]])
        ->orwhere([['sender_id','=',$request->reciever_id],['reciever_id','=',$request->id]])
        ->join('users as usersA', 'messages.sender_id', '=', 'usersA.id')
        ->join('users as usersB', 'messages.reciever_id', '=', 'usersB.id')
        ->select('messages.*', 'usersA.name as senderName','usersB.name as recieverName')
        ->get();

         return response(['status'=>'Messages loaded','messages'=>$messages]);
    }


    public function sendPrivateMessage(Request $request)
    {
        
        // $user =  User::find($request->sender_id);
        $message=Message::create(['message' => $request->message, 'sender_id' =>$request->sender_id, 'reciever_id' => $request->reciever_id]);
        dump($message);
        dump($message->load('user'));
        dd($message);
        try {
            broadcast(new PrivateMessage($message->load('user')))->toOthers();
        } catch (Exception $e) {
            return response(['error' => 'failed']);            
        }
        
        return response(['status'=>'Message private sent successfully','message'=>$message]);

    }
}
