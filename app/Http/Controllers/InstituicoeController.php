<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreInstituicoeRequest;
use App\Http\Requests\UpdateInstituicoeRequest;

class InstituicoeController extends Controller
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
     * @param  \App\Http\Requests\StoreInstituicoeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInstituicoeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Instituicoe  $instituicoe
     * @return \Illuminate\Http\Response
     */
    public function show(User $instituicoe)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Instituicoe  $instituicoe
     * @return \Illuminate\Http\Response
     */
    public function edit(User $instituicoe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateInstituicoeRequest  $request
     * @param  \App\Models\Instituicoe  $instituicoe
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInstituicoeRequest $request, User $instituicoe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Instituicoe  $instituicoe
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $instituicoe)
    {
        //
    }
}
