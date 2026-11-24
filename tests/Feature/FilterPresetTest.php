<?php

namespace Tests\Feature;

use App\Models\FilterPreset;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterPresetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_save_a_filter_preset(): void
    {
        $response = $this->post(route('filter-presets.store'), [
            'name' => 'Moje filtry',
            'filters' => ['sort' => 'latest'],
        ]);

        $response->assertRedirect(route('login'));
        $this->assertSame(0, FilterPreset::count());
    }

    public function test_rejects_a_preset_without_a_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('filter-presets.store'), [
            'name' => '',
            'filters' => ['sort' => 'latest'],
        ]);

        $response->assertRedirect()->assertSessionHasErrors('name');
        $this->assertSame(0, $user->filterPresets()->count());
    }

    public function test_rejects_preset_referencing_unknown_tags(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('filter-presets.store'), [
            'name' => 'Zły tag',
            'filters' => ['tags' => ['nie-istnieje']],
        ]);

        $response->assertRedirect()->assertSessionHasErrors('filters.tags.0');
        $this->assertSame(0, $user->filterPresets()->count());
    }

    public function test_saves_a_preset_with_only_supported_filters(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['slug' => 'egzaminy']);

        $response = $this->actingAs($user)->post(route('filter-presets.store'), [
            'name' => 'Przed sesją',
            'filters' => [
                'search' => 'egzamin',
                'tags' => [$tag->slug, $tag->slug, ''],
                'sort' => 'popular',
                'user_id' => '999',
            ],
        ]);

        $response->assertRedirect();

        $preset = $user->filterPresets()->firstOrFail();
        $this->assertSame('Przed sesją', $preset->name);
        $this->assertSame('egzamin', $preset->filters['search']);
        $this->assertSame([$tag->slug], $preset->filters['tags']);
        $this->assertSame('popular', $preset->filters['sort']);
        $this->assertArrayNotHasKey('user_id', $preset->filters);
    }

    public function test_saving_the_same_name_updates_the_existing_preset(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('filter-presets.store'), [
            'name' => 'Szybki dostęp',
            'filters' => ['sort' => 'latest'],
        ]);
        $this->actingAs($user)->post(route('filter-presets.store'), [
            'name' => 'Szybki dostęp',
            'filters' => ['sort' => 'oldest'],
        ]);

        $this->assertSame(1, $user->filterPresets()->count());
        $this->assertSame('oldest', $user->filterPresets()->firstOrFail()->filters['sort']);
    }

    public function test_owner_can_delete_a_preset(): void
    {
        $user = User::factory()->create();
        $preset = FilterPreset::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('filter-presets.destroy', $preset));

        $response->assertRedirect();
        $this->assertModelMissing($preset);
    }

    public function test_user_cannot_delete_another_users_preset(): void
    {
        $preset = FilterPreset::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->delete(route('filter-presets.destroy', $preset));

        $response->assertForbidden();
        $this->assertModelExists($preset);
    }

    public function test_listing_exposes_the_users_presets(): void
    {
        $user = User::factory()->create();
        $preset = FilterPreset::factory()->for($user)->create([
            'name' => 'Sesja',
            'filters' => ['category' => 'Egzaminy i sesja', 'sort' => 'oldest'],
        ]);

        $response = $this->actingAs($user)->get(route('posts.index'));

        $response->assertOk()->assertViewHas('filterPresets', function ($presets) use ($preset): bool {
            $stored = $presets->firstWhere('id', $preset->id);

            return $stored !== null && $stored->filters === $preset->filters;
        });
    }
}
