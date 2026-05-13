<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class PublicProductController extends Controller
{
    public function show(Product $product): Response
    {
        abort_unless($product->active, 404);

        return Inertia::render('products/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'size' => $product->size,
                'price' => $product->price,
                'images' => $product->getMedia('images')->map(fn ($m) => [
                    'id' => $m->id,
                    'url' => $m->getUrl(),
                ]),
            ],
        ]);
    }
}
