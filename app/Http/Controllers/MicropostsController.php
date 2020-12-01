<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MicropostsController extends Controller
{
    public function index() //use Authしない代わりに\Auth::check()こう書く
    {
        $data = []; 
        if (\Auth::check()) {
            $user = \Auth::user();
            $microposts = $user->feed_microposts()->orderBy('created_at', 'desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'microposts' => $microposts,
            ];
        }
        return view('welcome', $data);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:191',
        ]);
        
        $request->user()->microposts()->create([
            'content' => $request->content, 
        ]);
        
        return back(); //投稿完了後に直前のページが表示
    }
    
    public function destroy($id)
    {
        $micropost = \App\Micropost::find($id);
    
        if (\Auth::id() === $micropost->user_id) {
            $micropost->delete();
        }
        
        return back();
    }
}
