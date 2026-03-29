<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $items = $this->whenLoaded('items', function () {
            return CartItemResource::collection($this->items);
        });

        $total = 0;
        if ($this->relationLoaded('items')) {
            $total = $this->items->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            });
        }

        return [
            'id' => $this->id,
            'items' => $items,
            'items_count' => $this->items_count ?? $this->items->count(),
            'total' => (float) $total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
