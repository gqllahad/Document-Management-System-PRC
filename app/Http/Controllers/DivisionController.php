<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Project;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionController extends Controller
{

    public function admin()
    {
        $user = Auth::user();

        return view('admin.dashboard');
    }

    public function show($name)
    {
        $division = Division::whereRaw('LOWER(name) = ?', [$name])->firstOrFail();

        $user = Auth::user();

        abort_unless(
            $user->role === 'admin' || (int) $user->division_id === (int) $division->id,
            403
        );

        $projects = Project::with('status')
            ->where('division_id', $division->id)
            ->latest()
            ->paginate(10);

        return view("division.{$name}.index", compact('division', 'projects'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Division $division)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Division $division)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        //
    }
}
