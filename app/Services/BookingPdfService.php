<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingPdfService
{
    public function download(Booking $booking)
    {
        $booking->load('customer', 'bookingItems.itemable', 'bookingItems.vendorProfile', 'payments');

        $pdf = Pdf::loadView('pdf.booking-receipt', ['booking' => $booking]);

        return $pdf->download('booking-'.$booking->id.'-receipt.pdf');
    }

    public function downloadInvoice(Booking $booking)
    {
        $booking->load('customer', 'bookingItems.itemable', 'bookingItems.vendorProfile', 'payments');

        $pdf = Pdf::loadView('pdf.booking-invoice', ['booking' => $booking]);

        return $pdf->download('invoice-'.$booking->id.'.pdf');
    }
}
