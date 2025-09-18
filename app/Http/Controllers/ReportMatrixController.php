<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Throwable;

class ReportMatrixController extends Controller
{
    /* ====================== column helpers ====================== */

    private function pickColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if ($c && Schema::hasColumn($table, $c)) {
                return $c;
            }
        }
        return null;
    }

    private function profitExpr(string $table): string
    {
        // amount-like
        $amount = $this->pickColumn($table, ['amount','total_amount','price','total','charge']);
        $expr   = $amount ?: '0';

        // optional deductions
        foreach ([
            $this->pickColumn($table, ['refund_amount','refund','refunded_amount']),
            $this->pickColumn($table, ['fee_amount','fee','gateway_fee']),
            $this->pickColumn($table, ['cost_amount','cost']),
        ] as $col) {
            if ($col) $expr .= " - IFNULL($col,0)";
        }
        return $expr;
    }

    private function netPaymentExpr(string $table): string
    {
        $amount = $this->pickColumn($table, ['amount','total_amount','price','total','received_amount']);
        $fee    = $this->pickColumn($table, ['fee_amount','fee','gateway_fee']);
        $expr   = $amount ?: '0';
        if ($fee) $expr .= " - IFNULL($fee,0)";
        return $expr;
    }

    /* ====================== responses ====================== */

    private function success(mixed $data = null, string $message = 'OK', int $status = 200, array $meta = [])
    {
        $payload = ['status' => 'success', 'message' => $message, 'data' => $data];
        if (!empty($meta)) { $payload['meta'] = $meta; }
        return response()->json($payload, $status);
    }

    private function error(string $message = 'Something went wrong', int $status = 500, mixed $errors = null)
    {
        $payload = ['status' => 'error', 'message' => $message];
        if (!empty($errors)) { $payload['errors'] = $errors; }
        return response()->json($payload, $status);
    }

    /* ====================== endpoints (NO DATE USE) ====================== */

    /**
     * GET /api/reports/orders-matrix
     * Filters:
     *  - service_ids[]: int[] (optional)
     *  - statuses[]: string[] (optional; Allowed: Waiting for CRON, Failed, Pending, In Progress, Completed, Partial, Canceled, Processing)
     *  - metric: profit|orders (default: profit)
     *
     * Output (no monthly grouping): labels=["all"], series=[total]
     */
    public function ordersMatrix(Request $request)
    {
        try {
            $allowedStatuses = [
                'Waiting for CRON','Failed','Pending','In Progress','Completed','Partial','Canceled','Processing'
            ];

            $data = $request->validate([
                'service_ids'     => ['sometimes','array'],
                'service_ids.*'   => ['integer','min:1'],
                'statuses'        => ['sometimes','array'],
                'statuses.*'      => ['string','max:50', Rule::in($allowedStatuses)],
                'metric'          => ['sometimes','string','in:profit,orders'],
            ]);

            // Resolve columns present in your DB
            $statusCol  = $this->pickColumn('orders', ['status','order_status']);
            $serviceCol = $this->pickColumn('orders', ['service_id','service']);

            $metric = $data['metric'] ?? 'profit';

            $q = DB::table('orders');

            if (!empty($data['statuses']) && $statusCol) {
                $q->whereIn($statusCol, $data['statuses']);
            }
            if (!empty($data['service_ids']) && $serviceCol) {
                $q->whereIn($serviceCol, $data['service_ids']);
            }

            if ($metric === 'orders') {
                $total = (int) $q->count();
            } else {
                // profit = amount - refund - fee - cost (only using columns that exist)
                $row = $q->selectRaw('SUM('.$this->profitExpr('orders').') as v')->first();
                $total = round((float)($row->v ?? 0), 2);
            }

            return $this->success([
                'metric'      => $metric,
                'labels'      => ['all'],
                'series'      => [$total],
                'service_ids' => $data['service_ids'] ?? [],
                'statuses'    => $data['statuses'] ?? [],
                'applied_columns' => [
                    'status'  => $statusCol,
                    'service' => $serviceCol,
                ],
            ], 'Orders total ');
        } catch (ValidationException $ve) {
            return $this->error('Validation failed', 422, $ve->errors());
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to build orders total');
        }
    }

    /** GET /api/reports/orders-counts — same filters; always counts; no date */
    public function ordersCounts(Request $request)
    {
        try {
            $data = $request->validate([
                'service_ids'     => ['sometimes','array'],
                'service_ids.*'   => ['integer','min:1'],
                'statuses'        => ['sometimes','array'],
                'statuses.*'      => ['string','max:50', Rule::in([
                    'Waiting for CRON','Failed','Pending','In Progress','Completed','Partial','Canceled','Processing'
                ])],
            ]);

            $statusCol  = $this->pickColumn('orders', ['status','order_status']);
            $serviceCol = $this->pickColumn('orders', ['service_id','service']);

            $q = DB::table('orders');
            if (!empty($data['statuses']) && $statusCol) {
                $q->whereIn($statusCol, $data['statuses']);
            }
            if (!empty($data['service_ids']) && $serviceCol) {
                $q->whereIn($serviceCol, $data['service_ids']);
            }

            $total = (int) $q->count();

            return $this->success([
                'labels'      => ['all'],
                'series'      => [$total],
                'service_ids' => $data['service_ids'] ?? [],
                'statuses'    => $data['statuses'] ?? [],
                'applied_columns' => [
                    'status'  => $statusCol,
                    'service' => $serviceCol,
                ],
            ], 'Orders count ');
        } catch (ValidationException $ve) {
            return $this->error('Validation failed', 422, $ve->errors());
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to build orders count');
        }
    }

    /**
     * GET /api/reports/payments-matrix
     * Filters:
     *  - payment_ids[]: int[] (optional)
     *
     * Output (no monthly grouping): labels=["all"], series=[net_total]
     * net = amount - fee_amount (using whatever columns exist)
     */
    public function paymentsMatrix(Request $request)
    {
        try {
            $data = $request->validate([
                'payment_ids'    => ['sometimes','array'],
                'payment_ids.*'  => ['integer','min:1'],
            ]);

            $paymentKey = $this->pickColumn('payments', ['payment_id','payment_method_id','method_id','gateway_id']);

            $q = DB::table('payments');

            if (!empty($data['payment_ids']) && $paymentKey) {
                $q->whereIn($paymentKey, $data['payment_ids']);
            }

            $row = $q->selectRaw('SUM('.$this->netPaymentExpr('payments').') as v')->first();
            $total = round((float)($row->v ?? 0), 2);

            return $this->success([
                'labels'      => ['all'],
                'series'      => [$total],
                'payment_ids' => $data['payment_ids'] ?? [],
                'applied_columns' => [
                    'payment_id' => $paymentKey,
                ],
            ], 'Payments total ');
        } catch (ValidationException $ve) {
            return $this->error('Validation failed', 422, $ve->errors());
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to build payments total');
        }
    }
}
