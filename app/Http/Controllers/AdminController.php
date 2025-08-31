<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }

    public function brands(){
        $brands = Brand::orderBy('id','DESC')->paginate(10);
        return view('admin.brands',compact('brands'));
    }

    public function add_brand(){
        return view('admin.brand_add');
    }

    public function brand_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();

        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extention;
        $this->GenerateBrandThumbailsImage($image,$file_name);
        $brand->image = $file_name;
        $brand->save();

        return redirect()->route('admin.brands')->with('status','Brand has been added successfully');
    }

    public function GenerateBrandThumbailsImage($image, $imageName){
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124, function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function brand_edit($id){
        $brand = Brand::find($id);
        return view('admin.brand_edit', compact('brand'));
    }

    public function brand_update(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' =>'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:50048'
        ]);

        $brand = Brand::find($request->id);

        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/brands').'/'.$brand->image)){

                File::delete(public_path('uploads/brands').'/'.$brand->image);
            }

            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extention;
            $this->GenerateBrandThumbailsImage($image,$file_name);
            $brand->image = $file_name;
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('status','Brand has been updated successfully');
    }

    public function brand_delete($id){
        $brand = Brand::find($id);

        if(File::exists(public_path('uploads/brands').'/'.$brand->image)){
            File::delete(public_path('uploads/brands').'/'.$brand->image);
        }

        $brand->delete();
        return redirect()->route('admin.brands')->with('status','Brand has been deleted successfully');
    }

    public function categories(){
        $categories = Category::orderBy('id','DESC')->paginate(10);
        return view('admin.categories',compact('categories'));
    }

    public function category_add(){
        return view('admin.category_add');
    }

    public function category_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category();

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extention;
        $this->GenerateCategoryThumbailsImage($image,$file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status','category has been added succesfully');
    }

    public function GenerateCategoryThumbailsImage($image,$imageName){
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function category_edit($id){
        $category = Category::find($id);
        return view('admin.category_edit', compact('category'));
    }

    public function category_update(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' =>'required|unique:categories,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:50048'
        ]);

        $category = Category::find($request->id);

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/categories').'/'.$category->image)){

                File::delete(public_path('uploads/categories').'/'.$category->image);
            }

            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extention;
            $this->GenerateCategoryThumbailsImage($image,$file_name);
            $category->image = $file_name;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status','Category has been updated successfully');
    }

    public function category_delete($id){
        $category = Category::find($id);

        if(File::exists(public_path('uploads/categories').'/'.$category->image)){
            File::delete(public_path('uploads/categories').'/'.$category->image);
        }

        $category->delete();
        return redirect()->route('admin.categories')->with('status','Category has been deleted successfully');
    }

    public function products(){
        $products = Product::orderBy('created_at','DESC')->paginate(10);
        return view('admin.products',compact('products'));
    }

    public function product_add(){
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product_add',compact('categories','brands'));

    }

   public function product_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:50048',
            'category_id' => 'required',
            'brand_id' => 'required'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        // present time
        $current_timestamp = Carbon::now()->timestamp;

        // Main Image Save
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        // Multiple Images Save
        $gallery_arr = [];

        if ($request->hasFile('images')) {
            $allowedfileExtion = ['jpg','png','jpeg'];
            $files = $request->file('images');

            foreach ($files as $index => $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtion);

                if ($gcheck) {
                    // unique name create
                    $gfileName = $current_timestamp . "-" . $index . "." . $gextension;

                    // image resize & save
                    $this->GenerateProductThumbnailImage($file, $gfileName);

                    // name array rakha
                    array_push($gallery_arr, $gfileName);
                }
            }

            // images colume save (comma diye alada kore)
            $product->images = implode(',', $gallery_arr);
        }

        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product has been added successfully');
    }



    public function GenerateProductThumbnailImage($image, $imageName){
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');

        // Image::read()
        $img = Image::read($image->path());

        // create thumbnail
        $img->cover(540, 689, "top")
            ->resize(540, 689, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($destinationPathThumbnail.'/'.$imageName);
    }


    public function product_edit($id){
        $product = Product::find($id);
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product_edit',compact('product','categories','brands'));
    }

    public function product_update(Request $request,){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'.$request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:50048',
            'category_id' => 'required',
            'brand_id' => 'required'
        ]);

        $product = Product::findOrFail($request->id);

        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        // Update main image
        if($request->hasFile('image')){
            // old image delete korbe
            if(File::exists(public_path('uploads/products/thumbnails/'.$product->image))){
                File::delete(public_path('uploads/products/thumbnails/'.$product->image));
            }

            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateProductThumbnailImage($image,$imageName);
            $product->image = $imageName;
        }

        // Update gallery images
        $gallery_arr = [];
        if($request->hasFile('images')){
            // old gallery images delete
            if($product->images){
                foreach(explode(',',$product->images) as $oldimg){
                    if(File::exists(public_path('uploads/products/thumbnails/'.$oldimg))){
                        File::delete(public_path('uploads/products/thumbnails/'.$oldimg));
                    }
                }
            }

            $allowedfileExtion = ['jpg','png','jpeg'];
            $files = $request->file('images');

            foreach($files as $index=>$file){
                $gextension = $file->getClientOriginalExtension();
                if(in_array($gextension,$allowedfileExtion)){
                    $gfileName = $current_timestamp."-".$index.".".$gextension;
                    $this->GenerateProductThumbnailImage($file,$gfileName);
                    array_push($gallery_arr,$gfileName);
                }
            }

            $product->images = implode(',',$gallery_arr);
        }

        $product->save();

        return redirect()->route('admin.products')->with('status','Product updated successfully');
    }


    public function product_delete($id){
        // product khujbe
        $product = Product::findOrFail($id);

        // delete main image
        if($product->image && File::exists(public_path('uploads/products/thumbnails/'.$product->image))){
            File::delete(public_path('uploads/products/thumbnails/'.$product->image));
        }

        // delete gallery image
        if($product->images){
            $galleryImages = explode(',', $product->images);
            foreach($galleryImages as $galleryImage){
                if(File::exists(public_path('uploads/products/thumbnails/'.$galleryImage))){
                    File::delete(public_path('uploads/products/thumbnails/'.$galleryImage));
                }
            }
        }

        // database theke delete
        $product->delete();

        return redirect()->route('admin.products')->with('status', 'Product deleted successfully');
    }


}