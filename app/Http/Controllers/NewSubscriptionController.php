<?php

namespace App\Http\Controllers;

use App\Models\NewSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class NewSubscriptionController extends Controller
{
    public function index()
    {
        try {
            $subs = NewSubscription::with(['category', 'provider'])->latest()->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'New subscriptions retrieved successfully.',
                'data'    => $subs
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch new subscriptions.',
                'error'   => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'service_name'    => ['required','string','max:255'],
                'category_id'     => ['required','exists:categories,id'],
                'servicetype'     => ['required','in:1,2'],
                'service_package' => ['nullable','in:1,2'],
                'service_price'   => ['nullable','string'],
                'service_min'     => ['nullable','numeric'],
                'service_max'     => ['nullable','numeric'],
                'service_speed'   => ['nullable','in:1,2,3,4'],
                'price_type'      => ['nullable','in:normal,special'],
                'api_alert'       => ['nullable','in:1,2'],
                'status'          => ['nullable','in:1,2'],
            ]);

            $sub = NewSubscription::create($data);

            return response()->json([
                'success' => true,
                'message' => 'New subscription created successfully.',
                'data'    => $sub
            ], Response::HTTP_CREATED);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create new subscription.',
                'error'   => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        try {
            $sub = NewSubscription::with(['category','provider'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'New subscription retrieved successfully.',
                'data'    => $sub
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'New subscription not found.',
                'error'   => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $sub = NewSubscription::findOrFail($id);

            $data = $request->validate([
                'service_name'    => ['sometimes','string','max:255'],
                'category_id'     => ['sometimes','exists:categories,id'],
                'servicetype'     => ['sometimes','in:1,2'],
                'service_package' => ['nullable','in:1,2'],
                'service_api'     => ['sometimes','exists:providers,id'],
                'service_price'   => ['nullable','string'],
                'service_min'     => ['sometimes','numeric'],
                'service_max'     => ['sometimes','numeric'],
                'service_speed'   => ['sometimes','in:1,2,3,4'],
                'price_type'      => ['sometimes','in:normal,special'],
                'api_alert'       => ['nullable','in:1,2'],
                'status'          => ['nullable','in:1,2'],
            ]);

            $sub->update($data);

            return response()->json([
                'success' => true,
                'message' => 'New subscription updated successfully.',
                'data'    => $sub->fresh()
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update new subscription.',
                'error'   => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id)
    {
        try {
            $sub = NewSubscription::findOrFail($id);
            $sub->delete();

            return response()->json([
                'success' => true,
                'message' => 'New subscription deleted successfully.'
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete new subscription.',
                'error'   => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
