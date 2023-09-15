<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product= array(
            'title' => "Jacket",
            'slug' => "jacket",
            'summary' => "<p>Praesent dapibus, neque id cursus ucibus, tortor neque egestas augue, magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus.</p>",
            'description' => "<p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. sed ut perspiciatis unde sunt in culpa qui officia deserunt mollit anim id est laborum. sed ut perspiciatis unde omnis iste natus error sit voluptatem Excepteu sunt in culpa qui officia deserunt mollit anim id est laborum. sed ut perspiciatis Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. sed ut perspi deserunt mollit anim id est laborum. sed ut perspi</p>",
            'photo' => "img_product/e191f3bb6b24893757131e3fccd0a22be19162600bbc8259a0138589adf9f623.jpg",
            'stock' => 10,
            'size' => "M,L",
            'condition' => "new",
            'status' => 'active',
            'price' => 12000,
            'is_featured' => 1,
            'cat_id' => 1,
            'brand_id' => 1,
            'length' => 81,
            'width' => 28,
            'height' => 8,
            'weight' => 1000,
        );
        \App\Models\Product::create($product);
    }
}
