<?php

namespace App\Services\Partner;

use App\Models\Partner\Waiver;
use App\Models\Partner\UserWaiver;
use Illuminate\Support\Facades\Validator;

class WaiverValidationAndSaveService
{
    public static function validateIfHasWaiver()
    {
        $request = request();
        $waiver = null;

        if(request()->waiver_id) {
            $waiver = Waiver::find(request()->waiver_id);
        }

        if($waiver) {
            $rules = [];
            $messages = [];
            $answers = collect(request()->data)->whereNotNull('answer')
            ->pluck('answer')->toArray();
            if((!(count($waiver->questions) == count($answers))) || ($waiver->is_signature_needed && !request()->signature)) {
                if(!(count($waiver->questions) == count($answers))) {
                    $rules =  [
                        'data.*.answer' => 'required',
                    ];
                    $messages['data.*.answer.required'] = __('The answers to all waiver questions are required.');
                }
                if ($waiver->is_signature_needed && (!isset(request()->waiver_data['signature']) || !request()->waiver_data['signature'])) {
                    $rules['signature'] = ['required'];
                    $messages['signature.required'] = __('Please provide your signature.');
                }

                $validator = Validator::make($request->all(), $rules, $messages);

                return self::formatErrorMessages($validator);

            }
        }

        return [];

    }

    public static function formatErrorMessages($validator)
    {
        $errorMessages = [];

        if ($validator->fails()) {

            // Get the validation errors
            $errors = $validator->errors();

            // Define the errors you want to send to the frontend
            $errorMessages = [];

            // Check for at least one answers error
            $answerErrors = collect($errors->get('data.*.answer'))->filter();
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

    public static function saveWaiverAcceptanceData($family_member_id = null)
    {

        if(request()->waiver_id) {
            $user_waiver = UserWaiver::where('user_id', auth()->user()->id)
            ->where('waiver_id', request()->waiver_id);
            if($family_member_id) {
                $user_waiver = $user_waiver->where('family_member_id', $family_member_id);
            }
            $user_waiver = $user_waiver->first();
            if($user_waiver) {
                $user_waiver->delete();
            }

            $user_waiver = UserWaiver::create([
                'user_id' => auth()->user()->id,
                'waiver_id' => request()->waiver_id,
                'family_member_id' => $family_member_id ?? null,
                'user_waiver_accepted_data' => request()->data,
                'signature' => request()->signature,
            ]);

            return $user_waiver->id;
        }
    }

    public static function checkWaiverComplete($family_member_id = null){
        $waivers = Waiver::where('show_at','family-add')->get();
        $user_waivers = UserWaiver::where([['user_id',auth()->user()->id],['family_member_id','!=',null]])->get();
        if(count($waivers) == count($user_waivers)){
            return true;
        }else{
            return false;
        }
    }
}
