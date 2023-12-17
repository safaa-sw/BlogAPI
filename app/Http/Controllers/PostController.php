<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use App\Http\Resources\Post as PostResource;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts=Post::all();
        return $this->sendResponse(PostResource::collection($posts),"all Posts");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input=$request->all();
        $validator=Validator::make($input,[
            'title' => 'required',
            'description' => 'required',
        ]);
        if($validator->fails())
        {
            return $this->sendError('Validate Error',$validator->errors());
        }
        $user=Auth::user();
        $input['user_id']=$user->id;
        $post=Post::create($input);
        $post->save();
        return $this->sendResponse(new PostResource($post),"Post added Successfully");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post=Post::find($id);
        if(is_null($post))
        {
            return $this->sendError('Post not Found');
        }
        return $this->sendResponse(new PostResource($post),"Post Found Successfully");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator=Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required',
        ]);
        if($validator->fails())
        {
            return $this->sendError('Validation Error',$validator->errors());
        }
        $post=Post::Find($id);
        /*if(is_null($post))
        {
            return $this->sendError('Post not Found');
        }*/

        if ($post->user_id != Auth::id()) {
            return $this->sendError('You dont have rights');
        }
        $post->title=$request->title;
        $post->description=$request->description;
        $post->save();
        return $this->sendResponse(new PostResource($post),"Post updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return $this->sendResponse(new PostResource($post),"Post deleted Successfully");
    }

    public function userPosts($id)
    {
        $posts=Post::where('user_id',$id)->get();
        return $this->sendResponse(PostResource::collection($posts),"Posts For One User");
    }
}
