<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Partner\Waiver;
use App\Http\Controllers\Controller;
use App\Models\Partner\FamilyMember;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Partner\FamilyMemberRequest;
use App\Http\Requests\Partner\FamilyWaiverValidation;
use App\Models\Partner\UserWaiver;

class FamilyMemberController extends Controller
{

    public function index(Request $request)
    {
        $waiver = Waiver::where('show_at', 'family-add')->whereNull('deleted_at')->first();
        if($waiver) {
            $user_waiver = UserWaiver::where('user_id', auth()->user()->id)->where('waiver_id', $waiver->id)->first();
        }
        return Inertia::render('Store/Member/Family/Index', [
            'family_members' => FamilyMember::where('user_id', auth()->user()->id)->get(),
            'page_title' => __('Family'),
            'waiver' => $waiver,
            'user_waiver' => $user_waiver ?? null
        ]);
    }

    public function store(FamilyMemberRequest $request)
    {
        $waiverValidation = $this->validateIfHasWaiver();

        if(!empty($waiverValidation)) {
            return back()->withErrors($waiverValidation)->withInput();
        }

        $member = FamilyMember::create($request->all());

        if ($request->hasFile('profile_photo')) {
            $member->updateProfilePhoto($request->profile_photo);
        }
        $member->user_id = auth()->user()->id;
        $member->save();

        return $this->redirectBackSuccessWithSubdomain(__('Family member added successfully'), 'ss.member.family.index');
    }

    public function update(FamilyMemberRequest $request, $subdomain, $id)
    {
        $waiverValidation = $this->validateIfHasWaiver();

        if(!empty($waiverValidation)) {
            return back()->withErrors($waiverValidation)->withInput();
        }

        $member = FamilyMember::findOrFail($id);
        $member->update($request->validated());

        if ($request->has_image == false) {
            $member->deleteProfilePhoto();
        }

        if ($request->hasFile('profile_photo')) {
            $member->updateProfilePhoto($request->profile_photo);
        };

        return $this->redirectBackSuccessWithSubdomain(__('Family member updated successfully'), 'ss.member.family.index');
    }

    public function destroy($subdomain, $id)
    {
        $member = FamilyMember::findOrFail($id);
        $member->deleteProfilePhoto();
        $member->delete();

        return $this->redirectBackSuccessWithSubdomain(__('Family member deleted successfully'), 'ss.member.family.index');

    }


    public function validateIfHasWaiver()
    {
        $request = request();

        $waiver = null;

        if(request()->waiver_id) {
            $waiver = Waiver::find(request()->waiver_id);
        }

        if($waiver) {
            // dd(collect(request()->waiver_data['data']));
            $answers = collect(request()->waiver_data['data'])->whereNotNull('answer')
            // ->whereNotIn('answer', [false, 'false', 'undefined', null, 'null'])
            ->pluck('answer')->toArray();
            if((!(count($waiver->questions) == count($answers))) || ($waiver->is_signature_needed && !request()->waiver_data['signature'])) {
                $rules = [];
                $rules =  [
                    'waiver_data.data.*.answer' => 'required',
                ];
                $waiver = Waiver::find($request->waiver_id);

                if ($waiver->is_signature_needed && !request()->waiver_data['signature']) {
                    $rules['signature'] = ['required'];
                }

                $messages = [
                    'waiver_data.data.*.answer.required' => __('The answers to all waiver questions are required.'),
                    'signature.required' => __('Please provide your signature.'),
                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                return $this->formatErrorMessages($validator);

            } else {
                $this->saveWaiverAcceptanceData();
            }

        }

        return [];

    }

    public function formatErrorMessages($validator)
    {
        $errorMessages = [];

        if ($validator->fails()) {

            // Get the validation errors
            $errors = $validator->errors();

            // Define the errors you want to send to the frontend
            $errorMessages = [];

            // Check for at least one answers error
            $answerErrors = collect($errors->get('waiver_data.data.*.answer'))->filter();
            if ($answerErrors->isNotEmpty()) {
                $errorMessages['answer_error'] = __('The answers to all waiver questions are required.');
            }

            // Check for signature error
            if ($errors->has('signature')) {
                $errorMessages['signature_error'] = __('Please provide your signature.');
            }

        }

        return $errorMessages;
    }

    public function saveWaiverAcceptanceData() {

        UserWaiver::where('user_id', auth()->user()->id)->where('waiver_id', request()->waiver_id)->delete();
        UserWaiver::create([
            'user_id' => auth()->user()->id,
            'waiver_id' => request()->waiver_id,
            'user_waiver_accepted_data' => request()->waiver_data['data'],
            'signature' => request()->waiver_data['signature'],
        ]);
    }
}
