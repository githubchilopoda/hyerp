<?php

namespace App\Models\Approval;

use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    //
    protected $fillable = [
        'reimbursementtype_id',
        'date',
        'number',
        'amount',
        'customer_id',
        'contacts',
        'contactspost',
        'order_id',
        'status',
        'statusdescrip',
        'descrip',
        'datego',
        'dateback',
        'mealamount',
        'ticketamount',
        'stayamount',
        'otheramount',
        'approvaler1_id',
        'approvaldate1',
        'approvaler2_id',
        'approvaldate2',
        'approvaler3_id',
        'approvaldate3',
        'applicant_id',
    ];

    public function applicant() {
        return $this->hasOne('\App\Models\System\User', 'id', 'applicant_id');
    }

    public function reimbursementimages() {
        return $this->hasMany('\App\Models\Approval\Reimbursementimages', 'reimbursement_id', 'id');
    }
}
