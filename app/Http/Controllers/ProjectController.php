<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectExpense;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index(Request $request)
    {
        $query = Project::with(['order.customer', 'expenses']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhere('project_number', 'like', "%{$search}%")
                  ->orWhereHas('order.customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $projects = $query->latest()->get();

        // Calculate summary statistics
        $totalProjects = Project::count();
        $activeProjects = Project::whereIn('status', ['started', 'in_progress'])->count();
        $totalProfit = $projects->sum('net_profit');
        $totalDue = $projects->where('payment_status', '!=', 'paid')
                             ->whereIn('status', ['completed', 'delivered'])
                             ->sum('due_amount');

        return view('projects.index', compact(
            'projects', 'totalProjects', 'activeProjects', 'totalProfit', 'totalDue'
        ));
    }

    /**
     * Display the specified project
     */
    public function show(Project $project)
    {
        $project->load([
            'order.customer',
            'order.invoice.payments',
            'expenses',
        ]);

        return view('projects.show', compact('project'));
    }

    /**
     * Update project status
     */
    public function updateStatus(Request $request, Project $project)
    {
        $request->validate([
            'status' => 'required|in:pending,started,in_progress,completed,delivered',
        ]);

        $project->status = $request->status;

        // Auto-set actual_end_date when completed
        if ($request->status === 'completed' && !$project->actual_end_date) {
            $project->actual_end_date = now();
        }

        $project->save();

        return redirect()->back()->with('success', 'Project status updated successfully.');
    }

    /**
     * Add expense to project
     */
    public function storeExpense(Request $request, Project $project)
    {
        $validated = $request->validate([
            'expense_name'  => 'required|string|max:255',
            'category'      => 'required|in:materials,labor,transport,tools,others',
            'amount'        => 'required|numeric|min:0.01|max:999999.99',
            'expense_date'  => 'required|date|before_or_equal:today',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $project->expenses()->create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Expense added successfully.');
    }

    /**
     * Delete expense from project
     */
    public function destroyExpense(Project $project, ProjectExpense $expense)
    {
        // Verify expense belongs to this project
        if ($expense->project_id !== $project->id) {
            return redirect()->back()->with('error', 'Expense not found in this project.');
        }

        $expense->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }

    /**
     * Update advance payment
     */
    public function updateAdvance(Request $request, Project $project)
    {
        $request->validate([
            'advance_received' => 'required|numeric|min:0|max:' . $project->total_revenue,
        ]);

        $project->advance_received = $request->advance_received;

        // Recalculate payment status
        $invoice = $project->order?->invoice;
        $paidAmount = $invoice ? $invoice->paid_amount : 0;
        $totalPaid = $project->advance_received + $paidAmount;

        if ($totalPaid >= $project->total_revenue) {
            $project->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $project->payment_status = 'partial';
        } else {
            $project->payment_status = 'unpaid';
        }

        $project->save();

        return redirect()->back()->with('success', 'Advance payment updated successfully.');
    }
}
