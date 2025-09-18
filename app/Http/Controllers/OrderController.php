<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{

    /** List with common filters & pagination */
   public function index(Request $request)
    {
        // dd('ok');
        $q = Order::query()->with(['service','dripfeed','country', 'newSubscription']);

        // Quick filters
        if ($request->filled('order_status')) {
            $q->where('order_status', (string)$request->input('order_status'));
        }
        if($request->filled('services')){
            $q->where('services', (string)$request->input('services'));
        }
        if ($request->filled('client_id')) {
            $q->where('client_id', (int)$request->input('client_id'));
        }
        if ($request->filled('service_id')) {
            $q->where('service_id', (int)$request->input('service_id'));
        }
        if ($request->filled('dripfeed')) {
            $q->where('dripfeed', (int)$request->input('dripfeed'));
        }
        if ($request->filled('dripfeed_status')) {
            $q->where('dripfeed_status', (string)$request->input('dripfeed_status'));
        }
        if ($request->filled('subscriptions_type')) {
            $q->where('subscriptions_type', (int)$request->input('subscriptions_type'));
        }
        if ($request->filled('subscriptions_status')) {
            $q->where('subscriptions_status', (string)$request->input('subscriptions_status'));
        }

        // Search (order_id or order_url)
        if ($request->filled('search_type') && $request->filled('search')) {
            $term = (string) $request->input('search');
            if ($request->input('search_type') === 'order_id' && is_numeric($term)) {
                $q->where('id', (int) $term);
            } elseif ($request->input('search_type') === 'order_url') {
                $q->where('order_url', 'like', "%{$term}%");
            }
        }

        $orders = $q->orderByDesc('id')
            ->paginate($request->input('per_page', 25))
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
        ]);
        // $orders = Order::with(['service','dripfeed','country', 'newSubscription'])->paginate(10);
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Orders retrieved successfully',
        //     'data' => $orders,
        // ]);
    }

    public function show($id)
    {
        $order = Order::with(['service','dripfeed','country', 'newSubscription'])->find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'data' => $order,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $order = Order::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $this->validateData($request, $id);

        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
        $order->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order,
        ]);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }

    /** Centralized validation reflecting your migration */
    private function validateData(Request $request, ?int $orderId = null): array
    {
        return $request->validate([
            'client_id'   => ['nullable','integer'],
            'service_id'  => ['nullable','integer'],

            'order_url'   => ['required','string'],

            'order_api'   => ['nullable','integer'],
            'order_status'=> ['nullable','string','max:32'],
            'order_error' => ['nullable','string','max:255'],
            'order_detail'=> ['nullable','string'],

            'order_quantity' => ['nullable','integer','min:0'],
            'order_charge'   => ['nullable','numeric'],
            'order_extra'    => ['nullable','numeric'],

            'order_start'    => ['nullable','integer','min:0'],
            'order_remains'  => ['nullable','integer','min:0'],
            'last_check'     => ['nullable','date'],

            'api_charge'         => ['nullable','numeric'],
            'api_currencycharge' => ['nullable','numeric'],
            'api_orderid'        => ['nullable','string','max:191'],
            'api_serviceid'      => ['nullable','integer'],

            'order_profit'       => ['nullable','numeric'],

            'dripfeed'               => ['nullable','integer'],
            'dripfeed_id'            => ['nullable','integer'],
            'dripfeed_status'        => ['nullable','string','max:32'],
            'dripfeed_totalcharges'  => ['nullable','string','max:32'],
            'dripfeed_runs'          => ['nullable','string','max:32'],
            'dripfeed_delivery'      => ['nullable','string','max:32'],
            'dripfeed_interval'      => ['nullable','string','max:32'],
            'dripfeed_totalquantity' => ['nullable','string','max:32'],

            'subscriptions_type'     => ['nullable','integer'],
            'subscriptions_id'       => ['nullable','integer'],
            'subscriptions_status'   => ['nullable','string','max:32'],
            'subscriptions_username' => ['nullable','string'],
            'subscriptions_post'     => ['nullable','string'],
            'subscriptions_delivery' => ['nullable','string'],
            'subscriptions_delay'    => ['nullable','string'],
            'subscriptions_min'      => ['nullable','string'],
            'subscriptions_max'      => ['nullable','string'],
            'subscriptions_expiry'   => ['nullable','string'],

            'country_id'             => ['nullable','integer'],

            'refill'                 => ['nullable','integer'],
        ]);
    }
}
