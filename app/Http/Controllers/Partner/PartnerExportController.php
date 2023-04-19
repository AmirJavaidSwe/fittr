<?php

namespace App\Http\Controllers\Partner;

use App\Enums\ExportType;
use App\Jobs\ProcessExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\ExportFormRequest;
use App\Models\Partner\Export;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;


class PartnerExportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Partner/Datacenter/Export', [
            'exportings' => Export::orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('csv_file_name', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Data Center - Exports'),
            'header' => array(
                [
                    'title' => __('Exports'),
                    'link' => null,
                ],
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ExportFormRequest $request
     * @return RedirectResponse
     */
    public function store(ExportFormRequest $request)
    {
        $export = Export::create([
            'export_type' => $request->export_type,
            'filters' => array_filter($request->filters),
            'created_by' => $request->user()->id,
        ]);

        ProcessExport::dispatch($export);

        $extra = array('export_id' => $export->id);

        return $this->redirectBackSuccess(__('Export started'))->with('extra', $extra);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\Export  $export
     * @return Response
     */
    public function show(Export $export)
    {
        return Inertia::render('Partner/Datacenter/ExportShow', [
            'page_title' => __('Studio details'),
            'header' => array(
                [
                    'title' => __('Data Center - Export details'),
                    'link' => route('partner.exports.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Export details'),
                    'link' => null,
                ],
            ),
            'exporting' => $export,
        ]);
    }

        /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\Export  $studio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Export $export)
    {
        $export->delete();

        return $this->redirectBackSuccess(__('Export deleted successfully'), 'partner.exports.index');
    }

}
