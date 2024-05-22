<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Charges;
use App\Models\CommissionCharge;
use Illuminate\Http\Request;

class ChargesController extends Controller
{
    public function charges()
    {
          $charge = Charges::first();
          $commission = CommissionCharge::first();
        return view('admin.settings.charges.index',compact('charge','commission'));
    }

    public function setCharges(Request $request)
    {
        $validatedData = $request->validate([
            'guarantee_amount' => 'required|numeric',
            'commission_amount' => 'required|numeric'
        ]);


         $charge = Charges::first();
        if($charge){
            $charge->amount = $request->guarantee_amount;
            $charge->save();
        }
        else {
            $charge = new Charges();
            $charge->amount = $request->guarantee_amount;
            $charge->save();
        }

        $commissionCharge = CommissionCharge::first();
        if($commissionCharge){
            $commissionCharge->amount = $request->commission_amount;
            $commissionCharge->save();
        }
        else {
            $commission = new CommissionCharge();
            $commission->amount = $request->commission_amount;
            $commission->save();
        }
  
        if ($charge) {
            return response()->json(['status' => true, 'msg' => "Charge Set Successfully", 'data' => $charge]);
        } else {
            return response()->json(['status' => true, 'msg' => "Something went wrong"]);
        }
    }
}
