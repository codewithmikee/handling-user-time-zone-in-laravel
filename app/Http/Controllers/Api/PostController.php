<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use Throwable;
use Illuminate\Http\Request;
class PostController extends ProtectedApiController
{
    // WRAPPED TO HANDLE REQUEST BUT RETURNING CUSTOM RESPONSE
    public function index(Request $request)
    {
        return $this->handleRequest(function() use ($request) {
            $posts = Post::paginate(10);

            return $this->respondWithPagination(
                $posts,
                PostResource::class,
                'Posts fetched successfully'
            );

        }, $request, 'Posts fetched successfully');
    }

    // WITHOUT WRAPPING TO HANDLE REQUEST
    public function store(StorePostRequest $request)
    {
        // Auto-validated by StorePostRequest


        $this->authorize('create', Post::class);

        $post = Post::create($request->validated());

        return $this->respondSuccess(
            new PostResource($post),
            'Post created successfully',
            201
        );
    }
}
