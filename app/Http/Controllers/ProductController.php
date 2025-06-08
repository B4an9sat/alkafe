<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::with('category')->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $validatedData['image'] = $request->file('image')->store('product_images', 'public');
            } else {
                return back()->withInput()->with('error', 'File gambar tidak valid.');
            }
        }

        Product::create($validatedData);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load('category');
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id),
            ],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $validatedData['image'] = $request->file('image')->store('product_images', 'public');
            } else {
                return back()->withInput()->with('error', 'File gambar tidak valid.');
            }
        } else {
            $validatedData['image'] = $product->image;
        }

        $product->update($validatedData);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * API endpoint to get all products with their categories.
     * Used by the POS frontend.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex()
    {
        \Log::info('ProductController@apiIndex called.');
        $products = Product::with('category:id,name')->get(['id', 'name', 'sku', 'price', 'stock', 'image', 'category_id']);
        \Log::info('Products retrieved for API:', ['count' => $products->count(), 'data' => $products->toArray()]);
        return response()->json($products);
    }
}
