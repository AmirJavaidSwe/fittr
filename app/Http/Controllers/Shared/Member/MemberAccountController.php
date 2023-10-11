<?php

namespace App\Http\Controllers\Shared\Member;

use Inertia\Inertia;
use App\Models\Partner\User;
use Illuminate\Http\Request;
use App\Traits\ImageableTrait;
use App\Models\Partner\Booking;
use Illuminate\Validation\Rule;
use App\Models\Partner\Location;
use App\Models\Partner\ClassLesson;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Partner\FamilyMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Laravel\Jetstream\Http\Controllers\Inertia\Concerns\ConfirmsTwoFactorAuthentication;


class MemberAccountController extends Controller
{
    use ConfirmsTwoFactorAuthentication;
    use ImageableTrait;

    protected $search;
    protected $per_page;
    protected $order_by;
    protected $order_dir;
    protected $member_id;

    public function index(Request $request) {

        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        $user_id = empty(config('subdomain')) ? $request->member_id : auth()->user()->id;

        // member details
        $member = User::with('images')->select('id', 'role', 'first_name', 'last_name', 'email')->whereId($user_id)->first();

        // member booking details
        $bookings = Booking::with(['class.classType', 'class.instructors', 'class.studio.location'])
        ->where('user_id', $user_id);

        if($request->is_parent == "false" && $request->search_member_id) {
            $bookings = $bookings->where('family_member_id', $request->search_member_id);
        }

        else if($request->is_parent == "true") {
            $bookings = $bookings->whereNull('family_member_id');
        }

        $bookings = $bookings->orderBy($this->order_by, $this->order_dir)
        ->when($this->search, function ($query) {
            $query->whereHas('class', function($subQuery) {
                $subQuery->where('title', 'LIKE', '%'.$this->search.'%');
            });
        })

        ->paginate($this->per_page)
        ->withQueryString();

        // member family details
        $family_members = FamilyMember::with('userWaivers')->where('user_id', $user_id)->get();

        return Inertia::render('Store/Member/Profile/Index', [
            'search' => $this->search,
            'member_id' => $request->member_id,
            'search_member_id' => $request->search_member_id,
            'is_parent' => $request->is_parent,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            "bookings" => $bookings,
            "family" => $family_members,
            'member' => User::with('images')->select('id', 'role', 'first_name', 'last_name', 'email')->whereId(auth()->user()->id)->first(),
            'page_title' => empty(config('subdomain')) ?  __('Member Profile management') :  __('Profile management'),
            'header' => empty(config('subdomain')) ?  __('Member Profile management') :  __('Profile management')
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique('mysql_partner.users', 'email')->ignore($request->user_id, 'id')]
        ]);
        $user = User::find($request->user_id);
        $user->fill($request->except(['user_id']));
        $user->save();
        $msg = __("Updated Successfully");
        return $this->redirectBackSuccess($msg);
    }

    public function updatePhoto(Request $request)
    {
        try {
            // upload new and/or delete existing
            $user = User::find($request->user_id);
            if($request->hasFile('photo')) {
                $this->updateFiles($request->file('photo'), [], $user, session('business.id').'/members');
            }
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectBackError(__('Something went wrong!'));
        }
        $msg = __("Profile Photo Updated Successfully");
        return $this->redirectBackSuccess($msg);
    }
}
