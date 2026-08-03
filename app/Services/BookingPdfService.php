<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingPdfService
{
    protected function loadBooking(Booking $booking)
    {
        return $booking->load(
            'customer',
            'bookingItems.itemable',
            'bookingItems.vendorProfile',
            'bookingItems.menuSet',
            'payments'
        );
    }

    public function download(Booking $booking)
    {
        $this->loadBooking($booking);

        $pdf = Pdf::loadView('pdf.booking-receipt', ['booking' => $booking]);

        return $pdf->download('booking-'.$booking->reference.'-receipt.pdf');
    }

    public function downloadInvoice(Booking $booking)
    {
        $this->loadBooking($booking);

        $pdf = Pdf::loadView('pdf.booking-invoice', ['booking' => $booking]);

        return $pdf->download('invoice-'.$booking->reference.'.pdf');
    }
}
