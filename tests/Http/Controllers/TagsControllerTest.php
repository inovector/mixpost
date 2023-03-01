<?php

use function Pest\Faker\faker;
use Inovector\Mixpost\Models\User;
use Inovector\Mixpost\Models\Tag;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('can store a tag', function () {
    $this->actingAs(test()->user);

    $this->post(route('mixpost.tags.store'), [
        'name' => 'Test',
        'hex_color' => faker()->hexColor
    ])->assertStatus(302);

    expect(Tag::where('name', 'Test')->first() !== null)->toBeTrue();
});

it('can prevent unauthorized users to store a tag', function () {
    $this->postJson(route('mixpost.tags.store'))->assertUnauthorized();
});

it('can show validation on store a tag', function () {
    $this->actingAs(test()->user);

    $response = $this->postJson(route('mixpost.tags.store'));

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'hex_color']);
});

it('can update a tag', function () {
    $tag = Tag::factory()->create();

    $this->actingAs(test()->user);

    $this->put(route('mixpost.tags.update', ['tag' => $tag]), [
        'action' => 'name',
        'name' => 'Test',
    ])->assertStatus(302);

    $tag->refresh();

    expect($tag->name === 'Test')->toBeTrue();
});

it('can prevent unauthorized users to update a tag', function () {
    $this->putJson(route('mixpost.tags.update', ['tag' => 1]))->assertUnauthorized();
});

it('can show validation on update a tag', function () {
    $tag = Tag::factory()->create();

    $this->actingAs(test()->user);

    $response = $this->putJson(route('mixpost.tags.update', ['tag' => $tag]), [
        'action' => 'name'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('can delete a tag', function () {
    $tag = Tag::factory()->create();

    $this->actingAs(test()->user);

    $this->delete(route('mixpost.tags.delete', ['tag' => $tag]))->assertStatus(302);

    $this->assertModelMissing($tag);
});

it('can prevent unauthorized users to delete a tag', function () {
    $this->deleteJson(route('mixpost.tags.delete', ['tag' => 1]))->assertUnauthorized();
});
