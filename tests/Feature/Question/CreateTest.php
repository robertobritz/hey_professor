<?php

use App\Models\{Question, User};

use function Pest\Laravel\{actingAs, assertDatabaseCount, assertDatabaseHas, post, postJson};

it('Should be able to create a new question bigger than 255 caracters', function () {
    // Arrange :: preparar
    $user = User::factory()->create();
    actingAs($user); // Vou logar esse meu fake usuário

    // Act :: agir
    $request = post(route('question.store'), [
        'question' => str_repeat('*', 260) . '?',
    ]);

    // Assert :: verificar
    $request->assertRedirect();
    assertDatabaseCount('questions', 1);
    assertDatabaseHas('questions', ['question' => str_repeat('*', 260) . '?']);
});

it('Should create as a draft all the time', function () {
    // Arrange :: preparar
    $user = User::factory()->create();
    actingAs($user); // Vou logar esse meu fake usuário

    // Act :: agir
    $request = post(route('question.store'), [
        'question' => str_repeat('*', 260) . '?',
    ]);

    // Assert :: verificar
    assertDatabaseHas('questions', [
        'question' => str_repeat('*', 260) . '?',
        'draft'    => true]);
});

it('Should check if ends with question mak ?', function () {

    // Arrange :: preparar
    $user = User::factory()->create();
    actingAs($user); // Vou logar esse meu fake usuário

    // Act :: agir
    $request = post(route('question.store'), [
        'question' => str_repeat('*', 10),
    ]);

    // Assert :: verificar
    //$request->assertSessionHasErrors(['question' => 'min']);
    $request->assertSessionHasErrors(['question' => 'Are you sure that is a question? It is missing the question mark in the end.',
    ]);
    assertDatabaseCount('questions', 0);

});

it('Should have at least 10 characters', function () {
    // Arrange :: preparar
    $user = User::factory()->create();
    actingAs($user); // Vou logar esse meu fake usuário

    // Act :: agir
    $request = post(route('question.store'), [
        'question' => str_repeat('*', 8) . '?',
    ]);

    // Assert :: verificar
    //$request->assertSessionHasErrors(['question' => 'min']);
    $request->assertSessionHasErrors(['question' => __('validation.min.string', ['min' => 10, 'attribute' => 'question'])]); // __ é um helper de lang, ai passa todas as chaves relacionadas a ela.
    assertDatabaseCount('questions', 0);

});

test('only authenticated users can create a ne question', function () {

    post(route('question.store'), [
        'question' => str_repeat('*', 8) . '?',
    ])->assertRedirect(route('login'));

});

test('question should be unique', function () {
    $user = User::factory()->create();
    actingAs($user);

    Question::factory()->create(['question' => 'Alguma Pergunta?']);

    post(route('question.store'), [
        'question' => 'Alguma pergunta?',
    ])->assertSessionHasErrors(['question' => 'Pergunta já existe!']);
});
