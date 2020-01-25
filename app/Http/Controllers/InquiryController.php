<?php

namespace App\Http\Controllers;

use App\CustomerInformation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Mail;
use App\Mail\ThankyouMail;
use Illuminate\Support\Arr;


class InquiryController extends Controller
{
    public function showInquiryInputForm()
    {
        $items = [
            "igift" => "i-gift",
            "ivr" => 'Saas型IVR',
            "isearch" => "i-search",
            "iask" => "i-ask",
            "ilinkcheck" => "i-linkcheck",
            "iprint" => "i-print",
            "icatalog" => "i-catalog",
            "iflow" => "i-flow",
            "ilinkplus" => "i-linkplus",
            "ishopnavi" => "i-shopnavi",
            "ipoint" => "i-point",
            "ilivechat" => "i-livechat",
            "hosting" => "ホスティングサービス",
            "icampaign" => "キャンペーンサービス",
            "others" => "その他(会社に関する事など)"
        ];

        return view('inquiryInputForm', compact('items'));
    }
    // jxffSGu*e_EnY5V
    public function showInquiryConfirmation()
    {
        $info = request()->validate($this->validateRules());
        // $info = request();
        return view('inquiryconfrim', compact('info'));
    }

    public function showInquiryComplete()
    {
        $rules = $this->validateRules();
        $validated = Validator::make(request()->all(), $rules);

        if ($validated->fails()) {
            return redirect()->route('inquiry.input')
                    ->withErrors($validated)
                    ->withInput()
                    ->with('message', '登録に失敗しました。');
        }

        $content = $validated->getData();
        $data = Arr::except($content, ['_token']);

        CustomerInformation::create($data);
        Mail::to(request()->mailaddress)->send(new ThankyouMail($content));
        return view('completeform');
    }

    protected function validateRules()
    {
        return [
            "subject" => 'required',
            "opinion" => 'required| max: 500',
            "company" => 'required| max: 50',
            "section" => 'max: 50',
            "name" => 'required| max: 20',
            "ruby" => 'max: 20',
            "tel" => ['required', 'numeric', 'regex:(^[0-9]{4,20}$)'],
            "mailaddress" => 'required| email ',
        ];
    }

    public function inquiryBackward()
    {
        return request();

        return redirect()->route('inquiry.input')->withInput();
    }

}
