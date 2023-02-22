<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $guarded = ['id'];

    public function tag_tweet()
    {
        return $this->hasMany(TagTweet::class, 'tag_id', 'id')->with('tweets');
    }

    public function tag_comment()
    {
        return $this->hasMany(TagComment::class, 'tag_id', 'id')->with('comments');
    }
}
