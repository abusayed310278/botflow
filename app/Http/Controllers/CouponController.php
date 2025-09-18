<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class CouponController extends Controller
{
    /** GET /coupons?code=&min_piece=&max_piece=&per_page=&sort=piece|-piece|amount|-amount|created_at|-created_at */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'code'       => ['sometimes','string','max:255'],
                'min_piece'  => ['sometimes','numeric','min:0'],
                'max_piece'  => ['sometimes','numeric','min:0'],
                'per_page'   => ['sometimes','integer','min:1','max:200'],
                'sort'       => ['sometimes','string','in:piece,-piece,amount,-amount,created_at,-created_at'],
            ]);

            $q = Coupon::query();

            if ($code = $request->query('code')) {
                $q->codeLike($code);
            }

            $min = $request->query('min_piece');
            $max = $request->query('max_piece');
            if ($min !== null && $max !== null) {
                $q->pieceBetween((float)$min, (float)$max);
            } elseif ($min !== null) {
                $q->where('piece', '>=', (float)$min);
            } elseif ($max !== null) {
                $q->where('piece', '<=', (float)$max);
            }

            $sort = $request->query('sort', '-created_at');
            $dir  = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $col  = ltrim($sort, '-');
            $q->orderBy($col, $dir);

            $perPage = (int) $request->query('per_page', 15);
            $p = $q->paginate($perPage);

            return $this->success(
                $p->items(),
                'Coupons fetched',
                200,
                [
                    'pagination' => [
                        'current_page' => $p->currentPage(),
                        'per_page'     => $p->perPage(),
                        'total'        => $p->total(),
                        'last_page'    => $p->lastPage(),
                        'from'         => $p->firstItem(),
                        'to'           => $p->lastItem(),
                    ],
                ]
            );
        } catch (ValidationException $ve) {
            return $this->error('Validation failed', 422, $ve->errors());
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to fetch coupons');
        }
    }

    /** GET /coupons/{id} */
    public function show(int $id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            return $this->success($coupon, 'Coupon fetched');
        } catch (ModelNotFoundException $e) {
            return $this->error('Coupon not found', 404);
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to fetch coupon');
        }
    }

    /** POST /coupons */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'code'   => ['required','string','max:255', Rule::unique('coupons','code')],
                'piece'  => ['required','numeric','min:0'],
                'amount' => ['required','numeric','min:0'],
            ]);

            $coupon = Coupon::create($data);
            return $this->success($coupon, 'Coupon created', 201);
        } catch (ValidationException $ve) {
            return $this->error('Validation failed', 422, $ve->errors());
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to create coupon');
        }
    }

    /** PUT/PATCH /coupons/{id} */
    public function update(Request $request, int $id)
    {
        try {
            $data = $request->validate([
                'code'   => ['sometimes','string','max:255', Rule::unique('coupons','code')->ignore($id)],
                'piece'  => ['sometimes','numeric','min:0'],
                'amount' => ['sometimes','numeric','min:0'],
            ]);

            $coupon = Coupon::findOrFail($id);
            $coupon->update($data);

            return $this->success($coupon->fresh(), 'Coupon updated');
        } catch (ModelNotFoundException $e) {
            return $this->error('Coupon not found', 404);
        } catch (ValidationException $ve) {
            return $this->error('Validation failed', 422, $ve->errors());
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to update coupon');
        }
    }

    /** DELETE /coupons/{id} */
    public function destroy(int $id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->delete();
            return $this->success(null, 'Coupon deleted');
        } catch (ModelNotFoundException $e) {
            return $this->error('Coupon not found', 404);
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to delete coupon');
        }
    }

    /* ---------- Response helpers ---------- */

    private function success(mixed $data = null, string $message = 'OK', int $status = 200, array $meta = [])
    {
        $payload = [
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ];
        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }
        return response()->json($payload, $status);
    }

    private function error(string $message = 'Something went wrong', int $status = 500, mixed $errors = null)
    {
        $payload = [
            'status'  => 'error',
            'message' => $message,
        ];
        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }
        return response()->json($payload, $status);
    }
}
