<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use App\Models\Review;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
   
        public function run(): void
        {
            $faker = \Faker\Factory::create();

            File::ensureDirectoryExists(storage_path('app/test-tokens'));
    
            $admin1 = User::factory()->create([
                'name' => 'Admin One',
                'email' => 'admin1@example.com',
                'role' => 'admin',
            ]);
    
            $admin2 = User::factory()->create([
                'name' => 'Admin Two',
                'email' => 'admin2@example.com',
                'role' => 'admin',
            ]);
    
            $user1 = User::factory()->create([
                'name' => 'User One',
                'email' => 'user1@example.com',
                'role' => 'user',
            ]);
    
            $user2 = User::factory()->create([
                'name' => 'User Two',
                'email' => 'user2@example.com',
                'role' => 'user',
            ]);
    
            $user3 = User::factory()->create([
                'name' => 'User Three',
                'email' => 'user3@example.com',
                'role' => 'user',
            ]);
    
            $normalUsers = collect([$user1, $user2, $user3]);
    
            $categories = Category::factory()->count(10)->create();
    
            Book::factory()->count(30)->make()->each(function ($book) use ($normalUsers, $categories, $faker) {
                $owner = $normalUsers->random();
                $book->user_id = $owner->id;
                $book->approved = $faker->boolean(70); // 70% approved
                $book->pdf_path = 'books/' .'dummy'.'.pdf';
                $book->save();
    
                $book->categories()->attach(
                    $categories->random(rand(1, 3))->pluck('id')->toArray()
                );

                $reviewCount = rand(1, 5);
                for ($r = 0; $r < $reviewCount; $r++) {
                    Review::create([
                        'book_id' => $book->id,
                        'user_id' => $normalUsers->random()->id,
                        'content' => $faker->boolean(20) ? '<script>alert("XSS")</script>' : $faker->paragraph(),
                        'approved' => $faker->boolean(70),
                    ]);
                }
            });
    
            foreach (User::all() as $user) {
                $token = $this->generateForgedJWT($user);
                File::put(storage_path("app/test-tokens/{$user->email}.jwt"), $token);
            }
        }
    
        private function generateForgedJWT(User $user)
        {
            // FAKE JWT HEADER
            $header = base64_encode(json_encode([
                'alg' => 'none', 'typ' => 'JWT'
            ]));
    
            // FAKE JWT PAYLOAD
            $payload = base64_encode(json_encode([
                'sub' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'iat' => time(),
                'exp' => time() + 60 * 60
            ]));
    
            // NO SIGNATURE
            return "$header.$payload.";
        }



        
    }



