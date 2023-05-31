<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Stripe\Product;
use Exception;

/**
 * @group Products
 *
 * APIs for Products
 */

class PlanController extends Controller
{
    public $stripe;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->stripe =  new \Stripe\StripeClient('sk_test_4eC39HqLyjWDarjtT1zdp7dc');
    }

    /**
     * Display all products.
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        try {
            $plans = Plan::paginate(10);
            return response()->json([
                'status'  => true,
                'data'    => $plans,
                'message' => 'Product reterive successfully'
            ], 200);
            //code...
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'data'    => $e->getMessage(),
                'message' => 'error occurred'
            ], 500);
        }
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
     * Create product
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @bodyParam name string required Example: Standard
     * @bodyParam price float required Example: 12
     * @bodyParam description test required Example: description
     */
    public function store(Request $request)
    {

        // Validate the request parameters
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required',
            'description' => 'required'
        ]);

        try {
            $product = $this->stripe->products->create([
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);

            $plan = Plan::create([
                'name' => $request->input('name'),
                'slug' => $request->input('name'),
                'stripe_plan' => $product->id,
                'price' => $request->input('price'),
                'description' => $request->input('description'),
            ]);

            /** 
             * @todo this is for stripe sdk
             */
            // $product = Product::create([
            //     'name' => $request->input('name'),
            //     'price' => $request->input('price'),
            //     'description' => $request->input('description'),
            //     'type' => 'service',
            // ]);

            return response()->json([
                'status'  => true,
                'data'    => $plan,
                'message' => 'Product created successfully'
            ], 200);
            //code...
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'data'    => $e->getMessage(),
                'message' => 'error occurred'
            ], 500);
        }
    }

    /**
     * Display the Product.
     * @authenticated
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $plan = Plan::find($id);

            return response()->json([
                'status'  => true,
                'data'    => $plan,
                'message' => 'Product updated successfully'
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'data'    => $e->getMessage(),
                'message' => 'error occurred'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @authenticated
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        //
    }

    /**
     * Update the product.
     * @authenticated
     * @bodyParam name string required Example: Standard
     * @bodyParam price float required Example: 12
     * @bodyParam description test required Example: description
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        // Validate the request parameters
        $request->merge(['id' => $id]);
        $this->validate($request, [
            'id' => 'required|exists:plans',
            'name' => 'required',
            'price' => 'required',
            'description' => 'required'
        ]);

        try {


            $plan = Plan::find($id);

            $plan->update([
                'name' => $request->input('name'),
                'slug' => $request->input('name'),
                'price' => $request->input('price'),
                'description' => $request->input('description'),
            ]);

            $product = $this->stripe->products->update($plan->stripe_plan, [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'active' => true
            ]);

            return response()->json([
                'status'  => true,
                'data'    => $plan,
                'message' => 'Product updated successfully'
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'data'    => $e->getMessage(),
                'message' => 'error occurred'
            ], 500);
        }
    }

    /**
     * Delete Product.
     * @authenticated
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try {


            $plan = Plan::find($id);

            $plan->delete();

            $product = $this->stripe->products->delete($plan->stripe_plan, []);


            return response()->json([
                'status'  => true,
                'data'    => $plan,
                'message' => 'Product deleted successfully'
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'data'    => $e->getMessage(),
                'message' => 'error occurred'
            ], 500);
        }
    }
}
