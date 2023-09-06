<?php

namespace App\Http\Controllers\Partner;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Partner\Waiver;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\WaiverFromRequest;
use App\Models\Partner\FamilyMemberWaiver;
use App\Models\Partner\UserWaiver;

class PartnerWaiverController extends Controller
{

    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'asc');

        return Inertia::render('Partner/Waiver/Index', [
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Waivers'),
            'header' => array(
                [
                    'title' => __('Waivers'),
                    'link' => route('partner.waivers.index'),
                ]
            ),
            'waivers' => Waiver::get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Partner/Waiver/Create', [
            'page_title' => __('Create New Waiver'),
            'header' => array(
                [
                    'title' => __('Waivers'),
                    'link' => route('partner.waivers.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Create New Waiver'),
                    'link' => null,
                ],
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WaiverFromRequest $request)
    {
        Waiver::create($request->all());

        return $this->redirectBackSuccess(__('Waiver created successfully'), 'partner.waivers.index');
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
        return Inertia::render('Partner/Waiver/Edit', [
            'page_title' => __('Edit Waiver'),
            'header' => array(
                [
                    'title' => __('Waivers'),
                    'link' => route('partner.waivers.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit Waiver'),
                    'link' => null,
                ],
            ),
            'editWaiver' => Waiver::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WaiverFromRequest $request, $id)
    {
        $waiver = Waiver::findOrFail($id);
        $waiver->update($request->all());

        if($request->sign_again) {
            FamilyMemberWaiver::where('waiver_id', $id)->delete();
        }

        return $this->redirectBackSuccess(__('Waiver updated successfully'), 'partner.waivers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $waiver = Waiver::findOrFail($id);
        UserWaiver::where('waiver_id', $id)->delete();
        $waiver->delete();

        return $this->redirectBackSuccess(__('Waiver deleted successfully'), 'partner.waivers.index');
    }
}
