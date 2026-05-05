<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Borrow;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    private function authorizeStaffAccess()
    {
        if (Auth::user()->role === 'staff') {
            return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้');
        }
        return null;
    }
    /**
     * แสดงรายการครุภัณฑ์ทั้งหมด
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }

        $assets = Asset::with('department')->get();
        return response()->json($assets);
    }

    public function create()
    {
        if ($response = $this->authorizeStaffAccess()) {
            return $response;
        }
        $assets = Asset::where('status', 'active')->orderBy('name')->get();

        // ดึงข้อมูลผู้จำหน่ายทั้งหมด
        $departments = Department::all();

        // ส่งข้อมูล products และ suppliers ไปยัง view
        return view('assets.create', compact('assets', 'departments'));
    }
    /**
     * สร้างครุภัณฑ์ใหม่
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'serial_number' => 'required|string|unique:assets',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $asset = Asset::create($request->all());

        return response()->json($asset, 201); // 201 Created
    }

    /**
     * จัดการการยืมครุภัณฑ์
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrow(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
        ]);

        // ใช้ Transaction เพื่อให้ข้อมูลสอดคล้องกัน
        DB::beginTransaction();

        try {
            $asset = Asset::lockForUpdate()->find($request->asset_id);

            if ($asset->status !== 'available') {
                DB::rollBack();
                return response()->json(['message' => 'Asset is not available for borrowing.'], 400);
            }

            // อัปเดตสถานะของครุภัณฑ์
            $asset->status = 'borrowed';
            $asset->save();

            // บันทึกข้อมูลการยืม
            Borrow::create([
                'asset_id' => $asset->id,
                'user_id' => $request->user_id,
                'borrowed_at' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Asset borrowed successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    /**
     * จัดการการคืนครุภัณฑ์
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function return(Request $request)
    {
        $request->validate([
            'borrow_id' => 'required|exists:borrows,id',
        ]);

        DB::beginTransaction();

        try {
            $borrow = Borrow::lockForUpdate()->find($request->borrow_id);

            if ($borrow->returned_at !== null) {
                DB::rollBack();
                return response()->json(['message' => 'Asset has already been returned.'], 400);
            }

            $asset = Asset::lockForUpdate()->find($borrow->asset_id);

            // อัปเดตสถานะการยืม
            $borrow->returned_at = now();
            $borrow->save();

            // อัปเดตสถานะของครุภัณฑ์
            $asset->status = 'available';
            $asset->save();

            DB::commit();
            return response()->json(['message' => 'Asset returned successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
}
