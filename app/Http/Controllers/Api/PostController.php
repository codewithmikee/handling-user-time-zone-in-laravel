<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->handleRequest(function () {

            $userId = $this->getCurrentUserId();
            $posts = Post::where('user_id', $userId)->paginate(10);

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
