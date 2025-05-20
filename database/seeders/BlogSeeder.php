<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(50)
            ->has(
                Post::factory()
                    ->has(
                        Comment::factory()
                        ->for(User::factory())
                    )
                    ->has(
                        Like::factory()
                            ->for(User::factory())
                    )
            )
            ->create();

        $users = User::all();

        foreach ($users as $user) {
            $randomUsers = User::query()
                ->inRandomOrder(rand(1, 30))
                ->get();

            $data = [];

            foreach ($randomUsers as $randomUser) {
                $data[] = [
                    'user_id' => $user->id,
                    'subscriber_id' => $randomUser->id,
                ];
            }

            Subscription::query()
                ->insert($data);
        }
    }
}
