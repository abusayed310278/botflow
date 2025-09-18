<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class TicketController extends Controller
{
    /** GET /tickets?status=&client_id=&per_page= */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'status'    => ['sometimes','string','max:50'],
                'client_id' => ['sometimes','integer','min:1'],
                'per_page'  => ['sometimes','integer','min:1','max:200'],
            ]);

            $query = Ticket::query();

            if ($status = $request->query('status')) {
                $query->status($status);
            }

            if ($clientId = $request->query('client_id')) {
                $query->forClient((int) $clientId);
            }

            $perPage = (int) $request->query('per_page', 15);
            $p = $query->latest()->paginate($perPage);

            return $this->success(
                $p->items(),
                'Tickets fetched',
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
            return $this->error('Failed to fetch tickets');
        }
    }

    /** GET /tickets/{id} */
    public function show(int $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            return $this->success($ticket, 'Ticket fetched');
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to fetch ticket');
        }
    }

    /** POST /tickets */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'client_id'   => ['required','integer','min:1'],
                'subject'     => ['required','string','max:225'],
                'status'      => ['sometimes','string','max:50'],
                'client_new'  => ['sometimes','in:1,2'],
                'support_new' => ['sometimes','in:1,2'],
                'canmessage'  => ['sometimes','in:1,2'],
            ]);

            $ticket = Ticket::create($data);
            return $this->success($ticket, 'Ticket created', 201);
        } catch (ValidationException $ve) {
            return $this->error('Validation failed', 422, $ve->errors());
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to create ticket');
        }
    }

    /** PUT/PATCH /tickets/{id} */
    public function update(Request $request, int $id)
    {
        try {
            $data = $request->validate([
                'client_id'   => ['sometimes','integer','min:1'],
                'subject'     => ['sometimes','string','max:225'],
                'status'      => ['sometimes','string','max:50'],
                'client_new'  => ['sometimes','in:1,2'],
                'support_new' => ['sometimes','in:1,2'],
                'canmessage'  => ['sometimes','in:1,2'],
            ]);

            $ticket = Ticket::findOrFail($id);
            $ticket->update($data);

            return $this->success($ticket->fresh(), 'Ticket updated');
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        } catch (ValidationException $ve) {
            return $this->error('Validation failed', 422, $ve->errors());
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to update ticket');
        }
    }

    /** DELETE /tickets/{id} */
    public function destroy(int $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();
            return $this->success(null, 'Ticket deleted');
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        } catch (Throwable $e) {
            report($e);
            return $this->error('Failed to delete ticket');
        }
    }

    /** GET /tickets/status/{status}?per_page= */
    public function byStatus(string $status, Request $request)
    {
        try {
            $request->validate([
                'per_page' => ['sometimes','integer','min:1','max:200'],
            ]);

            $perPage = (int) $request->query('per_page', 15);
            $p = Ticket::status($status)->latest()->paginate($perPage);

            return $this->success(
                $p->items(),
                "Tickets with status '{$status}' fetched",
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
            return $this->error('Failed to filter tickets');
        }
    }

    /* -----------------------
     * Unified response helpers
     * ----------------------*/
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
