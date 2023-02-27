<?php

namespace App\Http\Controllers\Api;

use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class QuizController extends Controller
{



    /*
     |--------------------------------------------------------------------------
     |       INDEX METHOD
     |--------------------------------------------------------------------------
    */
    public function index(){
        try {
            $quiz = Quiz::status()->with('question.question_option')->get();

            return response()->json([
                'success'   => true,
                'error'     => null,
                'data'      => $quiz,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
            ]);
        }
    }

    /*
     |--------------------------------------------------------------------------
     |       STORE METHOD
     |--------------------------------------------------------------------------
    */
    public function store(Request $request){
        try {

            DB::transaction(function () use($request){

                $quiz = $this->storeOrUpdateQuiz($request);

                $this->storeQuestion($request, $quiz->id);
            });

            return response()->json([
                'success'   => true,
                'error'     => null,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
            ]);
        }
    }

    /*
     |--------------------------------------------------------------------------
     |       SHOW METHOD
     |--------------------------------------------------------------------------
    */
    public function show($id){
        try {

            $quiz = Quiz::with('question.question_option')->findOrFail($id);

            return response()->json([
                'success'   => true,
                'error'     => null,
                'data'      => $quiz,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
            ]);
        }
    }





    /*
     |--------------------------------------------------------------------------
     |       UPDTAE METHOD
     |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id){
        try {
            DB::transaction(function () use($request, $id){

                $quiz = $this->storeOrUpdateQuiz($request, $id);


                foreach ($quiz->question ?? [] as $key => $value) {
                    QuestionOption::where('question_id', $value->id)->delete();
                    $value->delete();
                }

                $this->storeQuestion($request, $quiz->id);
            });
            return response()->json([
                'success'   => true,
                'error'     => null,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
            ]);
        }
    }

    /*
     |--------------------------------------------------------------------------
     |       DESTROY METHOD
     |--------------------------------------------------------------------------
    */
    public function destroy($id){
        try {
            DB::transaction(function () use($id){

                $quiz = Quiz::findOrFail($id);

                foreach ($quiz->question ?? [] as $key => $value) {
                    QuestionOption::where('question_id', $value->id)->delete();
                    $value->delete();
                }

                $quiz->delete();
            });

            return response()->json([
                'success'   => true,
                'error'     => null,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
            ]);
        }
    }








    public function storeOrUpdateQuiz($request, $id = null){
        $quiz = Quiz::updateOrCreate([
            'id' => $id,
        ],[
            'title'         => $request->title,
            'description'   => $request->description,
            'status'        => $request->status ?? 1
        ]);
        return $quiz;
    }

    public function storeQuestion($request, $quiz_id){
        foreach ($request->question_title as $key => $q_title) {
            //  Insert Question
            $question = Question::create([
                'quiz_id'       => $quiz_id,
                'title'         => $q_title,
                'right_ans'     => $request->right_ans[$key],
                'is_mandatory'  => $request->is_mandatory[$key],
            ]);

            //  Insert Question Options
            foreach ($request->option[$key] as $option_title) {
                $q_option = QuestionOption::create([
                    'question_id'       => $question->id,
                    'title'             => $option_title,
                ]);
            }
        }

    }

}
