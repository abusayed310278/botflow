<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Exception;

class PaymentController extends Controller
{
    /**
     * GET /payments
     */
    public function index(Request $request)
    {
        try {
            $query = Payment::query()
                ->forClient($request->integer('client_id'))
                ->status($request->filled('status') ? (int)$request->input('status') : null)
                ->search($request->input('q'));

            if ($request->filled('from')) {
                $query->where('payment_create_date', '>=', Carbon::parse($request->input('from'))->startOfDay());
            }
            if ($request->filled('to')) {
                $query->where('payment_create_date', '<=', Carbon::parse($request->input('to'))->endOfDay());
            }

            $sort = $request->input('sort', '-payment_create_date');
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $column = ltrim($sort, '-');
            $query->orderBy($column, $direction);

            $payments = $query->paginate($request->integer('per_page', 15))->withQueryString();
            return response()->json([
                'success' => true,
                'message' => 'Payments retrieved successfully',
                'data' => $payments
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch payments', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /payments
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validated($request);

            if (empty($data['payment_create_date'])) {
                $data['payment_create_date'] = now();
            }

            $payment = Payment::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Payment created successfully',
                'data' =>$payment, 
                201]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create payment', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /payments/{payment}
     */
   public function show(int $id)
    {
        try {
            $payment = Payment::find($id);
            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Payment retrieved successfully',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch payment', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * PUT/PATCH /payments/{payment}
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $this->validated($request, $id);

            if (!isset($data['payment_update_date'])) {
                $data['payment_update_date'] = now();
            }
            $payment = Payment::findOrFail($id);

            $payment->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data' =>$payment
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update payment', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * DELETE /payments/{payment}
     */
    public function destroy($id)
    {
        try {
            $payment = Payment::find($id);
            $payment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete payment', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Validation rules shared across store/update.
     */
    protected function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'client_id'           => ['nullable', 'integer'],
            'client_balance'      => ['nullable', 'numeric'],
            'payment_amount'      => ['required', 'numeric'],
            'payment_privatecode' => ['nullable', 'string', 'max:191'],
            'payment_method'      => ['nullable', 'integer'],
            'payment_status'      => ['nullable', 'integer', Rule::in([0,1])],
            'payment_delivery'    => ['nullable', 'integer', Rule::in([0,1])],
            'payment_note'        => ['nullable', 'string', 'max:191'],
            'payment_mode'        => ['nullable', 'string', 'max:191'],
            'payment_create_date' => ['nullable', 'date'],
            'payment_update_date' => ['nullable', 'date'],
            'payment_ip'          => ['nullable', 'ip'],
            'payment_extra'       => ['nullable', 'string', 'max:191'],
            'payment_bank'        => ['nullable', 'string', 'max:64'],
        ]);
    }
}
