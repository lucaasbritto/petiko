<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User\User;
use App\Models\Task\Task;


class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task_with_valid_data(){
        $user = User::factory()->create();

        $data = [
            'title' => 'Teste de tarefa',
            'description' => 'Descrição da tarefa',
            'due_date' => now()->addDay()->toDateString(),
            'user_id' => $user->id,
        ];

        $this->actingAs($user);

        $response = $this->postJson('/api/task', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Teste de tarefa',
            'user_id' => $user->id,
        ]);
    }

    public function test_create_task_fails_with_missing_title(){
        $user = User::factory()->create();

        $data = [
            'description' => 'Descrição sem título',
            'due_date' => now()->addDay()->toDateString(),
            'user_id' => $user->id,
        ];

        $this->actingAs($user);

        $response = $this->postJson('/api/task', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    public function test_user_can_update_task(){
        $user = User::factory()->create();

        $token = auth('api')->login($user);

        $task = Task::factory()->create(['user_id' => $user->id]);

        $data = [
            'title' => 'Título atualizado',
            'description' => 'Descrição atualizada',
            'due_date' => now()->addDays(2)->format('Y-m-d'),
            'is_done' => false,
            'user_id' => $user->id,
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->putJson("/api/task/{$task->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Título atualizado']);
    }

    public function test_user_cannot_update_other_users_task(){
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user2->id]);

        $token = auth('api')->login($user1);

        $data = [
            'title' => 'Tentativa não autorizada',
            'description' => 'Teste de permissão',
            'due_date' => now()->addDays(5)->toDateString(),
            'is_done' => false,
            'user_id' => $user2->id,
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->putJson("/api/task/{$task->id}", $data);

        $response->assertStatus(403);
    }


    public function test_user_can_delete_own_task(){
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $token = auth('api')->login($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->deleteJson("/api/task/{$task->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);    
    }

    public function test_user_cannot_delete_others_task(){
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user2->id]);

        $token = auth('api')->login($user1);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->deleteJson("/api/task/{$task->id}");

        $response->assertStatus(403);
    }


    public function test_user_can_list_own_tasks(){
        $user = User::factory()->create();

        Task::factory()->count(3)->create(['user_id' => $user->id]);
        Task::factory()->count(2)->create();

        $token = auth('api')->login($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/task');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }
}
