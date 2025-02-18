<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\StoreToolProjectRequest;
use App\Http\Requests\StoreToolRequest;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectApplicant;
use App\Models\ProjectTool;
use App\Models\Tool;
use App\Models\WalletTransaction;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = Auth::user();

        $projectsQuery = Project::with(['category', 'applicants'])->orderByDesc('id');

        if($user->hasRole('project_client')){
            $projectsQuery->whereHas('owner', function ($query) use ($user){
                $query->where('client_id', $user->id);
            });
        }

        $projects = $projectsQuery->paginate(10);

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::all();
        return view('admin.projects.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        
        $user = Auth::user();
        $balance = $user->wallet->balance;

        if ($request->input('budget') > $balance) {
            return redirect()->back()->withErrors(['budget' => 'Balance anda tidak cukup']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'budget' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'about' => 'required|string',
            'skill_level' => 'required|string|in:Beginner,Intermediate,Expert'
        ]);

        DB::transaction(function () use ($request, $user, $validated) {
            $user->wallet->decrement('balance', $request->input('budget'));

            WalletTransaction::create([
                'type' => 'Project Cost',
                'amount' => $request->input('budget'),
                'is_paid' => true,
                'user_id' => $user->id,
            ]);

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            } else {
                $validated['thumbnail'] = ''; // Nilai default jika tidak ada file yang diunggah
            }

            $validated['slug'] = Str::slug($validated['name']);
            $validated['has_finished'] = false;
            $validated['has_started'] = false;
            $validated['client_id'] = $user->id;

            // Pastikan untuk mengecek data yang disimpan
            $newProject = Project::create($validated);
        });

        return redirect()->route('admin.projects.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }

    public function tools(Project $project)
    {
        if($project->client_id != auth()->id()){
            abort(403, 'You are not authorized');
        }
        $tools = Tool::all();
        return view('admin.projects.tools', compact('project', 'tools'));
    }

    public function tools_store(StoreToolProjectRequest $request, Project $project)
    {
        DB::transaction(function() use ($request, $project){
            
            $validated = $request->validated();
            $validated['project_id'] = $project->id;
            
            $toolProject = ProjectTool::firstOrCreate($validated);

        });

        return redirect()->route('admin.project_tools', $project->id);

    }

    public function complete_project_store(ProjectApplicant $projectApplicant)
    {

        DB::transaction(function() use ($projectApplicant){
            
            $validated['type'] = 'Revenue';
            $validated['is_paid'] = true;
            $validated['amount'] = $projectApplicant->project->budget;
            $validated['user_id'] =  $projectApplicant->freelancer_id;

            $addRevenue = WalletTransaction::create($validated);

            $projectApplicant->freelancer->wallet->increment('balance', $projectApplicant->project->budget);

            $projectApplicant->project->update([
                'has_finished' => true,
            ]);

        });

        return redirect()->route('admin.projects.show', [$projectApplicant->project, $projectApplicant->id]);
    }
}
