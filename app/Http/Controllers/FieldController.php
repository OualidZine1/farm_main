<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    /**
     * Display a listing of the fields.
     */
    public function index()
    {
        $fields = Field::orderBy('bloc_number')->get();

        return view('fields.index', compact('fields'));
    }

    /**
     * Show the form for creating a new field.
     */
    public function create()
    {
        return view('fields.create');
    }

    /**
     * Store a newly created field in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bloc_number' => 'required|string|max:255',
            'crop_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        Field::create($request->all());

        return redirect()->route('fields.index')
            ->with('success', 'Field created successfully!');
    }

    /**
     * Show the form for editing the specified field.
     */
    public function edit(Field $field)
    {
        return view('fields.edit', compact('field'));
    }

    /**
     * Update the specified field in storage.
     */
    public function update(Request $request, Field $field)
    {
        $request->validate([
            'bloc_number' => 'required|string|max:255',
            'crop_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $field->update($request->all());

        return redirect()->route('fields.index')
            ->with('success', 'Field updated successfully!');
    }

    /**
     * Remove the specified field from storage.
     */
    public function destroy(Field $field)
    {
        // Check if the field is used in any transactions
        $transactionCount = InventoryTransaction::where('field_id', $field->id)->count();

        if ($transactionCount > 0) {
            return redirect()->route('fields.index')
                ->with('error', 'Cannot delete field because it is used in transactions.');
        }

        $field->delete();

        return redirect()->route('fields.index')
            ->with('success', 'Field deleted successfully!');
    }
}
