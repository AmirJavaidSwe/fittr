<?php

namespace App\Http\Controllers\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PartnerChargeController extends Controller
{

    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Partner/Charges/Index', [
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Taxes & Charges'),
            'header' => array(
                [
                    'title' => __('Taxes & Charges'),
                    'link' => route('partner.charges.index'),
                ]
            ),
            'charges' => []
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        //
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
        //
    }
}
