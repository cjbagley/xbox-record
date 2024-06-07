<?php

use App\Models\Tag;

const ADMIN_TAG_URL = '/admin/tags';

test('tag index page is displayed', function () {
    $this
        ->actingAs(create_test_user())
        ->get(ADMIN_TAG_URL)
        ->assertOk();
});

test('tag can be added', function () {
    $tag = Tag::factory()->make();

    $this
        ->actingAs(create_test_user())
        ->post(ADMIN_TAG_URL, [
            'colour' => $tag->colour,
            'is_sensitive' => $tag->is_sensitive,
            'tag' => $tag->tag,
            'code' => $tag->code,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(ADMIN_TAG_URL);

    $saved_tag = Tag::where(['tag' => $tag->tag])->first();
    expect($saved_tag->tag)->toBe($tag->tag)
        ->and($saved_tag->colour)->toBe($tag->colour)
        ->and($saved_tag->code)->toBe($tag->code)
        ->and((bool) $saved_tag->is_sensitive)->toBe((bool) $tag->is_sensitive);
});

test('tag can be edited', function () {
    $tag = create_test_tag();
    $original_tag = clone $tag;

    $new_tag = fake()->word();
    $new_code = 'ZZZ';

    $this
        ->actingAs(create_test_user())
        ->put(sprintf('%s/%s', ADMIN_TAG_URL, $original_tag->id), [
            'tag' => $new_tag,
            'code' => $new_code,
            'colour' => $original_tag->colour,
            'is_sensitive' => $original_tag->is_sensitive,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(ADMIN_TAG_URL);

    $updated_tag = Tag::find($original_tag->id);

    expect($updated_tag->tag)->not->toBe($original_tag->tag);
    expect($updated_tag->code)->not->toBe($original_tag->code);

    expect($updated_tag->colour)->toBe($original_tag->colour)
        ->and((bool) $updated_tag->is_sensitive)->toBe((bool) $original_tag->is_sensitive)
        ->and($updated_tag->tag)->toBe($new_tag)
        ->and($updated_tag->code)->toBe($new_code);
});

test('tag can be deleted', function () {
    $tag = create_test_tag();

    $this
        ->actingAs(create_test_user())
        ->delete(route('tags.destroy', $tag))
        ->assertSessionHasNoErrors()
        ->assertRedirect(ADMIN_TAG_URL);

    expect(Tag::find($tag->id))->toBeNull();
});
