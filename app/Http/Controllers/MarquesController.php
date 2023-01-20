<?php

namespace App\Http\Controllers;

use App\Models\Marques;
use App\Http\Requests\StoreMarquesRequest;
use App\Http\Requests\UpdateMarquesRequest;

class MarquesController extends Controller
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
     * @param  \App\Http\Requests\StoreMarquesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMarquesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marques  $marques
     * @return \Illuminate\Http\Response
     */
    public function show(Marques $marques)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marques  $marques
     * @return \Illuminate\Http\Response
     */
    public function edit(Marques $marques)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMarquesRequest  $request
     * @param  \App\Models\Marques  $marques
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMarquesRequest $request, Marques $marques)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marques  $marques
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marques $marques)
    {
        //
    }
}
