<?php

namespace App\Http\Controllers;

use App\Models\Paniers;
use App\Http\Requests\StorePaniersRequest;
use App\Http\Requests\UpdatePaniersRequest;

class PaniersController extends Controller
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
     * @param  \App\Http\Requests\StorePaniersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaniersRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Paniers  $paniers
     * @return \Illuminate\Http\Response
     */
    public function show(Paniers $paniers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Paniers  $paniers
     * @return \Illuminate\Http\Response
     */
    public function edit(Paniers $paniers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePaniersRequest  $request
     * @param  \App\Models\Paniers  $paniers
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaniersRequest $request, Paniers $paniers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Paniers  $paniers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Paniers $paniers)
    {
        //
    }
}
