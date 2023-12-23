<?php

namespace App\Exports;

use App\Enums\IsAdvance;
use App\Libraries\AppLibrary;
use App\Services\ReservationService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReservationExport implements FromCollection, WithHeadings
{

    public ReservationService $reservationService;
    public PaginateRequest $request;

    public function __construct(ReservationService $reservationService, $request)
    {
        $this->reservationService = $reservationService;
        $this->request      = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $orderArray  = [];
        $ordersArray = $this->reservationService->list($this->request);

        foreach ($ordersArray as $reservation) {
            $orderArray[] = [
                $reservation->name,
                $reservation->phone,
                $reservation->email,
                $reservation->no_of_guests,
                AppLibrary::datetime($reservation->reservation_date_time),
            ];
        }
        return collect($orderArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.phone'),
            trans('all.label.email'),
            trans('all.label.no_of_guests'),
            trans('all.label.reservation_date_time')
        ];
    }
}