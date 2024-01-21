<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        //get data products
        $products = DB::table('products')
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            //sort by created_at desc
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.products.index', compact('products'));
    }

    public function create()
    {
        return view('pages.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|unique:products',
            // 'description' => 'required|min:10',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'category' => 'required|in:food,drink,snack',
            'image' => 'required|image|mimes:png,jpg,jpeg,webp'
        ]);

        $filename = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/products', $filename);
        $data = $request->all();

        $product = new \App\Models\Product;
        $product->name = $request->name;
        $product->price = (int) $request->price;
        $product->stock = (int) $request->stock;
        $product->category = $request->category;
        $product->image = $filename;
        $product->save();

        return redirect()->route('product.index')->with('success', 'Product successfully created');
    }

    public function edit($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        return view('pages.products.edit', compact('product'));
    }

    // public function update(Request $request, $id)
    // {
    //     $data = $request->all();
    //     $product = \App\Models\Product::findOrFail($id);
    //     $product->update($data);
    //     return redirect()->route('product.index')->with('success', 'Product successfully updated');
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'name' => 'required|min:3|unique:products,name,' . $id,
    //         'image' => 'image|mimes:png,jpg,jpeg'
    //     ]);

    //     $product = \App\Models\Product::findOrFail($id);

    //     if ($request->hasFile('image')) {
    //         Storage::delete('public/products/' . $product->image);
    //         $filename = time() . '.' . $request->image->extension();
    //         $request->image->storeAs('public/products', $filename);
    //         $product->update([
    //             'name' => $request->name,
    //             'description' => $request->description,
    //             'price' => $request->price,
    //             'stock' => $request->stock,
    //             'category' => $request->category,
    //             'image' => $filename,
    //         ]);
    //     } else {
    //         $product->update([
    //             'name' => $request->name,
    //             'description' => $request->description,
    //             'price' => $request->price,
    //             'stock' => $request->stock,
    //             'category' => $request->category,
    //         ]);
    //     }
    //     return redirect()->route('product.index')->with('success', 'product berhasil diupdate');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3|unique:products,name,' . $id,
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'category' => 'required|in:food,drink,snack',
            'image' => 'image|mimes:png,jpg,jpeg,webp'
        ]);

        $imagePath = Product::find($id)->image;

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists('products/' . $imagePath)) {
                Storage::disk('public')->delete('products/' . $imagePath);
            }
            $imagePath = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $imagePath);
        }

        $data = $request->all();
        $product = Product::findOrFail($id);
        $data['image'] = $imagePath;
        $product->update($data);
        return redirect()->route('product.index')->with('success', 'Product successfully updated');
    }

    // public function destroy($id)
    // {
    //     $product = \App\Models\Product::findOrFail($id);
    //     $product->delete();
    //     return redirect()->route('product.index')->with('success', 'Product successfully deleted');
    // }
    public function destroy($id)
    {
        $product = \App\Models\Product::findOrFail($id);

        // Hapus gambar dari direktori
        if (Storage::disk('public')->exists('products/' . $product->image)) {
            Storage::disk('public')->delete('products/' . $product->image);
        }

        // Hapus entitas produk dari database
        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product successfully deleted');
    }
}
