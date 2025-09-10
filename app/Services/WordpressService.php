<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class WordpressService
{
    protected string $site;

    public function __construct()
    {
        $this->site = env('WP_SITE_SLUG');
    }

    protected function base(): string
    {
        return "https://public-api.wordpress.com/wp/v2/sites/{$this->site}";
    }

    protected function token(User $user): ?string
    {
        $data = json_decode($user->wp_token ?? '{}', true);
        return $data['access_token'] ?? null;
    }

    // Fetch all posts for a user
    public function getAllPosts(User $user, int $perPage = 20): array
    {
        $allPosts = [];
        $page = 1;

        do {
            $response = Http::withToken($this->token($user))
                ->get($this->base() . '/posts', ['page' => $page, 'per_page' => $perPage])
                ->throw()
                ->json();

            if (!empty($response)) {
                $allPosts = array_merge($allPosts, $response);
                $page++;
            } else {
                break;
            }
        } while (count($response) === $perPage);

        return $allPosts;
    }

    // Fetch all users
    public function getAllUsers(User $user, int $perPage = 20): array
    {
        $allUsers = [];
        $page = 1;

        do {
            $response = Http::withToken($this->token($user))
                ->get($this->base() . '/users', ['page' => $page, 'per_page' => $perPage])
                ->throw()
                ->json();

            if (!empty($response)) {
                $allUsers = array_merge($allUsers, $response);
                $page++;
            } else {
                break;
            }
        } while (count($response) === $perPage);

        return $allUsers;
    }


    // Create a new post
    public function createPost(User $user, array $data): array
    {
        $response = Http::withToken($this->token($user))
            ->post($this->base() . '/posts', $data)
            ->throw()
            ->json();

        return $response;
    }

    // Update an existing post
    public function updatePost(User $user, int $postId, array $data): array
    {
        $response = Http::withToken($this->token($user))
            ->post($this->base() . "/posts/{$postId}", $data)
            ->throw()
            ->json();

        return $response;
    }

    // Delete a post
    public function deletePost(User $user, int $postId, bool $force = true): array
    {
        $response = Http::withToken($this->token($user))
            ->delete($this->base() . "/posts/{$postId}", ['force' => $force])
            ->throw()
            ->json();

        return $response;
    }
}