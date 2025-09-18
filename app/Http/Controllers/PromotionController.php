<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PromotionController extends Controller
{
    // Display all promotions
    public function index()
    {
        try {
            $promotions = Promotion::with('user')->get();
            return response()->json([
                'success' => true,
                'message' => 'Promotions fetched successfully',
                'promotions' => $promotions
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch promotions', 'message' => $e->getMessage()], 500);
        }
    }

    // Store new promotion
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'link'    => 'required|string|max:255',
                'status'  => 'required|string',
                'note'    => 'nullable|string',
            ]);

            $promotion = Promotion::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Promotion created successfully', 
                'promotion' => $promotion, 
                201]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create promotion', 'message' => $e->getMessage()], 500);
        }
    }

    // Show specific promotion
    public function show($id)
    {
        try {
            $promotion = Promotion::with('user')->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Promotion fetched successfully', 
                'promotion' =>$promotion
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Promotion not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch promotion', 'message' => $e->getMessage()], 500);
        }
    }

    // Update promotion
    public function update(Request $request, $id)
    {
        try {
            $promotion = Promotion::findOrFail($id);

            $validated = $request->validate([
                'link'    => 'sometimes|string|max:255',
                'status'  => 'sometimes|string',
                'note'    => 'nullable|string',
            ]);

            $promotion->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Promotion updated successfully', 
                'promotion' => $promotion
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Promotion not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update promotion', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete promotion
    public function destroy($id)
    {
        try {
            $promotion = Promotion::findOrFail($id);
            $promotion->delete();

            return response()->json(['message' => 'Promotion deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Promotion not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete promotion', 'message' => $e->getMessage()], 500);
        }
    }
}
