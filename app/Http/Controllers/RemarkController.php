<?php

namespace App\Http\Controllers;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class RemarkController extends Controller
{
    private function guard(Project $project) {
        $u = Auth::user();
        abort_unless($u->role === 'admin' || $u->division_id === $project->division_id, 403);
    }

    private function shape($r) {
        return [
            'id'    => $r->id,
            'body'  => $r->body,
            'user'  => $r->user->name,
            'role'  => $r->user->role,
            'admin' => $r->user->role === 'admin',
            'at'    => $r->created_at->format('M d, g:i A'),
        ];
    }

    public function index(Project $project) {
        $this->guard($project);
        // return $project->remarks()->with('user')->oldest()->get()->map(fn ($r) => $this->shape($r));
        return $project->remarkMessages()->with('user')->oldest()->get()->map(fn ($r) => $this->shape($r));
    }

    public function store(Request $request, Project $project) {
        $this->guard($project);
        $data = $request->validate(['body' => 'required|string|max:1000']);
        $r = $project->remarkMessages()->create(['user_id' => Auth::id(), 'body' => $data['body']])->load('user');
        return $this->shape($r);
    }
}
