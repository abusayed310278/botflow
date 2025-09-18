<?php

namespace App\Http\Controllers;

use App\Models\AdditionalsUpdate;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AdditionalsUpdateController extends Controller
{
    // List all updates
    public function index()
    {
        try {
            $updates = AdditionalsUpdate::with('service')->get();
            return response()->json([
                'success' => true,
                'message' => 'Updates fetched successfully', 
                'updates' => $updates
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch updates', 'message' => $e->getMessage()], 500);
        }
    }

    // Store new update
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'service_id'  => 'required|exists:services,service',
                'status'      => 'nullable|string',
                'description' => 'nullable|string',
                'date'        => 'nullable|date',
            ]);

            $update = AdditionalsUpdate::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Update created successfully',
                'update' => $update,
             201]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create update', 'message' => $e->getMessage()], 500);
        }
    }

    // Show one update
    public function show($id)
    {
        try {
            $update = AdditionalsUpdate::with('service')->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Update fetched successfully',
                'update' => $update
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Update not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch update', 'message' => $e->getMessage()], 500);
        }
    }

    // Update existing record
    public function update(Request $request, $id)
    {
        try {
            $update = AdditionalsUpdate::findOrFail($id);
            // dd($request->all());
            $validated = $request->validate([
                'status'      => 'sometimes|string',
                'description' => 'nullable|string',
                'date'        => 'sometimes|date',
            ]);

            $update->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Update updated successfully',
                'update' => $update
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Update not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete record
    public function destroy($id)
    {
        try {
            $update = AdditionalsUpdate::findOrFail($id);
            $update->delete();

            return response()->json([
                'success' => true,
                'message' => 'Update deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Update not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete', 'message' => $e->getMessage()], 500);
        }
    }
}
