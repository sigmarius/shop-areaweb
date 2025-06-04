<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Facades\PostFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\AddPostCommentRequest;
use App\Http\Requests\Post\GetPostsFeedRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\v1\Comment\CommentResource;
use App\Http\Resources\v1\Post\PostFeedResource;
use App\Http\Resources\v1\Post\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('post.access', only: ['destroy'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(GetPostsFeedRequest $request): JsonResponse
    {
        return response()->json([
            'total' => PostFacade::totalFeedPosts(),
            'posts' => PostFeedResource::collection(
                PostFacade::feed($request->limit(), $request->offset())
            )
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): PostResource
    {
        return PostResource::make(
            PostFacade::store($request->toDTO())
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): PostResource
    {
        return PostResource::make($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): PostResource
    {
        $post = PostFacade::setPost($post)
            ->update($request->toDTO());

        return PostResource::make($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): Response
    {
        $post->delete();

        return response()->noContent();
    }

    public function like(Post $post): JsonResponse
    {
        return response()->json([
            'state' => $post->like(),
        ]);
    }

    public function addComment(Post $post, AddPostCommentRequest $request): CommentResource
    {
        return CommentResource::make(
            $post->comments()
                ->create($request->toDTO()->toArray())
        );
    }
}
