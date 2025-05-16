<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use Throwable;
use Illuminate\Http\Request;

/**
 * Controller for managing posts (example resource).
 *
 * Demonstrates usage of handleRequest and standardized API responses.
 */
class PostController extends ProtectedApiController
{
    /**
     * List paginated posts with standardized response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Store a new post (auto-validated by StorePostRequest).
     *
     * @param StorePostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
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
