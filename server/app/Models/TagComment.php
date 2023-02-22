<?php

namespace App\Models;

use App\Models\Tags;
use App\Models\Comments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TagComment extends Model
{
    use HasFactory;

    protected $table = 'tag_comment';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function comments()
    {
        return $this->hasOne(Comments::class, 'id', 'comment_id')->with('user');
    }

    public function tag()
    {
        return $this->hasOne(Tags::class, 'id', 'tag_id');
    }
}
