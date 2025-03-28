<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Product;
use App\Models\User;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory {
    protected $model = Ticket::class;

    public function definition()
    {
        $productId = Product::inRandomOrder()->first()?->product_id ?? 1;
        $userId = User::inRandomOrder()->first()?->user_id ?? 1;
        $siteId = Site::inRandomOrder()->first()?->site_id ?? 1;

        return [
            'product_id' => $productId,
            'user_id' => $userId,
            'site_id' => $siteId,
            'ticket_link' => $this->faker->url,
            'partner_code' => $this->faker->uuid,
            'partner_id' => (string) $this->faker->randomNumber(5),
            'validity_date' => $this->faker->date(),
            'purchase_date' => $this->faker->date(),
        ];
    }
}
