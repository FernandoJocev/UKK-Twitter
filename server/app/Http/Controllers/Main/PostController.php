<?php

namespace App\Http\Controllers\Main;

use App\Models\Tags;
use App\Models\Tweets;
use App\Models\Comments;
use App\Models\TagTweet;
use App\Models\TagComment;
use Illuminate\Http\Request;
use App\Helpers\ValidatorHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createPost(Request $request)
    {
        $validator = ValidatorHelper::PostValidator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $data = explode('#', $request->tweet);
        $sliced_tags = array_slice($data, 1);
        $tags = implode("", $sliced_tags);

        // return $tags;

        $tweet = Tweets::create([
            'user_id' => Crypt::decrypt($request->user_id),
            'tweet' => $data[0],
            'media' => $request->media,
        ]);


        if (count($data) > 1) {
            Tags::create([
                'name' => $tags,
            ]);
        }

        if (count($data) > 1) {
            $getTweetTags = Tags::latest()->get();
            $data = Tweets::with('user')->latest()->get();
            TagTweet::create([
                'tweet_id' => $data[0]->id,
                'tag_id' => $getTweetTags[0]->id,
            ]);
            return response()->json([
                'message' => 'Tweet berhasil di tambahkan',
                'data' => $data,
            ], 200);
        }

        if ($tweet) {
            return response()->json([
                'message' => 'Tweet berhasil di tambahkan',
                'data' => $data,
            ], 200);
        }

        return response()->json([
            'message' => 'Error'
        ], 500);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function comments($)
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getComments($id)
    {
        $comments = Comments::with('tags', 'user')->where('tweet_id', $id)->latest()->get();

        return $comments;

        return response()->json([
            'data' => $comments,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comment(Request $request, $id)
    {
        $user_id = Crypt::decrypt($request->token);

        $data = explode('#', $request->comment);
        $sliced_tags = array_slice($data, 1);
        $tags = implode("", $sliced_tags);

        // return count($data);

        if (count($data) > 1) {
            Tags::create([
                'name' => $tags,
            ]);
        }

        $comment = Comments::create([
            'comments' => $data[0],
            'media' => $request->media,
            'tweet_id' => $id,
            'user_id' => $user_id,
        ]);

        if (count($data) > 1) {
            $getTweetTags = Tags::latest()->get();
            $getComment = Comments::latest()->get();
            TagComment::create([
                'tweet_id' => $id,
                'comment_id' => $getComment[0]->id,
                'tag_id' => $getTweetTags[0]->id,
            ]);
            return response()->json([
                'message' => 'Comment berhasil di tambahkan',
            ], 200);
        }

        if ($comment) {
            return response()->json([
                'message' => 'Commented',
            ], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPost($id)
    {
        $tweet = Tweets::with('tags', 'user')->where('id', $id)->first();

        return response()->json([
            'data' => $tweet,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePost(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tweet' => 'required|max:250'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ]);
        }

        $post = Tweets::findorfail($id);

        $data = explode('#', $request->tweet);
        $sliced_tags = array_slice($data, 1);
        $tags = implode("", $sliced_tags);

        $checkTags = Tags::where('name', $tags)->get();

        $post->tweet = $data[0];
        $post->media = $request->media;

        $post->save();

        if (count($checkTags) == 0 && count($data) > 0) {
            Tags::create([
                'name' => $tags,
            ]);
        }

        $getTweetTags = Tags::latest()->get();
        $data = Tweets::with('user')->latest()->get();

        $tweetTag = TagTweet::where('tweet_id', $id);

        if ($tweetTag != null) {
            $tweetTag->update([
                'tweet_id' => $data[0]->id,
                'tag_id' => $getTweetTags[0]->id,
            ]);
        } else if ($tweetTag->first() == null) {
            TagTweet::create([
                'tweet_id' => $data[0]->id,
                'tag_id' => $getTweetTags[0]->id
            ]);
        }

        if ($tweetTag != null && $post) {
            return response()->json([
                'message' => 'Tweet updated',
            ], 200);
        }

        // return response()->json([
        //     'message' => 'Error'
        // ], 500);
    }

    public function getEditComment($id)
    {
        $comment = Comments::where('id', $id)->first();

        return response()->json([
            'data' => $comment
        ], 200);
    }

    public function updateComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        };

        $comment = Comments::findorfail($request->id_comment);

        $data = explode('#', $request->comment);
        $sliced_tags = array_slice($data, 1);
        $tags = implode("", $sliced_tags);

        $checkTags = Tags::where('name', $tags)->get();

        $comment->comments = $data[0];
        $comment->media = $request->media;

        $comment->save();

        if (count($checkTags) == 0 && count($data) > 0) {
            Tags::create([
                'name' => $tags,
            ]);
        }

        $getCommentTags = Tags::latest()->get();
        $data = Tweets::with('user')->latest()->get();

        $commentTag = TagComment::where('comment_id', $request->id_comment);

        if ($commentTag != null) {
            $commentTag->update([
                'tweet_id' => $data[0]->id,
                'tag_id' => $getCommentTags[0]->id,
            ]);
        } else if ($commentTag->first() == null) {
            TagComment::create([
                'tweet_id' => $data[0]->id,
                'tag_id' => $getCommentTags[0]->id
            ]);
        }

        if ($commentTag != null && $comment) {
            return response()->json([
                'message' => 'Tweet updated',
            ], 200);
        }

        return response()->json([
            'message' => 'Komentar di perbarui'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePost($id)
    {
        $post = Tweets::where('id', $id);

        $post->delete();

        return response()->json([
            'message' => 'Tweet di hapus'
        ], 200);
    }

    public function searchPost(Request $request)
    {
        $findTags = Tags::with('tag_tweet', 'tag_comment')->where('name', 'LIKE', '%' . $request->get('query') . '%')->get();

        return response()->json([
            'data' => $findTags,
        ]);
    }

    public function deleteComment($id)
    {
        $comment = Comments::where('id', $id);
        $comment->delete();

        return response()->json([
            'message' => 'Komentar di hapus'
        ]);
    }
}
