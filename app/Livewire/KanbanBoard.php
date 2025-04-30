<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use App\Models\KanbanColumn;
use Illuminate\Support\Facades\Auth;

class KanbanBoard extends Component
{
    public $project;
    public $columns;
    public $tasks = [];
    public $githubConnected;
    public $boardType; // 'general' or 'developer'
    public $viewMode = 'kanban'; // 'kanban' or 'diagram'
    public $githubSyncOptions = ['commits' => true, 'issues' => true];

    protected $listeners = ['updateTaskStatus', 'updateColumns'];

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->githubConnected = $project->github_repo_url !== null;
        $this->boardType = $project->type === 'developer' ? 'developer' : 'general';

        $this->loadGithubSyncOptions();
        $this->ensureDefaultColumns();
        $this->loadColumns();
        $this->loadTasks();
    }

    protected function loadGithubSyncOptions()
    {
        $this->githubSyncOptions = [
            'commits' => $this->project->github_sync_commits,
            'issues' => $this->project->github_sync_issues,
        ];
    }

    protected function ensureDefaultColumns()
    {
        $defaults = [
            ['title' => 'To Do'],
            ['title' => 'In Progress'],
            ['title' => 'Done'],
            ['title' => 'Assigned To']
        ];

        if ($this->boardType === 'developer') {
            $defaults = array_merge($defaults, [
                ['title' => 'Code Review'],
                ['title' => 'QA'],
                ['title' => 'Priority'],
                ['title' => 'Difficulty']
            ]);

            if ($this->githubSyncOptions['commits']) {
                $defaults[] = ['title' => 'Commit ID'];
            }

            if ($this->githubSyncOptions['issues']) {
                $defaults[] = ['title' => 'Issue Link'];
            }
        }

        foreach ($defaults as $index => $column) {
            KanbanColumn::firstOrCreate([
                'project_id' => $this->project->id,
                'title' => $column['title']
            ], [
                'position' => $index
            ]);
        }
    }

    public function loadColumns()
    {
        $this->columns = KanbanColumn::where('project_id', $this->project->id)
            ->orderBy('position')
            ->get();
    }

    public function loadTasks()
    {
        $this->tasks = Task::where('project_id', $this->project->id)
            ->with(['assignedUser', 'comments', 'labels.tags'])
            ->get()
            ->groupBy('status');
    }

    public function updateTaskStatus($taskId, $newStatus)
    {
        $task = Task::findOrFail($taskId);
        $task->status = $newStatus;
        $task->save();

        $this->loadTasks();
    }

    public function updateColumns($columnsData)
    {
        foreach ($columnsData as $index => $data) {
            $column = KanbanColumn::findOrFail($data['id']);
            $column->title = $data['title'];
            $column->position = $index;
            $column->save();
        }

        $this->loadColumns();
    }

    public function addColumn($title)
    {
        KanbanColumn::create([
            'project_id' => $this->project->id,
            'title' => $title,
            'position' => $this->columns->count(),
        ]);

        $this->loadColumns();
    }

    public function switchView($mode)
    {
        $this->viewMode = $mode;
    }

    public function render()
    {
        return view('livewire.kanban-board', [
            'githubConnected' => $this->githubConnected,
            'boardType' => $this->boardType,
            'viewMode' => $this->viewMode,
            'githubSyncOptions' => $this->githubSyncOptions,
        ]);
    }
}