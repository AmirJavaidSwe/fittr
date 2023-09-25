<?php

namespace App\Http\Controllers\Partner;

use App\Enums\PartnerUserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\InstructorFormRequest;
use App\Models\Partner\User;
use App\Models\Partner\Instructor;
use App\Traits\ImageableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'instructors' => User::with('profile.images')
                ->instructor()
                ->orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('name', 'LIKE', '%'.$this->search.'%')
                              ->orWhere('email', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString()
                //AbstractPaginator@through(), transforms chunk
                ->through(fn ($user) => [
                    //may inject number of classes run/scheduled ?
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'profile' => $user->profile,
                ]),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Instructors'),
            'header' => __('Instructors'),
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
                'role' => PartnerUserRole::INSTRUCTOR->value,
                // 'password' => Hash::make('password'),
            ]
        );

        $instructor = Instructor::create($fields); //TODO: Security issue

        $instructor->sendEmailVerificationNotification();

        if(request()->has('returnTo')) {
            $extra = array('instructor' => $instructor);
            return redirect()->route(request()->returnTo)->with('extra', $extra);
        }

        return $this->redirectBackSuccess(__('Instructor created successfully'), 'partner.instructors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\User  $member
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
                    'title' => __('Instructor details'),
                    'link' => null,
                ],
            ),
            'instructor' => $instructor,
        ]);
    }

    public function edit(Instructor $instructor)
    {
        return Inertia::render('Partner/Instructor/Edit', [
            'page_title' => __('Edit Instructor'),
            'header' => __('Edit Instructor'),
            'instructor' => $instructor
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

        $instructor->load('profile');

        if ($instructor->profile) {
            $instructor->profile->update([
                'user_id' => $instructor->id,
                'description' => $request->profile_description,
            ]);
        } else {
            $instructor->profile()->create([
                'user_id' => $instructor->id,
                'description' => $request->profile_description,
            ]);

            $instructor->load('profile');
        }

        try {
            if($request->hasFile('profile_image')) {
                $this->updateFiles($request->file('profile_image'), [], $instructor->profile, 'images/instructor_photos');
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
     * @param  \App\Models\Partner\User  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Instructor $instructor)
    {
        $instructor->delete();

        //to do: inject method to unset instructor from assigned classes that will run in future; also alert admin with a list of affected classes

        return $this->redirectBackSuccess(__('Instructor deleted successfully'), 'partner.instructors.index');
    }
}
