<?php

namespace App\Http\Controllers\Partner;

use App\Enums\PartnerUserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\InstructorFormRequest;
use App\Models\Partner\ClassType;
use App\Models\Partner\Instructor;
use App\Models\Partner\InstructorProfile;
use App\Traits\ImageableTrait;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PartnerInstructorController extends Controller
{

    use ImageableTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'asc');

        return Inertia::render('Partner/Instructor/Index', [
            'instructors' => Instructor::with(['classTypes', 'profile.images'])
                ->orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('first_name', 'LIKE', '%'.$this->search.'%')
                              ->orWhere('last_name', 'LIKE', '%'.$this->search.'%')
                              ->orWhere('email', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString()
                //AbstractPaginator@through(), transforms chunk
                ->through(fn ($user) => [
                    //may inject number of classes run/scheduled ?
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'initials' => $user->initials,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'profile' => $user->profile,
                    'classtypes' => $user->classTypes,
                ]),
            'classtypes' => ClassType::select('id', 'title')->get(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Instructors'),
            'header' => array(
                [
                    'title' => __('Instructors'),
                    'link' => route('partner.instructors.index'),
                ],
            )
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Partner/Instructor/Create', [
            'page_title' => __('Create Instructor'),
            'header' => array(
                [
                    'title' => __('Instructors'),
                    'link' => route('partner.instructors.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Create new instructor'),
                    'link' => null,
                ],
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\InstructorFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InstructorFormRequest $request)
    {
        $fields = array_merge(
            $request->validated(),
            [
                'role' => PartnerUserRole::get('instructor'),
                // 'password' => Hash::make('password'),
            ]
        );

        $instructor = Instructor::create($fields);
        $instructor->classTypes()->sync($request->classtypes ?? []);

        //TODO: We need better logic here
        //ALSO: when activation occurs instructor needs proper redirection

        // $instructor->sendEmailVerificationNotification();

        $profile = InstructorProfile::create([
                'user_id' => $instructor->id,
                'description' => $request->profile_description
            ]);

        try {
            //upload new and/or delete existing
            if($request->hasFile('profile_image')) {
                $this->updateFiles($request->file('profile_image'), [], $profile, session('business.id').'/instructors');
            }
        } catch(\Exception $e) {
            logger()->error($e->getMessage());
        }

        if(request()->has('returnTo')) {
            $extra = array('instructor' => $instructor);
            return redirect()->route(request()->returnTo)->with('extra', $extra);
        }

        return $this->redirectBackSuccess(__('Instructor created successfully'), 'partner.instructors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\Instructor  $instructor
     * @return \Illuminate\Http\Response
     */
    public function show(Instructor $instructor)
    {
        return Inertia::render('Partner/Instructor/Show', [
            'page_title' => __('Instructor details'),
            'header' => array(
                [
                    'title' => __('Instructors'),
                    'link' => route('partner.instructors.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Details'),
                    'link' => null,
                ],
            ),
            'instructor' => $instructor->load(['classTypes', 'profile.images']),
        ]);
    }

    public function edit(Instructor $instructor)
    {
        return Inertia::render('Partner/Instructor/Edit', [
            'page_title' => __('Edit Instructor'),
            'header' => __('Edit Instructor'),
            'header' => array(
                [
                    'title' => __('Instructors'),
                    'link' => route('partner.instructors.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit'),
                    'link' => null,
                ],
            ),
            'instructor' => $instructor->load(['classTypes', 'profile.images']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\InstructorFormRequest  $request
     * @param  \App\Models\Partner\Instructor  $instructor
     * @return \Illuminate\Http\Response
     */
    public function update(InstructorFormRequest $request, Instructor $instructor)
    {
        $instructor->update($request->validated());
        $instructor->classTypes()->sync($request->classtypes ?? []);
        $profile = InstructorProfile::updateOrCreate(
            [
                'user_id' => $instructor->id,
            ],
            ['description' => $request->profile_description]
        );

        try {
            //upload new and/or delete existing
            if($request->hasFile('profile_image') || !$request->old_profile_image) {
                $this->updateFiles($request->file('profile_image'), [], $profile, session('business.id').'/instructors');
            }
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectBackError(__('Something went wrong!'));
        }

        return $this->redirectBackSuccess(__('Instructor updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\Instructor  $instructor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Instructor $instructor)
    {
        $instructor->delete();

        //to do: inject method to unset instructor from assigned classes that will run in future; also alert admin with a list of affected classes

        return $this->redirectBackSuccess(__('Instructor deleted successfully'), 'partner.instructors.index');
    }
}
