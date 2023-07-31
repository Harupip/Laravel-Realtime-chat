<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Participant;
use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;  

class MainController extends Controller
{
    public function index()
    {
        $id = Auth::id();
        $rooms = Room::all();
        $users = User::all();
        $messages = Message::all();
        $participants = DB::table('rooms')
                            ->join('participants', 'rooms.id','=','participants.room_id')
                            ->where('participants.user_id','=',$id)
                            ->select('rooms.id as id','rooms.name as name')
                            ->get(); 
        return view('dashboard', 
        compact('rooms', 'users', 'messages', 'participants'));
        //
    }
}
