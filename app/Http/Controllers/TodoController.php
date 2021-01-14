<?php

namespace App\Http\Controllers;

use App\Http\Services\TodoService;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * @return Todo[]|Collection
     */
    public function index()
    {
        return Todo::latest()->get();
    }

    public function store(Request $request,TodoService $service)
    {
        $service->store($request);
        return $this->index();
    }

    public function update(Request $request,Todo $todo)
    {
        if(!is_null($request->get('status'))){
            $todo->update([
                'is_completed' => $request->status
            ]);
        }else{
            $request->validate(['title'=>['required','max:225']]);

            $todo->update([
                'title' => $request->title
            ]);
        }

        return response(['message'=>'Update success!']);
    }

    public function destroy(Todo $todo)
    {
        try {
            $todo->delete();
            return response(['message' => 'Delete success!']);
        } catch (\Exception $e) {
            return response(['message' => 'Delete failed!'],400);
        }
    }

}
