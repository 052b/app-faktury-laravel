<?php

namespace App\Http\Controllers;

use App\Filters\FiltersClient;
use App\Filters\FiltersClientCompany;
use App\Filters\FiltersClientDeleted;
use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;
use Inertia\ResponseFactory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $clients = QueryBuilder::for(Client::class)
            ->allowedFilters([
                AllowedFilter::custom('client', new FiltersClient()),
                AllowedFilter::custom('deleted', new FiltersClientDeleted())->default(0),
                AllowedFilter::custom('company', new FiltersClientCompany())->default(1)
            ])
            ->allowedSorts(['name', 'address', 'city', 'nip', 'phone', 'mail'])
            ->paginate()
            ->withQueryString();

        return inertia('Client/Index', [
            'clients' => $clients,
            'filters' => \request()->all(['filter', 'sort'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreClientRequest $request
     * @return RedirectResponse
     */
    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();

        Client::create($validated);

        return \redirect()->route('clients.index')->with([
            'toast' => [
                'type'    => 'success',
                'message' => 'Utworzono nowy rekord'
            ]
        ]);
    }

    public function create()
    {
        return inertia('Client/Edit', ['client' => new Client()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Client $client
     * @return Response|ResponseFactory
     */
    public function edit(Client $client): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Client/Edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreClientRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(StoreClientRequest $request, int $id)
    {
        $validated = $request->validated();

        $client = Client::findOrFail($id);
        $client->fill($validated);
        $client->save();

        return \redirect()->route('clients.index')->with([
            'toast' => [
                'type'    => 'success',
                'message' => 'Rekord został zaktualizowany'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Client $client
     * @return RedirectResponse
     */
    public function destroy(Client $client): \Illuminate\Http\RedirectResponse
    {
        $client->deleted = true;
        $client->save();

        return \redirect()->route('clients.index')->with([
            'toast' => [
                'type'    => 'success',
                'message' => 'Rekord został usunięty'
            ]
        ]);
    }
}
