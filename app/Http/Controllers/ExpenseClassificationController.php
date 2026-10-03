<?php

namespace App\Http\Controllers;

use App\Models\ExpenseClassification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseClassificationController extends Controller
{
    /**
     * Təsnifatların siyahısı
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = ExpenseClassification::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $classifications = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(30);

        return view('expense_classifications.index', [
            'classifications' => $classifications,
            'search' => $search,
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
        ], [
            'name.required' => 'Təsnifat adı mütləq daxil edilməlidir.',
            'name.unique' => 'Bu adda təsnifat artıq mövcuddur.',
        ]);

        $maxOrder = ExpenseClassification::max('sort_order') ?? 0;

        ExpenseClassification::create([
            'name' => trim($validated['name']),
            'sort_order' => $validated['sort_order'] ?? ($maxOrder + 1),
            'is_active' => true,
        ]);

        return redirect()->route('expense-classifications.index')
            ->with('success', 'Yeni xərc təsnifatı uğurla əlavə edildi.');
    }

    /**
     * Təsnifatı redaktə et
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
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : $classification->is_active,
        ]);

        return redirect()->route('expense-classifications.index')
            ->with('success', 'Xərc təsnifatı uğurla yeniləndi.');
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
        $classification->delete();

        return redirect()->route('expense-classifications.index')
            ->with('success', 'Xərc təsnifatı uğurla silindi.');
    }
}
