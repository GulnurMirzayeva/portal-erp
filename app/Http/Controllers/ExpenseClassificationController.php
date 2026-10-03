<?php

namespace App\Http\Controllers;

use App\Models\ExpenseClassification;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class ExpenseClassificationController extends Controller
{
    /**
     * Təsnifatların siyahısı
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        try {
            $query = ExpenseClassification::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }

            $classifications = $query->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        } catch (\Throwable $e) {
            $classifications = collect();
        }

        return view('expense_classifications.index', [
            'classifications' => $classifications,
            'search' => $search,
        ]);
    }

    /**
     * Yeni təsnifat yaratma səhifəsi
     */
    public function create()
    {
        $nextOrder = 1;
        try {
            $nextOrder = (ExpenseClassification::max('sort_order') ?? 0) + 1;
        } catch (\Throwable $e) {
            $nextOrder = 1;
        }

        return view('expense_classifications.create', [
            'nextOrder' => $nextOrder,
        ]);
    }

    /**
     * Yeni təsnifat əlavə et
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:expense_classifications,name',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Təsnifat adı mütləq daxil edilməlidir.',
            'name.unique' => 'Bu adda təsnifat artıq mövcuddur.',
        ]);

        $maxOrder = 0;
        try {
            $maxOrder = ExpenseClassification::max('sort_order') ?? 0;
        } catch (\Throwable $e) {
            $maxOrder = 0;
        }

        ExpenseClassification::create([
            'name' => trim($validated['name']),
            'sort_order' => $validated['sort_order'] ?? ($maxOrder + 1),
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
        ]);

        return redirect()->route('expense-classifications.index')
            ->with('success', '«' . trim($validated['name']) . '» təsnifatı uğurla əlavə edildi.');
    }

    /**
     * Təsnifatı redaktə etmə səhifəsi
     */
    public function edit($id)
    {
        $classification = ExpenseClassification::findOrFail($id);

        return view('expense_classifications.edit', [
            'classification' => $classification,
        ]);
    }

    /**
     * Təsnifatı yenilə
     */
    public function update(Request $request, $id)
    {
        $classification = ExpenseClassification::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('expense_classifications', 'name')->ignore($classification->id),
            ],
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Təsnifat adı mütləq daxil edilməlidir.',
            'name.unique' => 'Bu adda təsnifat artıq mövcuddur.',
        ]);

        $classification->update([
            'name' => trim($validated['name']),
            'sort_order' => $validated['sort_order'] ?? $classification->sort_order,
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : false,
        ]);

        return redirect()->route('expense-classifications.index')
            ->with('success', '«' . $classification->name . '» təsnifatı uğurla yeniləndi.');
    }

    /**
     * Təsnifatın aktiv/deaktiv statusunu dəyiş
     */
    public function toggleStatus($id)
    {
        $classification = ExpenseClassification::findOrFail($id);
        $classification->is_active = !$classification->is_active;
        $classification->save();

        return response()->json([
            'success' => true,
            'is_active' => $classification->is_active,
            'message' => 'Status uğurla dəyişdirildi.',
        ]);
    }

    /**
     * Təsnifatı sil
     */
    public function destroy($id)
    {
        $classification = ExpenseClassification::findOrFail($id);
        $name = $classification->name;
        $classification->delete();

        return redirect()->route('expense-classifications.index')
            ->with('success', '«' . $name . '» təsnifatı uğurla silindi.');
    }
}
