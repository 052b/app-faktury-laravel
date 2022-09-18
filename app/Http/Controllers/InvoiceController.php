<?php

namespace App\Http\Controllers;

use App\Filters\FiltersInvoice;
use App\Filters\FiltersInvoiceClient;
use App\Filters\FiltersInvoiceCreatedDate;
use App\Filters\InvoiceCustomerSort;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $invoices = QueryBuilder::for(Invoice::class)
            ->allowedFilters([
                AllowedFilter::custom('created', new FiltersInvoiceCreatedDate()),
                AllowedFilter::custom('client', new FiltersInvoiceClient()),
                AllowedFilter::custom('invoice', new FiltersInvoice()),
            ])
            ->join('site_contractor', 'site_contractor.id', 'site_invoices.id_contractor')
            ->select(['site_invoices.*', 'site_contractor.name AS client_name'])
            ->defaultSort('-create_date')
            ->allowedSorts(['invoice_number', 'create_date', 'sell_date', 'status', 'client_name'])
            ->withSum('positions', 'price_netto')
            ->where('site_invoices.deleted', 0)
            ->paginate()
            ->withQueryString();

        $clients = Client::where('id_company', 1)
            ->where('name', '<>', '')
            ->orderBy('name')
            ->orderBy('deleted')
            ->get(['id', 'name', 'deleted']);

        return inertia('Invoice/Index', [
            'invoices' => $invoices,
            'clients'  => $clients,
            'filters'  => \request()->all(['filter', 'sort'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
