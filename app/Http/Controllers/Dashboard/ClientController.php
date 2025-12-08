<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clients.index');
    }
    public function getData()
    {
        $clients = Client::query();
        return DataTables::of($clients)->addIndexColumn()
            ->filter(function ($query) {
                if (request()->has('search') && !empty(request('search.value'))) {
                    $search = request('search.value');
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . "%")
                            ->orWhere('email', 'like', '%' . $search . "%")
                            ->orWhere('phone', 'like', '%' . $search . "%")
                            ->orWhere('address', 'like', '%' . $search . "%");
                    });
                }
            })->addColumn('name', function ($client) {
                return $client->name;
            })->addColumn('email', function ($client) {
                return $client->email;
            })->addColumn('address', function ($client) {
                return $client->address;
            })->addColumn('actions', function ($client) {
                return view('clients._actions', ['client' => $client]);
            })

            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->guard('web')->user() && auth()->guard('web')->user()->role == 'admin') {
            return view('clients.create');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (auth()->guard('web')->user() && auth()->guard('web')->user()->role == 'admin') {
            $client = Client::findOrFail($id);
            return view('clients.edit', compact('client'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ((auth()->guard('web')->user() && auth()->guard('web')->user()->role == 'admin')) {
            $client = Client::findOrFail($id);
            $client->delete();
            $clientCount=Client::count();
            $invoiceCount=Invoice::count();
            return response()->json(['success' => true,'clientCount'=>$clientCount,'invoiceCount'=>$invoiceCount]);
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
}
