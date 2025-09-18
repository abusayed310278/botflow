<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class ClientController extends Controller
{
    /** GET /api/clients */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->integer('per_page', 20);
            $data = Client::paginate($perPage);
            return response()->json($data);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch clients',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /** GET /api/clients/{id} */
    public function show(int $id): JsonResponse
    {
        try {
            $client = Client::where('client_id', $id)->first();

            if (!$client) {
                return response()->json(['message' => 'Client not found'], 404);
            }

            return response()->json($client);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch client',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /** POST /api/clients */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'email'     => ['required','string','max:225','email','unique:clients,email'],
                'password'  => ['required','string','min:6'],
                'username'  => ['nullable','string','max:225','unique:clients,username'],
                'name'      => ['nullable','string','max:225'],
                'telephone' => ['nullable','string','max:225'],

                'admin_type'    => ['nullable', Rule::in(['1','2'])],
                'balance'       => ['nullable','numeric'],
                'balance_type'  => ['nullable', Rule::in(['1','2'])],
                'debit_limit'   => ['nullable','numeric'],
                'spent'         => ['nullable','numeric'],
                'register_date' => ['nullable','date'],
                'login_date'    => ['nullable','date'],
                'login_ip'      => ['nullable','string','max:225'],
                'tel_type'      => ['nullable', Rule::in(['1','2'])],
                'email_type'    => ['nullable', Rule::in(['1','2'])],
                'client_type'   => ['nullable', Rule::in(['1','2'])],
                'access'        => ['nullable','string'],
                'lang'          => ['nullable','string','max:255'],
                'timezone'      => ['nullable','numeric'],
                'currency_type' => ['nullable', Rule::in(['INR','USD'])],
                'ref_code'      => ['nullable','string'],
                'ref_by'        => ['nullable','string'],
                'change_email'  => ['nullable', Rule::in(['1','2'])],
                'resend_max'    => ['nullable','integer'],
                'currency'      => ['nullable','string','max:225'],
                'passwordreset_token' => ['nullable','string','max:225'],
                'coustm_rate'   => ['nullable','integer'],
                'verified'      => ['nullable','string','max:3'],
            ]);

            // build payload (fill only what is missing to satisfy NOT NULLs)
            $payload = $data + [
                'admin_type'          => '2',
                'balance'             => 0,
                'balance_type'        => '2',
                'debit_limit'         => null,
                'spent'               => 0,
                'register_date'       => now(),
                'login_date'          => null,
                'login_ip'            => null,
                'apikey'              => Str::random(40),
                'tel_type'            => '1',
                'email_type'          => '1',
                'client_type'         => '2',
                'access'              => null,
                'lang'                => 'tr',
                'timezone'            => 0,
                'currency_type'       => 'USD',
                'ref_code'            => Str::random(12),
                'ref_by'              => null,
                'change_email'        => '2',
                'resend_max'          => 0,
                'currency'            => '1',
                'passwordreset_token' => Str::random(40),
                'coustm_rate'         => 0,
                'verified'            => null,
            ];

            // hash password
            $payload['password'] = Hash::make($payload['password']);

            DB::beginTransaction();
            $client = Client::create($payload);
            DB::commit();

            return response()->json($client, 201);

        } catch (ValidationException $ve) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $ve->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create client',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /** PUT/PATCH /api/clients/{id} */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $client = Client::where('client_id', $id)->first();
            if (!$client) {
                return response()->json(['message' => 'Client not found'], 404);
            }

            $data = $request->validate([
                'email'     => ['nullable','string','max:225','email', Rule::unique('clients','email')->ignore($id, 'client_id')],
                'password'  => ['nullable','string','min:6'],
                'username'  => ['nullable','string','max:225', Rule::unique('clients','username')->ignore($id, 'client_id')],
                'name'      => ['nullable','string','max:225'],
                'telephone' => ['nullable','string','max:225'],

                'admin_type'    => ['nullable', Rule::in(['1','2'])],
                'balance'       => ['nullable','numeric'],
                'balance_type'  => ['nullable', Rule::in(['1','2'])],
                'debit_limit'   => ['nullable','numeric'],
                'spent'         => ['nullable','numeric'],
                'register_date' => ['nullable','date'],
                'login_date'    => ['nullable','date'],
                'login_ip'      => ['nullable','string','max:225'],
                'tel_type'      => ['nullable', Rule::in(['1','2'])],
                'email_type'    => ['nullable', Rule::in(['1','2'])],
                'client_type'   => ['nullable', Rule::in(['1','2'])],
                'access'        => ['nullable','string'],
                'lang'          => ['nullable','string','max:255'],
                'timezone'      => ['nullable','numeric'],
                'currency_type' => ['nullable', Rule::in(['INR','USD'])],
                'ref_code'      => ['nullable','string'],
                'ref_by'        => ['nullable','string'],
                'change_email'  => ['nullable', Rule::in(['1','2'])],
                'resend_max'    => ['nullable','integer'],
                'currency'      => ['nullable','string','max:225'],
                'passwordreset_token' => ['nullable','string','max:225'],
                'coustm_rate'   => ['nullable','integer'],
                'verified'      => ['nullable','string','max:3'],
            ]);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            DB::beginTransaction();
            $client->fill($data)->save();
            DB::commit();

            return response()->json($client);

        } catch (ValidationException $ve) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $ve->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update client',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /** DELETE /api/clients/{id} */
    public function destroy(int $id): JsonResponse
    {
        try {
            $client = Client::where('client_id', $id)->first();

            if (!$client) {
                return response()->json(['message' => 'Client not found'], 404);
            }

            DB::beginTransaction();
            $client->delete();
            DB::commit();

            return response()->json(['status' => 'ok']);

        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete client',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
