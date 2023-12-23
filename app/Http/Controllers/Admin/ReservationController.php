<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Reservation;
use App\Exports\ReservationExport;
use App\Services\ReservationService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\ReservationResource;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\OrderDetailsResource;

class ReservationController extends AdminController
{
    private ReservationService $reservationService;

    public function __construct(ReservationService $reservation)
    {
        parent::__construct();
        $this->reservationService = $reservation;
        $this->middleware(['permission:online-orders'])->only(
            'index',
            'show',
            'export',
            'changeStatus',
            'changePaymentStatus',
            'selectDeliveryBoy'
        );
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ReservationResource::collection($this->reservationService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Reservation $reservation): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->reservationService->show($reservation, false));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ReservationExport($this->reservationService, $request), 'reservation-Order.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ReservationRequest $request) : ReservationResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  new ReservationResource($this->reservationService->store($request));
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
