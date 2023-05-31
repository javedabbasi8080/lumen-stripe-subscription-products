<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stripe =  new \Stripe\StripeClient('sk_test_4eC39HqLyjWDarjtT1zdp7dc');
        $users = [

            [
                'name' => "admin",
                'email' => "admin@admin.com",
                "password" => Hash::make('password'),
                "role" => 'superadmin',
                'plan_id' => 0
            ],

            [
                'name' => "customer1",
                'email' => "customer1@gmail.com",
                "password" => Hash::make('password'),
                "role" => 'customer',
                'plan_id' => 1
            ],

            [
                'name' => "customer2",
                'email' => "customer2@gmail.com",
                "password" => Hash::make('password'),
                "role" => 'customer',
                'plan_id' => 2
            ],

            [
                'name' => "customer3",
                'email' => "customer3@gmail.com",
                "password" => Hash::make('password'),
                "role" => 'customer',
                'plan_id' => 3
            ],

        ];

        foreach ($users as  $value) {
            $plan = Plan::find($value['plan_id']);
            $user = User::create([
                'name' => $value['name'],
                'email' => $value['email'],
                "password" => Hash::make('password')
            ]);

            $role = Role::where(['name' => $value['role']])->first();
            $permissions = Permission::pluck('id', 'id')->all();
            $role->syncPermissions($permissions);
            $user->assignRole([$role->id]);

            $stripeCustomer = $stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                // other customer details...
            ]);

            $user = User::find($user->id);

            if ($value['role'] == "customer") {
                $user->update(['stripe_cid' => $stripeCustomer->id]);


               $planCreate =  $stripe->plans->create([
                    'amount' => $plan->price,
                    'currency' => 'usd',
                    'interval' => 'month',
                    'product' => $plan->stripe_plan,
                  ]);

                // Subscribe customer to the plan
                $subscription = $stripe->subscriptions->create([
                    'customer' => $stripeCustomer->id,
                    'items' => [['plan' => $planCreate->id]],
                ]);

                // Save subscription details to user
                $user->subscriptions()->create([
                    'stripe_subscription_id' => $subscription->id,
                    'stripe_plan_id' => $planCreate->id,
                    'plan_id' => $plan->id,
                ]);
            }
        }
    }
}
