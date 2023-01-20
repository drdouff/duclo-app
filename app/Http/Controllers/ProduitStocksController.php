<?php

namespace App\Http\Controllers;

use App\Models\ProduitStocks;
use App\Http\Requests\StoreProduitStocksRequest;
use App\Http\Requests\UpdateProduitStocksRequest;

class ProduitStocksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreProduitStocksRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProduitStocksRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProduitStocks  $produitStocks
     * @return \Illuminate\Http\Response
     */
    public function show(ProduitStocks $produitStocks)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProduitStocks  $produitStocks
     * @return \Illuminate\Http\Response
     */
    public function edit(ProduitStocks $produitStocks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProduitStocksRequest  $request
     * @param  \App\Models\ProduitStocks  $produitStocks
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProduitStocksRequest $request, ProduitStocks $produitStocks)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProduitStocks  $produitStocks
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProduitStocks $produitStocks)
    {
        //
    }
}
