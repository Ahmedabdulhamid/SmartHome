<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('invoices.index');
    }
    public function getData()
    {
        $invoices = Invoice::query();

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->filter(function ($query) {
                if (request()->has('search') && request('search')['value'] != '') {
                    $searchValue = request('search')['value'];
                    $query->where(function ($q) use ($searchValue) {
                        $q->where('invoice_number', 'like', "%{$searchValue}%")
                            ->orWhere('invoice_number', 'like', "%{$searchValue}%")
                            ->orWhere('total_amount', 'like', "%{$searchValue}%");
                    });
                }
            })->addColumn('invoice_number', function ($invoice) {
                return $invoice->invoice_number;
            })->addColumn('invoice_date', function ($invoice) {
                return $invoice->invoice_date;
            })->addColumn('due_date', function ($invoice) {
                return $invoice->due_date ? $invoice->due_date : 'N/A';
            })->addColumn('total_amount', function ($invoice) {
                return 'EGP' . number_format($invoice->total_amount, 2);
            })
            ->addColumn('actions', function ($invoice) {
                return view('invoices._actions', compact('invoice'));
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->guard('web')->user() && auth()->guard('web')->user()->role == 'admin') {
            return view('invoices.create');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}


    public function show(string $id) {}


    public function edit(string $id)
    {
        if (auth()->guard('web')->user() && auth()->guard('web')->user()->role == 'admin') {
            $invoice = Invoice::findOrFail($id);
            return view('invoices.edit', compact('invoice'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }


    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (auth()->guard('web')->user() && auth()->guard('web')->user()->role == 'admin') {
            $invoice = Invoice::findOrFail($id);

            // تنفيذ عملية الحذف
            $invoice->delete();
            $invoiceCount=Invoice::count();

            // إرجاع استجابة نجاح (JSON) لطلب Ajax
            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully.',
                'invoiceCount'=>$invoiceCount
            ], 200);
        } else {
            abort(403, 'Unauthorized action.');
        }
        // البحث عن الفاتورة

    }
}
