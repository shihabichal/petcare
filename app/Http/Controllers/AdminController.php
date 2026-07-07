<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\Transaction;
use App\Models\Service;
use Carbon\Carbon;

class AdminController extends Controller
{
    // ==================== DASHBOARD ====================
    public function dashboard()
    {
        $stats = [
            'pending'     => Transaction::where('status', 'pending')->count(),
            'in_progress' => Transaction::where('status', 'ongoing')->count(),
            'unpaid'      => Transaction::where('payment_status', 'unpaid')->count(),
        ];
        $transactions = Transaction::with(['customer', 'pet', 'service'])->latest()->paginate(10);
        $customers    = Customer::with('pets')->get();
        $services     = Service::where('is_active', true)->get();

        return view('admin.dashboard', compact('transactions', 'customers', 'services', 'stats'));
    }

    // ==================== CUSTOMER MANAGEMENT ====================
    public function customers(Request $request)
    {
        $search    = $request->get('search');
        $customers = Customer::with('pets')
            ->withCount('transactions')
            ->when($search, fn($q) => $q->where('name', 'ilike', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%")
                ->orWhere('customer_code', 'ilike', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'search'));
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address'      => 'nullable|string',
            'pet_name'     => 'required|string|max:255',
            'pet_type'     => 'required|string|max:100',
            'pet_breed'    => 'nullable|string|max:100',
            'pet_age'      => 'nullable|integer|min:0',
            'pet_gender'   => 'nullable|string|max:10',
        ]);

        $customer = Customer::create([
            'name'         => $request->name,
            'phone_number' => $request->phone_number,
            'address'      => $request->address,
        ]);

        Pet::create([
            'customer_id' => $customer->id,
            'name'        => $request->pet_name,
            'type'        => $request->pet_type,
            'breed'       => $request->pet_breed,
            'age_years'   => $request->pet_age,
            'gender'      => $request->pet_gender,
        ]);

        return redirect()->back()->with('success', 'Customer & hewan berhasil ditambahkan. Kode: ' . $customer->customer_code);
    }

    public function updateCustomer(Request $request, Customer $customer)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address'      => 'nullable|string',
        ]);
        $customer->update($request->only('name', 'phone_number', 'address'));
        return redirect()->back()->with('success', 'Data customer berhasil diperbarui.');
    }

    public function destroyCustomer(Customer $customer)
    {
        $customer->delete();
        return redirect()->back()->with('success', 'Customer berhasil dihapus.');
    }

    public function storePet(Request $request, Customer $customer)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'type'     => 'required|string|max:100',
            'breed'    => 'nullable|string|max:100',
            'age_years'=> 'nullable|integer|min:0',
            'gender'   => 'nullable|string|max:10',
        ]);

        Pet::create(['customer_id' => $customer->id] + $request->only('name', 'type', 'breed', 'age_years', 'gender'));
        return redirect()->back()->with('success', 'Hewan berhasil ditambahkan.');
    }

    public function destroyPet(Pet $pet)
    {
        $pet->delete();
        return redirect()->back()->with('success', 'Data hewan berhasil dihapus.');
    }

    // ==================== TRANSACTION MANAGEMENT ====================
    public function transactions(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $transactions = Transaction::with(['customer', 'pet', 'service'])
            ->when($search, fn($q) => $q->where(fn($sub) =>
                $sub->where('transaction_code', 'ilike', "%{$search}%")
                    ->orWhereHas('customer', fn($c) => $c->where('name', 'ilike', "%{$search}%"))
            ))
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $customers = Customer::with('pets')->get();
        $services  = Service::where('is_active', true)->get();

        return view('admin.transactions.index', compact('transactions', 'customers', 'services', 'search', 'status'));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'pet_id'          => 'required|exists:pets,id',
            'service_id'      => 'required|exists:services,id',
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'days'            => 'nullable|integer|min:1',
            'total_price'     => 'nullable|numeric|min:0',
            'pickup_required' => 'sometimes|boolean',
            'pickup_address'  => 'nullable|string',
            'pickup_time'     => 'nullable|date',
            'status'          => 'required|in:pending,confirmed,ongoing,completed,cancelled',
            'notes'           => 'nullable|string',
            'notes_internal'  => 'nullable|string',
        ]);

        $service   = Service::findOrFail($request->service_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate   = $request->end_date ? Carbon::parse($request->end_date) : null;

        $days = 1;
        if ($service->type === 'penitipan' && $endDate) {
            $days = max(1, $startDate->diffInDays($endDate));
        } elseif ($request->days) {
            $days = $request->days;
        }

        $totalPrice = $request->total_price ?? ($service->price * $days);

        Transaction::create([
            'customer_id'    => $request->customer_id,
            'pet_id'         => $request->pet_id,
            'service_id'     => $request->service_id,
            'total_price'    => $totalPrice,
            'payment_status' => 'unpaid',
            'status'         => $request->status ?? 'pending',
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'days'           => $days,
            'pickup_required'=> $request->boolean('pickup_required'),
            'pickup_address' => $request->pickup_address,
            'pickup_time'    => $request->pickup_time,
            'notes'          => $request->notes,
            'notes_internal' => $request->notes_internal,
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dibuat.');
    }

    public function verifyPayment(Transaction $transaction)
    {
        $transaction->update(['payment_status' => 'paid']);
        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,ongoing,completed,cancelled']);
        $transaction->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Status diperbarui.');
    }

    public function updateNotes(Request $request, Transaction $transaction)
    {
        $request->validate(['notes' => 'nullable|string', 'notes_internal' => 'nullable|string']);
        $transaction->update($request->only('notes', 'notes_internal'));
        return redirect()->back()->with('success', 'Catatan berhasil diperbarui.');
    }
}
