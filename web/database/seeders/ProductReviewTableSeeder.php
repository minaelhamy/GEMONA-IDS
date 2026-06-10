<?php

namespace Database\Seeders;

use App\Models\ProductReview;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class ProductReviewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public array $productReviews = [
        [
            'product_id' => 38,
            'star'       => 5,
            'review'     => "I love this hat and i can style it just about any way i’d like to!",
        ],
        [
            'product_id' => 29,
            'star'       => 4,
            'review'     => "Would have been 5 stars but these things suck up every piece of dust out there. True to fit and very comfortable!!!",
        ],
        [
            'product_id' => 7,
            'star'       => 5,
            'review'     => "Most comfortable and breathable pants. The waistband is awesome and the cut/taper of the pants is perfect. I would recommend to all friends and family who need casual pants.",
        ],
        [
            'product_id' => 3,
            'star'       => 5,
            'review'     => "Bought this for a gift for my teenage son. He really likes the basic look of the shirt itself but the unique pattern on the letters. The shirt itself is soft and warm, very comfortable. It looks good paired with many different pants and shoes.",
        ],

        [
            'product_id' => 74,
            'star'       => 5,
            'review'     => "Such a great diaper bag! Size is perfect for newborns. Good amount of storage and compartments for everything and the look is sleek. Highly recommend!",
        ],
        [
            'product_id' => 62,
            'star'       => 4,
            'review'     => "These are fantastic golf shoes. Comfy, nice materials, decent grip for a non-cleat option. Just keep in mind they run small. I have a lot of experience with this shoe and my estimate is they run 3/4 size small. The width seems like a solid medium. You would be safe to order a full size up but my recommendation would be to order two pairs (full size up and half size up), see what works, and return one, especially if you can get them on discount.",
        ],
        [
            'product_id' => 46,
            'star'       => 5,
            'review'     => "Fits oversized so you should size down one or two sizes depending on the look you’re looking for. Very warm can be worn with or without a jacked. The zipper in the back of the dress is an extra plus to styling it!!!",
        ],
        [
            'product_id' => 52,
            'star'       => 5,
            'review'     => "These are great pants. The feels like today was in the 30's with wind and I was warm as could be and I'm always cold. The fit is good if you read the chart. I'm 5'9 175 with a 33/34 inch waist and the medium fits perfectly.",
        ],
    ];

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO') && $envService->getValue('DISPLAY_TYPE') == 'fashion') {
            foreach ($this->productReviews as $productReview) {
                ProductReview::create([
                    'user_id'    => 3,
                    'product_id' => $productReview['product_id'],
                    'star'       => $productReview['star'],
                    'review'     => $productReview['review'],
                ]);
            }
        }
    }
}
