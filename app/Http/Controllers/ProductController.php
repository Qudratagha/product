<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $file = 'products.json';

    public function index()
    {
        return view('products.index');
    }

    public function fetch()
    {
        $products = $this->readFile();
        $totalSum = array_reduce($products, function ($sum, $product) {
            return $sum + ($product['quantity_in_stock'] * $product['price_per_item']);
        }, 0);

        return response()->json([
            'products' => $products,
            'totalSum' => $totalSum,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity_in_stock' => 'required|integer|min:0',
            'price_per_item' => 'required|numeric|min:0',
        ]);

        // Read existing data
        $products = $this->readFile();

        // Append new product
        $validated['datetime_submitted'] = now()->toDateTimeString();
        $validated['total_value'] = $validated['quantity_in_stock'] * $validated['price_per_item'];
        $products[] = $validated;

        // Save to JSON file
        $this->writeFile($products);

        return response()->json(['success' => true, 'product' => $validated]);
    }

    private function readFile()
    {
        if (!Storage::exists($this->file)) {
            Storage::put($this->file, json_encode([])); // Create an empty JSON file
        }
        return json_decode(Storage::get($this->file), true) ?? [];
    }

    private function writeFile($data)
    {
        Storage::put($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function edit($index)
    {
        $products = $this->readFile();

        // Check if the index exists
        if (!isset($products[$index])) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json(['product' => $products[$index]]);
    }

    public function update(Request $request, $index)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity_in_stock' => 'required|integer|min:0',
            'price_per_item' => 'required|numeric|min:0',
        ]);

        $products = $this->readFile();

        // Check if the index exists
        if (!isset($products[$index])) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Update the product data
        $validated['datetime_submitted'] = now()->toDateTimeString();
        $validated['total_value'] = $validated['quantity_in_stock'] * $validated['price_per_item'];
        $products[$index] = $validated;

        // Save the updated data back to the file
        $this->writeFile($products);

        return response()->json(['success' => true, 'product' => $validated]);
    }
}
