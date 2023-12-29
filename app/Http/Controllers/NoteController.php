<?php

namespace App\Http\Controllers;

use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $note = $user->notes->all();

        return response()->json([
            'notes'=>$note
        ])->setStatusCode(200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        $note = new Note($data);
        $note->user_id = $user->id;
        $note->save();

        return (new NoteResource($note))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): NoteResource
    {
        $user = Auth::user();
        // TODO: rewrite it later with the proper eloquent relationship
        $note = Note::where('id', $id)->where('user_id', $user->id)->first();

        if(!$note){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    'message' => [
                        'Not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new NoteResource($note);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, UpdateNoteRequest $request): NoteResource
    {
        $user = Auth::user();

        $note = Note::where('id', $id)->where('user_id', $user->id)->first();

        if(!$note){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    'message' => [
                        'Not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $note->fill($data);
        $note->save();

        return new NoteResource($note);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $note = Note::where('id', $id)->where('user_id', $user->id)->first();

        if(!$note){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    'message' => [
                        'Not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $note->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
