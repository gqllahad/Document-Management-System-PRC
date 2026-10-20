<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Division;
use App\Models\Project;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function dashboard()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $divisions = Division::orderByRaw(
            "CASE WHEN name = 'ICTS-OD' THEN 0 ELSE 1 END"
        )->orderBy('name')->get();

        $statuses = Status::orderBy('sort_order')->get();

        $projects = Project::with(['status', 'division', 'latestUpdate'])
            ->latest()
            ->get();

        $stageCounts = Project::selectRaw('status_id, COUNT(*) as total')
            ->groupBy('status_id')
            ->pluck('total', 'status_id');

        $delivered = $stageCounts[$statuses->firstWhere('name', 'Delivered')?->id] ?? 0;

        $recordedProjects = $projects->whereNotNull('contract_number');

        $recordedAbc = $recordedProjects->sum('approved_budget');

        $recordedContractPrice = $recordedProjects
            ->where('contract_price', '>', 0)
            ->sum('contract_price');

        $totalSavings = $recordedProjects
            ->where('contract_price', '>', 0)
            ->sum(fn ($p) => $p->approved_budget - $p->contract_price);

        $stats = [
            'total'          => $projects->count(),
            'delivered'      => $delivered,
            'ongoing'        => $projects->count() - $delivered,
            'abc'            => $projects->sum('approved_budget'),
            'contract_price' => $recordedContractPrice,
            'savings'        => $totalSavings,

            'savings_percentage' => $recordedAbc > 0
                ? ($totalSavings / $recordedAbc) * 100
                : 0,
        ];

        return view('admin.dashboard', compact(
            'divisions',
            'statuses',
            'projects',
            'stageCounts',
            'stats'
        ));
    }

     public function manage_users()
    {
        abort_unless(Auth::check(), 403);

        abort_unless(Auth::user()->role === 'admin', 403);

        $users = User::with('division')
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();

        
        return view('admin.manage_users', compact('users'));
    }


    public function updateRole(Request $request, User $user)
    {
        abort_if($user->role === 'admin', 403);

        $data = $request->validate([
            'role' => 'required|in:head,fund_controller',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update($data);

        return back()->with('success', 'Role updated for ' . $user->name . '.');
    }

}
