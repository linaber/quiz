<?php

namespace App\Http\Controllers\Api;

//use App\Filament\Resources\QuestionResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $questions = Question::query()
            ->with('categories')
            ->when($request->category, fn($q) => $q->whereHas('categories', fn($q) => $q->where('id', $request->category)))
            ->paginate(15);

        return QuestionResource::collection($questions);
    }

    public function show(Question $question)
    {
        return new QuestionResource($question->load('categories'));
    }

    public function store(Request $request)
    {
        // Валидация входящих данных
        $validated = $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.text' => 'required|string',
            'answers.*.is_primary' => 'required|boolean',
        ]);

        $primaryCount = collect($validated['answers'])->filter(fn($item) => $item['is_primary'])->count();

        if ($primaryCount === 0) {
            return back()->withErrors(['answers' => 'Необходимо указать один основной вариант ответа.']);
        }

        if ($primaryCount > 1) {
            return back()->withErrors(['answers' => 'Допускается только один основной вариант ответа.']);
        }

        // Логика сохранения
        Question::create($validated);
    }


}
