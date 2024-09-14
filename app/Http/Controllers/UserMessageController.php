<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserMessage;
use Illuminate\Support\Facades\Mail;


class UserMessageController extends Controller
{
    /**
     * Store a new user message in the database.
     *
     * @param Request $request The request object containing the message, name, and email.
     * @throws Some_Exception_Class If the message cannot be saved.
     * @return array An array indicating the success of the operation.
     */
    public function store(Request $request)
    {
        $newMessage = new UserMessage;
        $newMessage->message = $request->message;
        $newMessage->name = $request->name;
        $newMessage->email = $request->email;

        $newMessage->save();
        
        return ["success"=> true];
    }

    public function index()
    {
        $messages = UserMessage::all();
        return view('messages.index', ['messages' => $messages]);
    }
}
