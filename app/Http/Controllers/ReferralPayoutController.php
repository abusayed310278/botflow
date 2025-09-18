<?php

namespace App\Http\Controllers;

use App\Models\ReferralPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Exception;

class ReferralPayoutController extends Controller
{
    /** GET /referrals-payouts?status=&q=&client_id=&per_page= */
   public function index(Request $request)
    {
        try {
            $q = ReferralPayout::with('client:client_id,username');

            if ($request->filled('client_id')) {
                $q->where('client_id', (int) $request->client_id);
            }

            if ($request->filled('status')) {
                $q->where('status', (string) $request->status);
            }

            if ($request->filled('q')) {
                $term = $request->q;
                $q->where(function ($w) use ($term) {
                    $w->where('code', 'like', "%{$term}%")
                    ->orWhereHas('client', fn($c) => $c->where('username', 'like', "%{$term}%"));
                });
            }

            // default sort
            $sort = $request->input('sort', '-created_at');
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $column = ltrim($sort, '-');
            $q->orderBy($column, $direction);

            $referralPayouts = $q->paginate($request->integer('per_page', 15))->withQueryString();
            return response()->json([
                'success' => true,
                'data' => $referralPayouts,
                'message' => 'Payouts retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch payouts',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /** POST /referrals-payouts */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'client_id'        => ['nullable', 'integer'],
                'amount_requested' => ['required', 'numeric', 'gte:0'],
                'status'           => ['nullable', 'string'],
                'code'             => ['nullable', 'string', 'max:32', 'unique:referrals_payouts,code'],
            ]);

            // auto-generate code if not provided
            $data['code'] = $data['code'] ?? ('PO-' . Str::upper(Str::random(10)));

            $payout = ReferralPayout::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Payout created successfully',
                'data'    => $payout->load('client:client_id,username')
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create payout', 'message' => $e->getMessage()], 500);
        }
    }

    /** GET /referrals-payouts/{id} */
    public function show(int $id)
    {
        try {
            $payout = ReferralPayout::with('client:client_id,username')->find($id);
            if (!$payout) return response()->json(['error' => 'Payout not found'], 404);
            return response()->json([
                'success' => true,
                'message' => 'Payout retrieved successfully',
                'data'    => $payout
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch payout', 'message' => $e->getMessage()], 500);
        }
    }

    /** PUT/PATCH /referrals-payouts/{id} */
    public function update(Request $request, int $id)
    {
        try {
            $payout = ReferralPayout::find($id);
            if (!$payout) return response()->json(['error' => 'Payout not found'], 404);

            $data = $request->validate([
                'client_id'        => ['nullable', 'integer'],
                'amount_requested' => ['sometimes', 'numeric', 'gte:0'],
                'status'           => ['nullable', 'string'],
                'code'             => ['sometimes', 'string', 'max:32', 'unique:referrals_payouts,code,' . $payout->id],
            ]);

            $payout->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Payout updated successfully',
                'data'    => $payout->load('client:client_id,username')
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update payout', 'message' => $e->getMessage()], 500);
        }
    }

    /** DELETE /referrals-payouts/{id} */
    public function destroy(int $id)
    {
        try {
            $payout = ReferralPayout::find($id);
            if (!$payout) return response()->json(['error' => 'Payout not found'], 404);

            $payout->delete();
            return response()->json([
                'success' => true,
                'message' => 'Payout deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete payout', 'message' => $e->getMessage()], 500);
        }
    }
}
