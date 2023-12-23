<?php

namespace App\Services;

use Exception;
use App\Enums\OrderStatus;
use App\Models\Reservation;
use App\Events\SendReservationMail;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ReservationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use Smartisan\Settings\Facades\Settings;

class ReservationService
{
    public object $reservation;
    protected array $reservationFilter = [
        'name',
        'phone',
        'email',
        'no_of_guests',
        'reservation_date_time'
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_by') ?? 'desc';

            return Reservation::where(function ($query) use ($requests) {
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = Date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = Date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('reservation_date_time', '>=', $first_date)->whereDate(
                        'reservation_date_time',
                        '<=',
                        $last_date
                    );
                }
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->reservationFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
    /**
     * @throws Exception
     */
    public function store(ReservationRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->reservation = Reservation::create(
                    $request->validated()
                );

                SendReservationMail::dispatch([
                    'name' => $this->reservation->name,
                    'phone' => $this->reservation->phone,
                    'email' => $this->reservation->email,
                    'no_of_guests' => $this->reservation->no_of_guests,
                    'reservation_date_time' => $this->reservation->reservation_date_time
                ]);
            });
            return $this->reservation;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Reservation $reservation, $auth = false): Reservation|array
    {
        try {
            if ($auth) {
                if ($reservation->user_id == Auth::user()->id) {
                    return $reservation;
                } else {
                    return [];
                }
            } else {
                return $reservation;
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Reservation $reservation)
    {
        try {
            DB::transaction(function () use ($reservation) {
                $reservation->address()->delete();
                $reservation->coupon()->delete();
                $reservation->orderItems()->delete();
                $reservation->delete();
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception($exception->getMessage(), 422);
        }
    }
}
