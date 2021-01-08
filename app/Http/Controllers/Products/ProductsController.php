<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Section;

class ProductsController extends Controller
{
    public function index(){
        $products = Product::select('id','product_name','description','section_id')->with('section')->get();
        $sections = Section::select('id','section_name')->get();
        return view('products.products',compact('products','sections'));
    }

    public function store(Request $request){
        $data = [];
        $data = [
            'product_name'  => $request->product_name,
            'description'   => $request->description,
            'section_id'    => $request->section_id
        ];
        $validatedData = $request->validate([
            'product_name' => 'required',
            'section_id' => 'required',
        ],[

            'product_name.required' =>'يرجي ادخال اسم المنتج',
            'section_id.required' =>'يرجي ادخال اسم القسم'
        ]);
        $product = Product::create($data);
        session()->flash('Add','تم اضافة المنتج بنجاح');
        return redirect()->route('get.products');
    }

    public function update(Request $request){
        $id = $request->id;
        $section_id = Section::select('id')->where('section_name',$request->section_name)->first()->id;

        $data = [];
        $data = [
            'product_name'  => $request->product_name,
            'description'   => $request->description,
            'section_id'    => $section_id
        ];

        $validatedData = $request->validate([
            'product_name' => 'required',
            'section_id'    => 'required'
        ],[

            'product_name.required' =>'يرجي ادخال اسم المنتج',
            'section_id.required' =>'يرجي ادخال اسم القسم'
        ]);

        $product = Product::find($id);
        $product->update($data);
        session()->flash('edit','تم تعديل القسم بنجاج');
        return redirect()->route('get.products');

    }

    public function destroy(Request $request){
        $id = $request->id;
        $product = Product::find($id);
        $product->delete();
        session()->flash('delete','تم حذف المنتج بنجاح');
        return redirect()->route('get.products');
    }
}
