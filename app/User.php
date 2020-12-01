<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        // User が持つ Microposts を $user->microposts()->get() もしくは $user->microposts で取得
        return $this->hasMany(Micropost::class);
    }
    
    public function followings() // $user->followings で $user が フォローしている User 達を取得
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
//第一引数に得られる Model クラス (User::class) を指定し、第二引数に中間テーブル (user_follow) を指定し、
//第三引数に中間テーブルに保存されている自分の id を示すカラム名 (user_id) を指定し、
//第四引数に中間テーブルに保存されている関係先の id を示すカラム名 (follow_id) を指定します。
    
    public function followers() // $user->followers も同様で $user をフォローしている User 達を取得
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身ではないかの確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    // タイムライン用
    public function feed_microposts()
    {
        // User がフォローしている User の id の配列を取得
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        // 自分の id も追加
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
}
