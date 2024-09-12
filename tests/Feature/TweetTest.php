use App\Models\Tweet;
use App\Models\User;

// 🔽一覧取得のテスト
it('displays tweets', function () {
  $user = User::factory()->create();

  $this->actingAs($user);

  $tweet = Tweet::factory()->create();

  $response = $this->get('/tweets');

  $response->assertStatus(200);
  $response->assertSee($tweet->tweet);
  $response->assertSee($tweet->user->name);
});

// 作成画面のテスト
it('displays the create tweet page', function () {
  $user = User::factory()->create();

  $this->actingAs($user);

  $response = $this->get('/tweets/create');

  $response->assertStatus(200);
});

// 作成処理のテスト
it('allows authenticated users to create a tweet', function () {
  $user = User::factory()->create();

  $this->actingAs($user);

  $tweetData = ['tweet' => 'This is a test tweet.'];

  $response = $this->post('/tweets', $tweetData);

  $this->assertDatabaseHas('tweets', $tweetData);

  $response->assertStatus(302);
  $response->assertRedirect('/tweets');
});

// 詳細画面のテスト
it('displays a tweet', function () {
  $user = User::factory()->create();

  $this->actingAs($user);

  $tweet = Tweet::factory()->create();

  $response = $this->get("/tweets/{$tweet->id}");

  $response->assertStatus(200);
  $response->assertSee($tweet->tweet);
  $response->assertSee($tweet->created_at->format('Y-m-d H:i'));
  $response->assertSee($tweet->updated_at->format('Y-m-d H:i'));
  $response->assertSee($tweet->tweet);
  $response->assertSee($tweet->user->name);
});

// 編集画面のテスト
it('displays the edit tweet page', function () {
  $user = User::factory()->create();

  $this->actingAs($user);

  $tweet = Tweet::factory()->create(['user_id' => $user->id]);

  $response = $this->get("/tweets/{$tweet->id}/edit");

  $response->assertStatus(200);

  $response->assertSee($tweet->tweet);
});

// 更新処理のテスト
it('allows a user to update their tweet', function () {
  $user = User::factory()->create();

  $this->actingAs($user);

  $tweet = Tweet::factory()->create(['user_id' => $user->id]);

  $updatedData = ['tweet' => 'Updated tweet content.'];

  $response = $this->put("/tweets/{$tweet->id}", $updatedData);

  $this->assertDatabaseHas('tweets', $updatedData);

  $response->assertStatus(302);
  $response->assertRedirect("/tweets/{$tweet->id}");
});

// 削除処理のテスト
it('allows a user to delete their tweet', function () {
  $user = User::factory()->create();

  $this->actingAs($user);

  $tweet = Tweet::factory()->create(['user_id' => $user->id]);

  $response = $this->delete("/tweets/{$tweet->id}");

  $this->assertDatabaseMissing('tweets', ['id' => $tweet->id]);

  $response->assertStatus(302);
  $response->assertRedirect('/tweets');
});

