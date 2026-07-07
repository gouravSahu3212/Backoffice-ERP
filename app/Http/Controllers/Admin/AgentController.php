<?php

namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAgentRequest;
use App\Http\Requests\Admin\UpdateAgentRequest;
use App\Models\User;
use App\Services\AgentService;
use Illuminate\Http\Request;

class AgentController extends AdminController
{
    public function __construct(
        protected AgentService $service
    ) {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $agents = User::role('Agent')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.agents.index', compact('agents', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.agents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAgentRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.agents.index')
            ->with('success', 'Agent created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $agent)
    {
        abort_unless($agent->hasRole('Agent'), 404);

        return view('admin.agents.edit', compact('agent'));
    }

    /**
     * Update the specified resource in storage (supports JSON for AJAX).
     */
    public function update(UpdateAgentRequest $request, User $agent)
    {
        abort_unless($agent->hasRole('Agent'), 404);

        $agent = $this->service->update($agent, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Agent updated successfully.',
                'agent' => [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'username' => $agent->username,
                    'email' => $agent->email,
                    'phone' => $agent->phone,
                    'is_active' => $agent->is_active,
                ],
            ]);
        }

        return redirect()
            ->route('admin.agents.index')
            ->with('success', 'Agent updated successfully.');
    }

    /**
     * Toggle the agent's active status (supports JSON for AJAX).
     */
    public function toggleStatus(Request $request, User $agent)
    {
        abort_unless($agent->hasRole('Agent'), 404);

        $this->service->toggleStatus($agent);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $agent->fresh()->is_active,
            ]);
        }

        return back()->with('success', 'Agent status updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $agent)
    {
        $this->service->delete($agent);

        return back()->with('success', 'Agent deleted.');
    }
}
