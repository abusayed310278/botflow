<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
public function index(Request $request)
{
    try {
        $query = Service::query();

        // Check if "category" parameter is present
        if ($request->has('category')) {
            $category = $request->get('category');
            \Log::info('Category Filter: ' . $category); // Log category value for debugging
            
            // Perform a case-insensitive search using LIKE
            $query->whereRaw('LOWER(TRIM(category)) LIKE ?', ['%' . strtolower(trim($category)) . '%']);
        }

        $services = $query->get();

        // Log the actual SQL query
        \Log::info('Executed Query: ' . $query->toSql());

        return response()->json([
            'success' => true,
            'data' => $services
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch services',
            'error' => $e->getMessage()
        ], 500);
    }
}





    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        // ✅ Validate multiple array items
        $validated = $request->validate([
            'services'               => 'required|array|min:1',
            'services.*.service'     => 'nullable|string|max:255',
            'services.*.name'        => 'required|string|max:255',
            'services.*.type'        => 'nullable|string|max:255',
            'services.*.rate'        => 'required|numeric|min:0',
            'services.*.custom_rate' => 'nullable|numeric|min:0',
            'services.*.min'         => 'required|integer|min:1',
            'services.*.max'         => 'required|integer|min:1',
            'services.*.dripfeed'    => 'boolean',
            'services.*.refill'      => 'boolean',
            'services.*.cancel'      => 'boolean',
            'services.*.category'    => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // ✅ Add timestamps if your model uses them
            $now = now();
            $servicesToInsert = array_map(function ($service) use ($now) {
                return array_merge($service, [
                    'created_at' => $now,
                    'updated_at' => $now,
                    //'update_price' => $service['rate'] + $service['max'],
                ]);
            }, $validated['services']);

            // ✅ Bulk insert (one query instead of 10 separate queries)
            Service::insert($servicesToInsert);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($servicesToInsert) . ' services saved successfully',
                'data'    => $servicesToInsert,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save services',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified service.
     */
    public function show($id)
    {
        try {
            $service = Service::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $service
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name'         => 'required|string|max:255',
                'type'         => 'nullable|string|max:255',
                'rate'         => 'required|numeric|min:0',
                'custom_rate'  => 'nullable|numeric|min:0',
                'min'          => 'required|integer|min:1',
                'max'          => 'required|integer|min:1',
                'dripfeed'     => 'boolean',
                'refill'       => 'boolean',
                'cancel'       => 'boolean',
                'category'     => 'nullable|string|max:255',
            ]);

            $validated['update_price'] = $validated['rate'] + $validated['max'];
            
            $service = Service::findOrFail($id);
            $service->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified service.
     */
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


//ok