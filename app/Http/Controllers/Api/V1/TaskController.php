<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResources;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Votre titre d'API",
 *      description="Description de votre API",
 *      @OA\Contact(
 *          email="contact@example.com",
 *          name="Nom du contact"
 *      ),
 *      @OA\License(
 *          name="MIT License",
 *          url="https://opensource.org/licenses/MIT"
 *      )
 * )
 */

    public function index()
    {
        // $this->authorize('viewAny',Task::class);
        return TaskResources::collection(Task::all());
    }

    public function show(Task $task)
    {
        $this->authorize('view', Task::class);

        return new TaskResources($task);
    }

    // public function store(Request $request)
    // {
    //     $data['user_id'] = auth()->id();
    //     return new TaskResources(Task::create($request->all()));
    // }
    
    public function store(Request $request)
    {
        // Vérifiez si l'utilisateur est autorisé à créer une tâche
        // $this->authorize('create', Task::class);
    
        // Vérifiez si l'utilisateur est authentifié
        if (auth()->check()) {
            // L'utilisateur est authentifié, créez la tâche avec son ID
            $data = [
                "user_id" => auth()->id(),
                "name" => $request->input('name'),
                "body" => $request->input('body')
            ];
    
            // Créer la tâche et retourner les ressources de la tâche créée
            return new TaskResources(Task::create($data));
        } else {
            // L'utilisateur n'est pas authentifié, renvoyer une réponse indiquant l'accès refusé
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task); // Cette ligne provoque l'erreur
        return $task->update($request->all());
    }    

    

    public function destroy(Task $task)
    {
        $this->authorize('forceDelete', $task);
        return $task->delete();
    }
}
