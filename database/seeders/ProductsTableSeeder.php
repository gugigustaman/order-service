<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('products')->insert([
         	[
	            'name' => 'Product X',
	            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem iusto animi consectetur sint explicabo et totam voluptatum hic vel enim, architecto consequuntur quod at impedit, necessitatibus quas maiores est officiis ad magnam, expedita, nostrum obcaecati quam ullam? Voluptatem sunt sit deleniti aut molestiae praesentium aspernatur, aperiam laborum officia laboriosam optio?',
	            'price' => 99000,
	            'stock' => 3,
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now(),
	        ]
    	]);
    }
}
