<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use Mail;
use App\Models\Contact;

class ContactController extends Controller
{
   	/**
	 * This function is used to handle contact form submit
	 * Store Message | Send Email
	 * 
	 * @param ContactRequest $request
	 * @return Illuminate\Http\JsonResponse
	 */
    public function sendMessage(ContactRequest $request): object
    {
        $validated = $request->validated();

        $contact = Contact::create($validated);
        
        if($contact->exists) {
        	Mail::to('staskata1998@gmail.com')->send(new \App\Mail\ContactEmail($validated));
    		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Успешно изпратихте съобщението си! Благодарим ви!' ,
	        			'type' => 1, 
	        			'active' => 1
	        		] 
	            ], 200);
        } 


    	return response()->json(
        	['notification' => 
        		[
        			'msg' => 'Съобщението ви не беше изпратено!' ,
        			'type' => 1, 
        			'active' => 1
        		] 
            ], 422);
    }
}

