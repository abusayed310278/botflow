<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Throwable;

class ReportMatrixController extends Controller
{
    /** GET /api/reports/orders-matrix
     *  Params:
     *   - year (required, int)
     *   - service_ids[] (optional, int[]) multi-select
     *   - statuses[] (optional, string[]) multi-select
     *   - metric = profit|orders (default: profit)
     */
    public function ordersMatrix(Request $request)
    {
        try {
            $allowedStatuses = [
                'Waiting for CRON','Failed','Pending','In Progress','Completed','Partial','Canceled','Processing'
            ];

            $data = $request->validate([
                'year'           => ['required','integer','min:2000','max:2100'],
                'service_ids'    => ['sometimes','array'],
                'service_ids.*'  => ['integer','min:1'],
                'statuses'       => ['sometimes','array'],
                'statuses.*'     => ['string','max:50', Rule::in($allowedStatuses)],
                'metric'         => ['sometimes','string','in:profit,orders'],
            ]);

            $metric = $data['metric'] ?? 'profit';
            $year   = (int) $data['year'];

            $q = DB::table('orders')
                ->selectRaw('MONTH(order_create) as m')
                ->when(!empty($data['statuses']),    fn($qq) => $qq->whereIn('status', $data['statuses']))
                ->when(!empty($data['service_ids']), fn($qq) => $qq->whereIn('service_id', $data['service_ids']))
                ->whereYear('order_create', $year);

            if ($metric === 'orders') {
                $q->selectRaw('COUNT(*) as v');
            } else {
                // profit = amount - refund - fee - cost
                $q->selectRaw('SUM(amount - IFNULL(refund_amount,0) - IFNULL(fee_amount,0) - IFNULL(cost_amount,0)) as v');
            }

            $rows = $q->groupBy('m')->get();

            $series = array_fill(1, 12, 0.00);
            foreach ($rows as $r) { $series[(int)$r->m] = round((float)$r->v, 2); }

            return $this->success([
                'year'        => $year,
                'metric'      => $metric,
                'service_ids' => $data['service_ids'] ?? [],
                'statuses'    => $data['statuses'] ?? [],
                'series'      => $series, // months 1..12
            ], 'Orders matrix');
        } catch (ValidationException $ve) {
            return $this->error('Validation failed', 422, $ve->errors());
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to build orders matrix');
        }
    }

    /** Alias for Number of Orders (always counts) */
    public function ordersCounts(Request $request)
    {
        $request->merge(['metric' => 'orders']);
        return $this->ordersMatrix($request);
    }

    /** GET /api/reports/payments-matrix
     *  Params:
     *   - year (required, int)
     *   - payment_ids[] (optional, int[]) multi-select
     *   - metric (optional) currently only 'earnings' (net = amount - fee_amount)
     */
    public function paymentsMatrix(Request $request)
    {
        try {
            $data = $request->validate([
                'year'          => ['required','integer','min:2000','max:2100'],
                'payment_ids'   => ['sometimes','array'],
                'payment_ids.*' => ['integer','min:1'],
                'metric'        => ['sometimes','string','in:earnings'],
            ]);

            $year = (int) $data['year'];

            $q = DB::table('payments')
                ->selectRaw('MONTH(created_at) as m')
                ->selectRaw('SUM(amount - IFNULL(fee_amount,0)) as v')
                ->when(!empty($data['payment_ids']), fn($qq) => $qq->whereIn('payment_id', $data['payment_ids']))
                ->whereYear('created_at', $year)
                ->groupBy('m');

            $rows = $q->get();

            $series = array_fill(1, 12, 0.00);
            foreach ($rows as $r) { $series[(int)$r->m] = round((float)$r->v, 2); }

            return $this->success([
                'year'        => $year,
                'metric'      => 'earnings',
                'payment_ids' => $data['payment_ids'] ?? [],
                'series'      => $series,
            ], 'Payments matrix');
        } catch (ValidationException $ve) {
            return $this->error('Validation failed', 422, $ve->errors());
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to build payments matrix');
        }
    }

    /* ---- response helpers ---- */
    private function success(mixed $data = null, string $message = 'OK', int $status = 200, array $meta = [])
    {
        $payload = ['status'=>'success','message'=>$message,'data'=>$data];
        if ($meta) $payload['meta'] = $meta;
        return response()->json($payload, $status);
    }
    private function error(string $message = 'Something went wrong', int $status = 500, mixed $errors = null)
    {
        $payload = ['status'=>'error','message'=>$message];
        if ($errors) $payload['errors'] = $errors;
        return response()->json($payload, $status);
    }
}
