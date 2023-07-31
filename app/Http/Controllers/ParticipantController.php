<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Participant;
use App\Models\User;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $count = Participant::selectRaw('count(*) as total')
                    ->where('room_id',$request->room_id)
                    ->where('user_id',$request->user)
                    ->first();
            if ($count->total >= 1) {
                return redirect('/room/error');
                // return response()->json([       
                //     'status' => false,
                //     'message' => 'You have already in this room.',
                // ], 401);
            }
           Participant::create([
                'room_id' => $request->room_id,
                'user_id' => $request->user,   
           ]);  
           return redirect('/dashboard');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function show(Participant $participant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function edit(Participant $participant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Participant $participant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function destroy($room_id)
    {
        $user_id = Auth::id();
        $participant = DB::table('participants')
                        ->select('id')
                        ->where('user_id',$user_id)
                        ->where('room_id', $room_id)
                        ->first();
        
        $p = Participant::FindOrFail($participant->id);
       
        $p->delete();
       //return response()->json($participant->id);
        return redirect('/dashboard');
        //
    }
}
