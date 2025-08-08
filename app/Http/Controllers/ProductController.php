<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Manufacturer;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // เพิ่มการ import Log facade

class ProductController extends Controller
{
    // เมธอดสำหรับตรวจสอบสิทธิ์การเข้าถึง (ใช้ซ้ำๆ ได้)
    private function authorizeStaffAccess()
    {
        if (Auth::user()->role === 'staff') {
            return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้');
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()

    // --- ส่วนนี้คือการทดสอบ Error 500 ---
        // เมื่อคุณต้องการทดสอบหน้า 500 ที่สร้างขึ้น ให้ uncomment บรรทัดนี้
        // และตรวจสอบให้แน่ใจว่า APP_DEBUG=false ในไฟล์ .env ของคุณ
        // throw new \Exception('Test 500 Error: นี่คือข้อผิดพลาดจำลองเพื่อทดสอบหน้า Error 500 ของคุณ');
        // ------------------------------------

    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        // โหลดความสัมพันธ์ของหมวดหมู่และผู้จำหน่าย
        $products = Product::with(['category', 'supplier', 'manufacturer', 'productType'])->orderBy('name')->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $manufacturers = Manufacturer::where('status', 'active')->orderBy('name')->get();
        $productTypes = ProductType::where('status', 'active')->orderBy('name')->get();
        return view('products.create', compact('categories', 'suppliers', 'manufacturers', 'productTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('ProductController@store: Request received.'); // เพิ่ม Log
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'product_type_id' => 'required|exists:product_types,id',
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:50|unique:products,product_code',
            'unit' => 'required|string|max:50',
            'minimum_stock_level' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'cost_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'category_id.required' => 'กรุณาเลือกหมวดหมู่สินค้า',
            'category_id.exists' => 'หมวดหมู่สินค้าไม่ถูกต้อง',
            'supplier_id.required' => 'กรุณาเลือกผู้จำหน่าย',
            'supplier_id.exists' => 'ผู้จำหน่ายไม่ถูกต้อง',
            'manufacturer_id.required' => 'กรุณาเลือกผู้ผลิต',
            'manufacturer_id.exists' => 'ผู้ผลิตไม่ถูกต้อง',
            'product_type_id.required' => 'กรุณาเลือกประเภทสินค้า',
            'product_type_id.exists' => 'ประเภทสินค้าไม่ถูกต้อง',
            'name.required' => 'กรุณากรอกชื่อสินค้า',
            'product_code.required' => 'กรุณากรอกรหัสสินค้า',
            'product_code.unique' => 'รหัสสินค้านี้มีอยู่ในระบบแล้ว',
            'unit.required' => 'กรุณากรอกหน่วยนับ',
            'minimum_stock_level.required' => 'กรุณากรอกระดับสต็อกขั้นต่ำ',
            'minimum_stock_level.integer' => 'ระดับสต็อกขั้นต่ำต้องเป็นตัวเลขจำนวนเต็ม',
            'minimum_stock_level.min' => 'ระดับสต็อกขั้นต่ำต้องไม่น้อยกว่า 0',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
            'cost_price.required' => 'กรุณากรอกราคาต้นทุน',
            'cost_price.numeric' => 'ราคาต้นทุนต้องเป็นตัวเลข',
            'cost_price.min' => 'ราคาต้นทุนต้องไม่น้อยกว่า 0',
        ]);

        $productData = $request->except('image');

        Log::info('ProductController@store: Checking for image file.'); // เพิ่ม Log
        // จัดการการอัปโหลดรูปภาพ
        if ($request->hasFile('image')) {
            Log::info('ProductController@store: Image file found.'); // เพิ่ม Log
            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('products'); // เก็บรูปภาพใน storage/app/public/products
            $productData['image_path'] = str_replace('public/', '', $imagePath); // เก็บพาธที่ไม่มี public/ ในฐานข้อมูล
            Log::info('ProductController@store: Image stored at: ' . $imagePath); // เพิ่ม Log
            Log::info('ProductController@store: Image original name: ' . $imageFile->getClientOriginalName()); // เพิ่ม Log
            Log::info('ProductController@store: Image MIME type: ' . $imageFile->getMimeType()); // เพิ่ม Log
            Log::info('ProductController@store: Image size: ' . $imageFile->getSize() . ' bytes'); // เพิ่ม Log
        } else {
            Log::info('ProductController@store: No image file found in request.'); // เพิ่ม Log
        }

        Product::create($productData);
        Log::info('ProductController@store: Product created successfully.'); // เพิ่ม Log
        return redirect()->route('products.index')->with('success', 'เพิ่มสินค้าใหม่เรียบร้อยแล้ว!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $manufacturers = Manufacturer::where('status', 'active')->orderBy('name')->get();
        $productTypes = ProductType::where('status', 'active')->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories', 'suppliers', 'manufacturers', 'productTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        Log::info('ProductController@update: Request received for product ID: ' . $product->id); // เพิ่ม Log
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'product_type_id' => 'required|exists:product_types,id',
            'name' => 'required|string|max:255',
            'product_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products')->ignore($product->id, 'id'),
            ],
            'unit' => 'required|string|max:50',
            'minimum_stock_level' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'cost_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'category_id.required' => 'กรุณาเลือกหมวดหมู่สินค้า',
            'category_id.exists' => 'หมวดหมู่สินค้าไม่ถูกต้อง',
            'supplier_id.required' => 'กรุณาเลือกผู้จำหน่าย',
            'supplier_id.exists' => 'ผู้จำหน่ายไม่ถูกต้อง',
            'manufacturer_id.required' => 'กรุณาเลือกผู้ผลิต',
            'manufacturer_id.exists' => 'ผู้ผลิตไม่ถูกต้อง',
            'product_type_id.required' => 'กรุณาเลือกประเภทสินค้า',
            'product_type_id.exists' => 'ประเภทสินค้าไม่ถูกต้อง',
            'name.required' => 'กรุณากรอกชื่อสินค้า',
            'product_code.required' => 'กรุณากรอกรหัสสินค้า',
            'product_code.unique' => 'รหัสสินค้านี้มีอยู่ในระบบแล้ว',
            'unit.required' => 'กรุณากรอกหน่วยนับ',
            'minimum_stock_level.required' => 'กรุณากรอกระดับสต็อกขั้นต่ำ',
            'minimum_stock_level.integer' => 'ระดับสต็อกขั้นต่ำต้องเป็นตัวเลขจำนวนเต็ม',
            'minimum_stock_level.min' => 'ระดับสต็อกขั้นต่ำต้องไม่น้อยกว่า 0',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
            'cost_price.required' => 'กรุณากรอกราคาต้นทุน',
            'cost_price.numeric' => 'ราคาต้นทุนต้องเป็นตัวเลข',
            'cost_price.min' => 'ราคาต้นทุนต้องไม่น้อยกว่า 0',
        ]);

        $productData = $request->except('image', 'remove_image'); // แยกข้อมูลรูปภาพและ remove_image ออกไปก่อน

        Log::info('ProductController@update: Checking for image file or remove_image flag.'); // เพิ่ม Log
        // จัดการการอัปโหลดรูปภาพใหม่
        if ($request->hasFile('image')) {
            Log::info('ProductController@update: New image file found.'); // เพิ่ม Log
            // ลบรูปภาพเก่าถ้ามี
            if ($product->image_path) {
                Log::info('ProductController@update: Deleting old image: ' . $product->image_path); // เพิ่ม Log
                Storage::disk('public')->delete($product->image_path);
            }
            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('products'); // เก็บรูปภาพใหม่
            $productData['image_path'] = str_replace('public/', '', $imagePath);
            Log::info('ProductController@update: New image stored at: ' . $imagePath); // เพิ่ม Log
            Log::info('ProductController@update: New image original name: ' . $imageFile->getClientOriginalName()); // เพิ่ม Log
            Log::info('ProductController@update: New image MIME type: ' . $imageFile->getMimeType()); // เพิ่ม Log
            Log::info('ProductController@update: New image size: ' . $imageFile->getSize() . ' bytes'); // เพิ่ม Log
        } elseif ($request->input('remove_image')) { // ตรวจสอบว่าผู้ใช้ต้องการลบรูปภาพหรือไม่
            Log::info('ProductController@update: Remove image flag detected.'); // เพิ่ม Log
            if ($product->image_path) {
                Log::info('ProductController@update: Deleting old image due to remove flag: ' . $product->image_path); // เพิ่ม Log
                Storage::disk('public')->delete($product->image_path);
            }
            $productData['image_path'] = null;
            Log::info('ProductController@update: Image path set to null.'); // เพิ่ม Log
        } else {
            // ถ้าไม่มีการอัปโหลดรูปภาพใหม่ และไม่ได้เลือก 'remove_image'
            // ให้คงค่า image_path เดิมไว้
            $productData['image_path'] = $product->image_path;
            Log::info('ProductController@update: No new image or remove flag. Keeping existing image path.'); // เพิ่ม Log
        }


        $product->update($productData);
        Log::info('ProductController@update: Product updated successfully.'); // เพิ่ม Log
        return redirect()->route('products.index')->with('success', 'อัปเดตสินค้าเรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        try {
            // ลบรูปภาพที่เกี่ยวข้องก่อนลบสินค้า
            if ($product->image_path) {
                Log::info('ProductController@destroy: Deleting image: ' . $product->image_path); // เพิ่ม Log
                Storage::disk('public')->delete($product->image_path);
            }
            $product->delete();
            Log::info('ProductController@destroy: Product deleted successfully.'); // เพิ่ม Log
            return redirect()->route('products.index')->with('success', 'ลบสินค้าเรียบร้อยแล้ว!');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('ProductController@destroy: Database error: ' . $e->getMessage()); // เพิ่ม Log
            if ($e->getCode() == "23000") {
                return redirect()->route('products.index')->with('error', 'ไม่สามารถลบสินค้านี้ได้ เนื่องจากมีข้อมูลอื่นที่เกี่ยวข้องอยู่ (เช่น ล็อตสินค้า หรือรายการสต็อก)');
            }
            return redirect()->route('products.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
