<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;

class ApiController extends Controller
{
    /**
     * Return pets for a given customer (used by AJAX in transaction form)
     */
    public function petsByCustomer($customerId)
    {
        $pets = Pet::where('customer_id', $customerId)
            ->get(['id', 'name', 'type', 'breed'])
            ->map(fn($p) => [
                'id'   => $p->id,
                'label'=> $p->name . ' (' . $p->type . ($p->breed ? ' - ' . $p->breed : '') . ')',
            ]);

        return response()->json($pets);
    }
}
