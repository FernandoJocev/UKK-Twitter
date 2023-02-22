<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tags;

class TagTweet extends Model
{
    use HasFactory;

    protected $table ='tag_tweet';

    protected $guarded = ['id'];

    public function tweets()
    {
        return $this->hasOne(Tweets::class, 'id', 'tweet_id')->with('user');
    }

    public function tag()
    {
        return $this->hasOne(Tags::class, 'id', 'tag_id');
    }
}
