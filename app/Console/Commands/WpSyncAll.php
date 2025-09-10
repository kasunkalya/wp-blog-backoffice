<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WordpressService;
use App\Models\User;
use App\Models\Post;

class WpSyncAll extends Command
{
    protected $signature = 'wp:sync-all';
    protected $description = 'Sync all WordPress users and posts';

    public function handle(): int
    {
        
        $admin = User::first(); 
        if (!$admin || !$admin->wp_token) {
            $this->error('No admin user with WP token found.');
            return self::FAILURE;
        }

        $wpService = new WordpressService();

      
        $this->info('Fetching WordPress users...');
        $users = $wpService->getAllUsers($admin);
        foreach ($users as $wpUser) {
            User::updateOrCreate(
                ['email' => $wpUser['email'] ?? "wp_{$wpUser['id']}@example.local"],
                [
                    'name' => $wpUser['name'] ?? $wpUser['username'] ?? 'WP User',
                    'wp_user_id' => $wpUser['id'] ?? null,
                ]
            );
        }
        $this->info('Users synced: ' . count($users));

        $this->info('Fetching posts for each user...');
        $usersWithToken = User::whereNotNull('wp_token')->get();

        $totalPosts = 0;
        foreach ($usersWithToken as $user) {
            $posts = $wpService->getAllPosts($user);
            foreach ($posts as $wpPost) {
                Post::updateOrCreate(
                    ['wp_id' => $wpPost['id']],
                    [
                        'title' => is_array($wpPost['title']) ? $wpPost['title']['rendered'] ?? '' : $wpPost['title'] ?? '',
                        'content' => is_array($wpPost['content']) ? $wpPost['content']['rendered'] ?? '' : $wpPost['content'] ?? '',
                        'status' => $wpPost['status'] ?? 'draft',
                        'priority' => $wpPost['priority'] ?? 0,
                    ]
                );
            }

            $totalPosts += count($posts);
        }

        $this->info("All posts synced. Total posts: $totalPosts");

        return self::SUCCESS;
    }
}
