<?php
use App\Models\Task;
use App\Models\User;

it('can list tasks', function () {
    $response = $this->get(route('tasks.index')); // Using get() instead of getJson()

    $response->assertStatus(200);

});

// it('can create a task', function () {
//     $taskData = Task::factory()->raw();

//     $response = $this->postJson('/api/v1/tasks', $taskData);

//     $response->assertStatus(201);
//     // You can also assert other properties of the response or the created task
// });

// it('can show a task', function () {
//     $task = Task::factory()->create();

//     $response = $this->getJson("/api/v1/tasks/{$task->id}");

//     $response->assertStatus(200)
//              ->assertJsonFragment(['id' => $task->id]);
// });

// it('can update a task', function () {
//     $task = Task::factory()->create();
//     $updatedData = ['name' => 'Updated Task'];

//     $response = $this->putJson("/api/v1/tasks/{$task->id}", $updatedData);

//     $response->assertStatus(200);
//     // You can also assert other properties of the response or the updated task
// });

// it('can delete a task', function () {
//     $task = Task::factory()->create();

//     $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

//     $response->assertStatus(204);
//     $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
// });