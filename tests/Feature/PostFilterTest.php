<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_filter_keeps_only_posts_having_every_selected_tag(): void
    {
        $user = User::factory()->create();
        $alpha = Tag::factory()->create(['name' => 'Alfa', 'slug' => 'alfa']);
        $beta = Tag::factory()->create(['name' => 'Beta', 'slug' => 'beta']);
        $withBoth = Post::factory()->for($user)->create();
        $withAlphaOnly = Post::factory()->for($user)->create();
        $withBoth->tags()->attach([$alpha->id, $beta->id]);
        $withAlphaOnly->tags()->attach($alpha->id);

        $response = $this->get(route('posts.index', ['tags' => ['alfa', 'beta']]));

        $response->assertOk()->assertViewHas('posts', function ($posts) use ($withBoth, $withAlphaOnly): bool {
            $visibleIds = $posts->pluck('id')->all();

            return in_array($withBoth->id, $visibleIds, true)
                && ! in_array($withAlphaOnly->id, $visibleIds, true);
        });
    }

    public function test_tag_counts_exclude_posts_that_are_not_publicly_visible(): void
    {
        $visibleTag = Tag::factory()->create();
        $hiddenTag = Tag::factory()->create();
        $published = Post::factory()->create();
        $draft = Post::factory()->draft()->create();
        $pending = Post::factory()->pending()->create();
        $published->tags()->attach($visibleTag->id);
        $draft->tags()->attach([$visibleTag->id, $hiddenTag->id]);
        $pending->tags()->attach([$visibleTag->id, $hiddenTag->id]);

        $response = $this->get(route('posts.index'));

        $response->assertOk()->assertViewHas('tags', function ($tags) use ($visibleTag, $hiddenTag): bool {
            $counts = $tags->pluck('posts_count', 'id')->all();

            return (int) $counts[$visibleTag->id] === 1
                && (int) $counts[$hiddenTag->id] === 0;
        });
    }

    public function test_tags_are_ordered_by_public_post_count_descending(): void
    {
        $popular = Tag::factory()->create(['name' => 'Popularny', 'slug' => 'popularny']);
        $rare = Tag::factory()->create(['name' => 'Rzadki', 'slug' => 'rzadki']);
        Post::factory()->count(3)->create()->each(fn (Post $post) => $post->tags()->attach($popular));
        Post::factory()->create()->tags()->attach($rare);

        $response = $this->get(route('posts.index'));

        $response->assertOk()->assertViewHas('tags', function ($tags): bool {
            return $tags->pluck('slug')->all() === ['popularny', 'rzadki'];
        });
    }

    public function test_active_filters_expose_unique_selected_tag_slugs(): void
    {
        $response = $this->get(route('posts.index', ['tags' => ['beta', '', 'alfa', 'alfa']]));

        $response->assertOk()->assertViewHas('activeFilters.tags', ['beta', 'alfa']);
    }

    public function test_post_card_tag_link_uses_the_array_tag_filter(): void
    {
        $tag = Tag::factory()->create(['slug' => 'egzaminy']);
        Post::factory()->create()->tags()->attach($tag);

        $response = $this->get(route('posts.index'));

        $response->assertOk()
            ->assertSee(route('posts.index', ['tags' => ['egzaminy']]), false);
    }
}
