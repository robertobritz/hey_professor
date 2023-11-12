<?php

use App\Models\{Question, User};

use function Pest\Laravel\{actingAs, get};

it('should be able to open a question to edit', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->for($user, 'createdBy')->create(['draft' => true]);

    actingAs($user);

    get(route('question.edit', $question))
        ->assertSuccessful();
});

it('should return a view', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->for($user, 'createdBy')->create(['draft' => true]);

    actingAs($user);

    get(route('question.edit', $question))
        ->assertViewIs('question.edit');
});

it('shoul make sure that only question with status DRAFT can be edited', function () {
    $user             = User::factory()->create();
    $questionNotDraft = Question::factory()->for($user, 'createdBY')->create(['draft' => false]);
    $draftQUestion    = Question::factory()->for($user, 'createdBY')->create(['draft' => true]);

    actingAs($user);

    get(route('question.edit', $questionNotDraft))->assertForbidden();
    get(route('question.edit', $draftQUestion))->assertSuccessful();

});
