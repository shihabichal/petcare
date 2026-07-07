<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function customers()
    {
        $customers = Customer::with('pets', 'transactions')->latest()->get();

        $pdf = Pdf::loadView('pdf.customers', compact('customers'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('data-customer-petcare-' . now()->format('Ymd') . '.pdf');
    }

    public function transactions()
    {
        $transactions = Transaction::with(['customer', 'pet', 'service'])->latest()->get();

        $pdf = Pdf::loadView('pdf.transactions', compact('transactions'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('data-transaksi-petcare-' . now()->format('Ymd') . '.pdf');
    }
}
