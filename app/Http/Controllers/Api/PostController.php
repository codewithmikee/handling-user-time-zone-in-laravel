<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseControllers\ProtectedApiController;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
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
        return $this->handleRequest(function () use ($request) {

        $data = $request->validated();

        $post = Post::create( ['user_id' => $this->getCurrentUserId(), ...$data]);

        return  new PostResource($post);
        }, $request, 'Post created successfully');
    }


    public function update(UpdatePostRequest $request, Post $post)
    {
        return $this->handleRequest(function () use ($request, $post) {

            if ($post->user_id !== $this->getCurrentUserId()) {
                return $this->throwUnAuthorized('Unauthorized', ['invalid_owner' => 'You are not the owner of this post']);
            }

            $post->update($request->validated());
            return new PostResource($post);
        }, $request, 'Post updated successfully');
    }

    public function destroy(Request $request, Post $post)
    {
        return $this->handleRequest(function () use ($request, $post) {

            if ($post->user_id !== $this->getCurrentUserId()) {
                return $this->throwUnAuthorized('Unauthorized', ['invalid_owner' => 'You are not the owner of this post']);
            }

            $post->delete();
            return $this->respondSuccess(null, 'Post deleted successfully');
        }, $request, 'Post deleted successfully');
    }

}
