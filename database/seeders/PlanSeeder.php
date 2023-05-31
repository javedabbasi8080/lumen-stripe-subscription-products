<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stripe =  new \Stripe\StripeClient('sk_test_4eC39HqLyjWDarjtT1zdp7dc');

        $data = [

            [
                'name' => 'Product 1', 
                'slug' => 'Product-1', 
                'price' => 10, 
                'description' => 'description', 
            ],


            [
                'name' => 'Product 2', 
                'slug' => 'Product-2', 
                'price' => 10, 
                'description' => 'description', 
            ],

            [
                'name' => 'Product 3', 
                'slug' => 'Product-3', 
                'price' => 10, 
                'description' => 'description', 
            ],
        ];


        foreach ($data as  $value) {

            $product = $stripe->products->create([
                'name' => $value['name'],
                'description' => $value['description']
            ]);
    
            $plan = Plan::create([
                'name' => $value['name'],
                'slug' => $value['slug'],
                'stripe_plan' => $product->id,
                'price' => $value['price'],
                'description' => $value['description'],
            ]);

            # code...
        }

        

        //
    }
}
