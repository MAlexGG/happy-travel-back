<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $destinations = Destination::all();
        return response()->json($destinations, 200);
    }

    /**
     * Get all destinations by user
     */
    public function getAllDestinationsByUser()
    {
        $user = Auth::user();
        $destinations = $user->destinations()->get();
        return response()->json($destinations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'location' => 'required|max:255',
        ]);

        $user = Auth::user();

        $destination = Destination::create([
            'title' => $request->title,
            'location' => $request->location,
            'user_id' => $user->id
        ]);

        return response()->json([
            'destination' => $destination,
            'msg' => 'Destino creado correctamente'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $destination = Destination::find($id);
        if($destination) {
            return response()->json($destination, 200);
        } 
        return response()->json(['msg' => 'No existe ese destino']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $destination = Destination::find($id);
        if($destination){
            $destination->delete();
            return response()->json([
                'msg' => 'Destino borrado correctamente'
            ], 200);
        }
        return response()->json(['msg' => 'Destino inexistente']);
    }

    /**
     * User can like a destination.
     */
    public function isFavorite($id)
    {
        $user = Auth::user();
        $destination = Destination::find($id);
        //$destination->isFavorite()->attach($user);
        //para evitar relaciones repetidas usamos syncWithoutDetaching en vez de attach
        $destination->isFavorite()->syncWithoutDetaching($user);

        return response()->json([
            'isFav' => true,
            'msg' => 'Has dado like'
        ], 200);
    }

    public function isNotFavorite($id)
    {
        $user = Auth::user();
        $destination = Destination::find($id);
        $destination->isFavorite()->detach($user);

        return response()->json([
            'isFav' => false,
            'msg' => 'Has dado unlike'
        ], 200);
    }
}
