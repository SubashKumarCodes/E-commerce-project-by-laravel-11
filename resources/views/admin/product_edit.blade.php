@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Edit Product</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <a href="{{ route('admin.products') }}">
                            <div class="text-tiny">Products</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><div class="text-tiny">Edit product</div></li>
                </ul>
            </div>

            <!-- form-edit-product -->
            <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action="{{ route('admin.product.update',$product->id) }}">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" value="{{ $product->id }}">
                <div class="wg-box">
                    <fieldset class="name">
                        <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                        <input type="text" name="name" value="{{ old('name',$product->name) }}" class="mb-10" required>
                    </fieldset>
                    @error('name') <span class="alert alert-danger">{{ $message }}</span> @enderror

                    <fieldset class="name">
                        <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                        <input type="text" name="slug" value="{{ old('slug',$product->slug) }}" class="mb-10" required>
                    </fieldset>
                    @error('slug') <span class="alert alert-danger">{{ $message }}</span> @enderror

                    <div class="gap22 cols">
                        <fieldset class="category">
                            <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                            <div class="select">
                                <select name="category_id">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id==$category->id ? 'selected':'' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>

                        <fieldset class="brand">
                            <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                            <div class="select">
                                <select name="brand_id">
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $product->brand_id==$brand->id ? 'selected':'' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                    </div>

                    <fieldset class="shortdescription">
                        <div class="body-title mb-10">Short Description</div>
                        <textarea name="short_description" class="mb-10 ht-150">{{ old('short_description',$product->short_description) }}</textarea>
                    </fieldset>

                    <fieldset class="description">
                        <div class="body-title mb-10">Description</div>
                        <textarea name="description" class="mb-10">{{ old('description',$product->description) }}</textarea>
                    </fieldset>
                </div>

                <div class="wg-box">
                    <fieldset>
                        <div class="body-title">Upload image</div>
                        <div class="upload-image flex-grow">
                            <div class="item" id="imgpreview">
                                <img src="{{ asset('uploads/products/thumbnails/'.$product->image) }}" alt="">
                            </div>
                            <div id="upload-file" class="item up-load">
                                <label class="uploadfile" for="myFile">
                                    <span class="icon"><i class="icon-upload-cloud"></i></span>
                                    <span class="body-text">Drop or <span class="tf-color">click</span></span>
                                    <input type="file" id="myFile" name="image" accept="image/*">
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="body-title mb-10">Upload Gallery Images</div>
                        <div class="upload-image mb-16" id="galUpload">
                            <label class="uploadfile" for="gFile">
                                <span class="icon"><i class="icon-upload-cloud"></i></span>
                                <span class="text-tiny">Drop or <span class="tf-color">click</span></span>
                                <input type="file" id="gFile" name="images[]" accept="image/*" multiple>
                            </label>

                            {{-- show old gallery images --}}
                            @if($product->images)
                                @foreach(explode(',',$product->images) as $gimg)
                                    <div class="item gitems">
                                        <img src="{{ asset('uploads/products/thumbnails/'.$gimg) }}" alt="">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </fieldset>

                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Regular Price</div>
                            <input type="text" name="regular_price" value="{{ old('regular_price',$product->regular_price) }}">
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Sale Price</div>
                            <input type="text" name="sale_price" value="{{ old('sale_price',$product->sale_price) }}">
                        </fieldset>
                    </div>

                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">SKU</div>
                            <input type="text" name="SKU" value="{{ old('SKU',$product->SKU) }}">
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Quantity</div>
                            <input type="text" name="quantity" value="{{ old('quantity',$product->quantity) }}">
                        </fieldset>
                    </div>

                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Stock</div>
                            <div class="select">
                                <select name="stock_status">
                                    <option value="instock" {{ $product->stock_status=='instock'?'selected':'' }}>In Stock</option>
                                    <option value="outofstock" {{ $product->stock_status=='outofstock'?'selected':'' }}>Out of Stock</option>
                                </select>
                            </div>
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Featured</div>
                            <div class="select">
                                <select name="featured">
                                    <option value="0" {{ $product->featured==0?'selected':'' }}>No</option>
                                    <option value="1" {{ $product->featured==1?'selected':'' }}>Yes</option>
                                </select>
                            </div>
                        </fieldset>
                    </div>

                    <div class="cols gap10">
                        <button class="tf-button w-full" type="submit">Update product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function(){
        $("#myFile").on("change", function(){
            const [file] = this.files;
            if(file){
                $("#imgpreview img").attr('src', URL.createObjectURL(file));
            }
        });

        $("#gFile").on("change", function(){
            $("#galUpload .gitems").remove();
            const files = this.files;
            $.each(files, function(index, file){
                const imgURL = URL.createObjectURL(file);
                $("#galUpload").append(`
                    <div class="item gitems"><img src="${imgURL}" /></div>
                `);
            });
        });
    });
</script>
@endpush
