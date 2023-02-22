<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweets extends Model
{
    use HasFactory;

    protected $table = 'tweets';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany(TagTweet::class, 'tweet_id', 'id')->with('tag');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'tweet_id', 'id');
    }

    public function tag_comment()
    {
        return $this->hasMany(TagComment::class, 'tweet_id', 'id')->with('tag');
    }
}
