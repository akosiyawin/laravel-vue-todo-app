<?php


namespace App\Http\Services;



use App\Models\Todo;
use Illuminate\Http\Request;

class TodoService
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ],[
            'title.required' => "There is no title indicated!"
        ]);

        $todo = new Todo();
        $todo->title = $request->get('title');
        $todo->save();
    }

}
