<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

class ReferralController extends Controller
{
    /** GET /referrals */
    public function index(Request $request)
    {
        try {
            $query = Referral::query()
                ->forClient($request->integer('client_id'))
                ->status($request->filled('status') ? (int)$request->input('status') : null)
                ->search($request->input('q'));

            // Sorting (default newest first by created_at)
            $sort = $request->input('sort', '-created_at'); 
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $column = ltrim($sort, '-');
            $query->orderBy($column, $direction);

            $referral = $query->paginate($request->integer('per_page', 15))->withQueryString();
            return response()->json([
                'success' => true,
                'message' => 'Referrals retrieved successfully',
                'data' => $referral
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch referrals', 'message' => $e->getMessage()], 500);
        }
    }

    /** POST /referrals */
    public function store(Request $request)
    {
        try {
            $data = $this->validated($request);

            $referral = Referral::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Referral created successfully',
                'data' => $referral->refresh()
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create referral', 'message' => $e->getMessage()], 500);
        }
    }

    /** GET /referrals/{referral}  (implicit binding uses referral_id) */
    public function show($id)
    {
        try {
            $referral = Referral::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Referral retrieved successfully',
                'data' => $referral
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch referral', 'message' => $e->getMessage()], 500);
        }
    }

    /** PUT/PATCH /referrals/{referral} */
    public function update(Request $request, $id)
    {
        try {
            $data = $this->validated($request, $id);
            $referral = Referral::findOrFail($id);

            $referral->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Referral updated successfully',
                'data' => $referral->refresh()
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update referral', 'message' => $e->getMessage()], 500);
        }
    }

    /** DELETE /referrals/{referral} */
    public function destroy($id)
    {
        try {
            $referral = Referral::findOrFail($id);
            $referral->delete();
            return response()->json([
                'success' => true,
                'message' => 'Referral deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete referral', 'message' => $e->getMessage()], 500);
        }
    }

    /** Validation used by store/update */
    protected function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'referral_client_id'            => ['nullable', 'integer'],
            'referral_clicks'               => ['nullable', 'integer', 'min:0'],
            'referral_sign_up'              => ['nullable', 'integer', 'min:0'],

            'referral_totalFunds_byRefered' => ['nullable', 'numeric', 'gte:0'],
            'referral_earned_commision'     => ['nullable', 'numeric', 'gte:0'],
            'referral_requested_commision'  => ['nullable', 'numeric', 'gte:0'],
            'referral_total_commision'      => ['nullable', 'numeric', 'gte:0'],
            'referral_rejected_commision'   => ['nullable', 'numeric', 'gte:0'],

            'referral_status'               => ['nullable', 'integer', Rule::in([0,1])],
            'referral_code'                 => ['nullable', 'string', 'max:64'],
        ]);
    }
}
