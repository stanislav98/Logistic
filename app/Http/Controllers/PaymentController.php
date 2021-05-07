<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use \Stripe\Stripe;
use App\Models\Firm;

class PaymentController extends Controller
{
    public function retrievePlans() {
       $key = \config('services.stripe.secret');
       $stripe = new \Stripe\StripeClient($key);
       $plansraw = $stripe->plans->all();
       $plans = $plansraw->data;
       
       foreach($plans as $plan) {
           $prod = $stripe->products->retrieve(
               $plan->product,[]
           );
           $plan->product = $prod;
       }
       return response()->json(['plans' => [$plans] ],200);
    }

    public function processSubscription(Request $request)
    {
       $firm = Firm::find($request->firm['id']);
       $paymentMethod = $request->paymentMethod;        
       $firm->createOrGetStripeCustomer();

       try {

          $firm->newSubscription(
          $request->plan['product']['name'], $request->plan['id']
          )->create($paymentMethod['id']);
           return response()->json(['notification' => ['msg' => 'Успешно заплатихте абонамента си!' , 'type' => 1, 'active' => 1] 
            ],200);
       } catch (\Exception $e) {
            return response()->json(['notification' => ['msg' => 'Неуспешно плащане. Моля опитайте по-късно!' , 'type' => 0, 'active' => 1] 
            ],422);
       }
       
       
    }

    public function endSubscription(Request $request) {
    	$firm = Firm::find($request->firm['id']);
      try {
        $firm->subscriptions()->first()->cancel();
         return response()->json(['notification' => ['msg' => 'Успешно отказахте абонамента си!' , 'type' => 1, 'active' => 1] ],200);
      } catch (Exception $e) {
        return response()->json(['notification' => ['msg' => 'Неуспешно отказване от абонамент! Моля опитайте отново!' , 'type' => 0, 'active' => 1] ],422);
      }
    }

    public function getInvoices(Request $request) {
    	$firm = Firm::find($request->id);
      $invoices = [];
    	$invoices_object  = $firm->invoices();
      foreach($invoices_object  as $invoice) {
          array_push($invoices, $invoice->asStripeInvoice() );
      }

    	return response()->json( ['invoices' => $invoices ],200);
    }
}
