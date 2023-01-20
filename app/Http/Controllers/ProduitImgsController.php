<?php

namespace App\Http\Controllers;

use App\Models\ProduitImgs;
use App\Http\Requests\StoreProduitImgsRequest;
use App\Http\Requests\UpdateProduitImgsRequest;

class ProduitImgsController extends Controller
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
     * @param  \App\Http\Requests\StoreProduitImgsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProduitImgsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProduitImgs  $produitImgs
     * @return \Illuminate\Http\Response
     */
    public function show(ProduitImgs $produitImgs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProduitImgs  $produitImgs
     * @return \Illuminate\Http\Response
     */
    public function edit(ProduitImgs $produitImgs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProduitImgsRequest  $request
     * @param  \App\Models\ProduitImgs  $produitImgs
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProduitImgsRequest $request, ProduitImgs $produitImgs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProduitImgs  $produitImgs
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProduitImgs $produitImgs)
    {
        //
    }
}
