<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plans\ProductModel;
use App\Models\HealthProductBenefits;
use Auth;
use Validator;

class ProductApiController extends Controller
{
    public function fatch_products(Request $request)
    {
        //   dd('hi');
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required',
         ]);

       if ($validator->fails()) {
          return response()->json(['status' => 'Error','message' => $validator->errors()],200);
         }
        $plan_id = $request->plan_id;
        //   dd($plan_id );
        $products = ProductModel::where('plan_id', $plan_id)->get();
        //   dd($products);
        if ($products) {

            $productData = [];
            foreach($products as  $product)
            {
            $productData[] = [
                'product_id' => $product->product_id,
                'plan_id' => $product->plan_id,
                'product_name' => $product->product_name,
                'term_takaful' => $product->term_takaful,
                'annual_hospital_cash_limit' => $product->annual_hospital_cash_limit,
                'natural_death_benefit' => $product->natural_death_benefit,
                'accidental_death_benefit' => $product->accidental_death_benefit,
                'accidental_medicial_reimbursement	' => $product->accidental_medicial_reimbursement,
                'contribution' => $product->contribution,
                'product_code' => $product->product_code,
                'fee' => $product->fee,
                'autoRenewal' => $product->autoRenewal,
                'duration' => $product->duration,
                'scope_of_cover' => $product->scope_of_cover,
                'eligibility' => $product->eligibility,
                'other_key_details' => $product->other_key_details,
                'exclusions' => $product->exclusions,
               ];

               $HealthProductBenefits = HealthProductBenefits::where('product_id', $product->product_id)->get();
            //    dd($HealthProductBenefits);
            $HealthProductBenefitsData = [];
            foreach($HealthProductBenefits as  $HealthProductBenefits)
            {
            $HealthProductBenefitsData[] = [
                'id' => $HealthProductBenefits->product_id,
                'product_id' => $HealthProductBenefits->product_id,
                'benefits' => $HealthProductBenefits->benefits,
                'silver' => $HealthProductBenefits->silver,
                'gold' => $HealthProductBenefits->gold,
                'platinum' => $HealthProductBenefits->platinum,

               ];
            }

            }

            $data = array(
              'status' => 'Success',
              'message' => 'Your Products Get Successfully',
              'Products' => $productData,
              'HealthProductBenefitsData' => $HealthProductBenefitsData,
            );
            return response()->json($data ,200);

        }

        else{
            return response()->json([
                'status' => 'Error',
                'data' => [
                    'ErrorCode' => 500,
                    'message' => 'Plan Id is Not available',
                ],
            ], 500);
        }

        return response()->json(['status' => 'Error','message' => 'Plan Id is Not available.'], 200);

    }
}
