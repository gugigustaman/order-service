<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

class FlashSaleTest extends TestCase
{
    protected $auth_users;
    protected $products;

    /**
     * Set up test case, database migration and seeding
     */
    protected function setUp(): void {
        print("\n");
        print("\n1. Set Up Database");
        parent::setUp();
        print("\n   - Migrating database...");
        Artisan::call('migrate:fresh');
        print("\n     [OK] Database migrated\n");

        print("\n   - Seeding database...");
        Artisan::call('db:seed');
        print("\n     [OK] Database seeded. 5 Users and 1 Product with 3 available stocks\n");
    }

    /**
     * Flash sale flow testing
     */
    public function testFlashSale() {        
        print("\n2. Ordering same product for each users...\n");
        $users = User::all();

        foreach ($users as $user) {
            /**
             * Log User In
             */
            print("\n   - Logging user ".$user->name." in...");
            $this->json('POST', '/api/login', [
                    'email' => $user->email,
                    'password' => 'Uc4nt.Gues$'
                ])
                ->assertResponseOk();

            $this->seeJsonStructure([
                'access_token', 'user'
            ]);

            print("\n     [OK] ".$user->name." logged in successfully\n");

            $data = $this->response->getData();

            $user = $data->user;
            $token = $data->access_token;

            /**
             * Add Product to Cart
             */
            print("\n   - Adding product to ".$user->name."'s' cart...");

            $this->json('POST', '/api/cart/add_item', [
                'product_id' => 1,
                'qty' => 1
            ], [
                'Authorization' => 'Bearer '.$token
            ])->assertResponseOk();

            print("\n     [OK] Product added to ".$user->name."'s' cart\n");

            /**
             * Check Product Exists in Cart
             */
            print("\n   - Checking product exists in ".$user->name."'s' cart...");
            
            $this->json('GET', '/api/cart', [], [
                'Authorization' => 'Bearer '.$token
            ])->seeJsonStructure([
                'user_id', 'status',
                'details' => [
                    ['product_id', 'qty']
                ]
            ]);

            $this->seeJson([
                'user_id' => $user->id,
                'status' => 0
            ]);

            $cart = $this->response->getData();

            $this->assertEquals(1, $cart->details[0]->product_id);
            $this->assertEquals(1, $cart->details[0]->qty);

            print("\n     [OK] Product exists in ".$user->name."'s' cart\n");


            /**
             * Pay User's Cart / Order
             */
            print("\n   - Paying ".$user->name."'s' cart...");

            $this->json('POST', '/api/cart/pay', [
                'payment_ref_num' => (String) Str::uuid()
            ], [
                'Authorization' => 'Bearer '.$token
            ])->assertResponseStatus(202);

            print("\n     [OK] Paid ".$user->name."'s' cart\n\n");
        }

        print("\n3. Executing Order Queue...");

        print("\n   - Making sure there are 5 orders in queue...");
        $this->assertEquals(5, Queue::size('order'));
        print("\n     [OK] There are 5 orders in queue\n");

        print("\n   - Executing queue...");
        Artisan::call('queue:work --queue=order --stop-when-empty');
        $this->assertEquals(0, Queue::size('order'));
        print("\n     [OK] All orders in queue executed\n");

        print("\n   - Checking product stock after order queue processed...");
        Artisan::call('queue:work --queue=order --stop-when-empty');
        $this->seeInDatabase('products', ['id' => 1, 'stock' => 0]);    
        print("\n     [OK] Product has been sold out\n");

        $validations = [
            1 => 1,
            2 => 1,
            3 => 1,
            4 => 0,
            5 => 0,
        ];

        foreach ($validations as $user_id => $order_status) {
            print("\n   - Checking User ".$user_id. "'s order status...");
            $this->seeInDatabase('orders', ['user_id' => $user_id, 'status' => $order_status]);    
            print("\n     [OK] Order ".$user_id. "'s order is ". ($order_status == 1 ? 'SUCCESS' : 'FAILED')."\n");
        }
    }

    
}
