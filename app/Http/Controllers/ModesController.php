<?php

namespace App\Http\Controllers;

use App\Models\Modes;
use App\Http\Requests\StoreModesRequest;
use App\Http\Requests\UpdateModesRequest;

class ModesController extends Controller
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
     * @param  \App\Http\Requests\StoreModesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreModesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modes  $modes
     * @return \Illuminate\Http\Response
     */
    public function show(Modes $modes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modes  $modes
     * @return \Illuminate\Http\Response
     */
    public function edit(Modes $modes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateModesRequest  $request
     * @param  \App\Models\Modes  $modes
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateModesRequest $request, Modes $modes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modes  $modes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Modes $modes)
    {
        //
    }
}
