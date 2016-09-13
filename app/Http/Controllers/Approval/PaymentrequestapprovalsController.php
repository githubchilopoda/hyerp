<?php

namespace App\Http\Controllers\Approval;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Approval\Paymentrequest;
use App\Models\Approval\Approversetting;
use App\Models\Approval\Paymentrequestapproval;
use Auth;

class PaymentrequestapprovalsController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mcreate($paymentrequestid)
    {
        //
        $paymentrequest = Paymentrequest::findOrFail($paymentrequestid);
        // $config = DingTalkController::getconfig();
        // return view('approval/reimbursementapprovals/mcreate', compact('reimbursement', 'config'));
        return view('approval/paymentrequestapprovals/mcreate', compact('paymentrequest'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mstore(Request $request)
    {
        $input = $request->all();
        $userid = Auth::user()->id;
        // $myleveltable = Approversetting::where('approvaltype_id', ReimbursementsController::$approvaltype_id)
        //     ->where('approver_id', $userid)->first();
        $paymentrequest = Paymentrequest::findOrFail($input['paymentrequest_id']);
        $approversetting = Approversetting::findOrFail($paymentrequest->approversetting_id);
        // if ($myleveltable)
        {
            // $input['level'] = $myleveltable->level;
            $input['level'] = $approversetting->level;
            $input['approver_id'] = $userid;

            $paymentrequestapproval = Paymentrequestapproval::create($input);

            if ($input['status'] == '0')
            {
                // 设置下一个审批人
                if ($paymentrequestapproval)
                {
                    $approversettingNext = Approversetting::where('approvaltype_id', PaymentrequestsController::typeid())->where('level', '>', $approversetting->level)->orderBy('level')->first();
                    if ($approversettingNext)
                        $paymentrequest->approversetting_id = $approversettingNext->id;
                    else
                        $paymentrequest->approversetting_id = 0; // 已走完

                    $paymentrequest->save();
                }
            }
            elseif ($input['status'] == '-1') {
                // 设置上一个审批人
                if ($paymentrequestapproval)
                {
                    $paymentrequest = Paymentrequest::findOrFail($paymentrequestapproval->paymentrequest_id);
                    $approversetting = Approversetting::findOrFail($paymentrequest->approversetting_id);
                    $approversettingNext = Approversetting::where('approvaltype_id', PaymentrequestsController::typeid())->where('level', '<', $approversetting->level)->orderBy('level', 'desc')->first();
                    if ($approversettingNext)
                        $paymentrequest->approversetting_id = $approversettingNext->id;
                    else
                        $paymentrequest->approversetting_id = 0; // 已走完

                    $paymentrequest->save();
                }
            }

            // send dingtalk message.
            $touser = $paymentrequest->nextapprover();
            if ($touser)
            {
                DingTalkController::send($touser->dtuserid, '', 
                    '来自' . $reimbursement->applicant->name . '的付款申请单需要您审批.', 
                    config('custom.dingtalk.agentidlist.approval'));          
            }

            return 'success';
        }

        return 'error';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}