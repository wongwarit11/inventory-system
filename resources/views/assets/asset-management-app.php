@extends('layouts.app')

@section('title', 'ระบบจัดการครุภัณฑ์')

@section('content')
<div class="d-flex justify-content-center align-items-center mb-4">
    <h1 class="text-primary fw-bold mb-0">
        <img src="{{ asset('images/hpk_logo.png') }}" alt="Your logo" style="height: 45px; margin-right: 10px; vertical-align: middle;">
        ระบบจัดการครุภัณฑ์ <span class="text-secondary">โรงพยาบาลห้วยปลากั้งเพื่อสังคม</span>
    </h1>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="bg-white rounded-xl shadow-2xl p-8 max-w-5xl w-full">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">ระบบจัดการครุภัณฑ์</h1>
        <p class="text-gray-500 mt-2">เพิ่ม แก้ไข ลบ ยืม และคืนครุภัณฑ์</p>
    </div>

    <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">เพิ่มครุภัณฑ์ใหม่</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="newAssetId" class="block text-sm font-medium text-gray-700">รหัสครุภัณฑ์ (Asset ID)</label>
                <input type="text" id="newAssetId" placeholder="กรอกรหัสครุภัณฑ์"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
            </div>
            <div class="md:col-span-2">
                <label for="newAssetName" class="block text-sm font-medium text-gray-700">ชื่อครุภัณฑ์ (Asset Name)</label>
                <input type="text" id="newAssetName" placeholder="กรอกชื่อครุภัณฑ์"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
            </div>
            <div class="md:col-span-1">
                <label for="newAssetDepartment" class="block text-sm font-medium text-gray-700">แผนก (Department)</label>
                <select id="newAssetDepartment"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                </select>
            </div>
            <div class="md:col-span-3">
                <label for="newAssetDescription" class="block text-sm font-medium text-gray-700">รายละเอียด (Description)</label>
                <textarea id="newAssetDescription" placeholder="กรอกรายละเอียดครุภัณฑ์" rows="2"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
            </div>
            <div class="md:col-span-1">
                <button id="addAssetBtn"
                    class="w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-lg shadow-lg transform transition duration-300 ease-in-out hover:scale-105">
                    <i class="fas fa-plus mr-1"></i> เพิ่ม
                </button>
            </div>
        </div>
    </div>

    <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">ยืม-คืน ครุภัณฑ์</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="assetId" class="block text-sm font-medium text-gray-700">รหัสครุภัณฑ์ (Asset ID)</label>
                <input type="text" id="assetId" placeholder="กรอกรหัสครุภัณฑ์"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div>
                <label for="userId" class="block text-sm font-medium text-gray-700">รหัสผู้ใช้งาน (User ID)</label>
                <input type="text" id="userId" placeholder="กรอกรหัสผู้ใช้งาน"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
        </div>
        <div class="flex justify-center mt-6 space-x-4">
            <button id="borrowBtn"
                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg shadow-lg transform transition duration-300 ease-in-out hover:scale-105 flex items-center">
                <i class="fas fa-hand-holding-usd mr-2"></i>
                ยืมครุภัณฑ์
            </button>
            <button id="returnBtn"
                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow-lg transform transition duration-300 ease-in-out hover:scale-105 flex items-center">
                <i class="fas fa-undo mr-2"></i>
                คืนครุภัณฑ์
            </button>
        </div>
    </div>

    <div id="statusMessage" class="text-center text-sm font-medium text-gray-700 mb-6">
    </div>

    <div>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">รายการครุภัณฑ์ทั้งหมด</h2>
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">รหัสครุภัณฑ์</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">ชื่อครุภัณฑ์</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">รายละเอียด</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">แผนก</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">สถานะ</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">ผู้ยืม</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="assetList" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500">กำลังโหลดข้อมูล...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">แก้ไขครุภัณฑ์</h3>
        <div class="mb-4">
            <label for="editAssetName" class="block text-sm font-medium text-gray-700">ชื่อครุภัณฑ์ใหม่</label>
            <input type="text" id="editAssetName"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500">
        </div>
        <div class="mb-4">
            <label for="editAssetDescription" class="block text-sm font-medium text-gray-700">รายละเอียดใหม่</label>
            <textarea id="editAssetDescription" rows="2"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"></textarea>
        </div>
        <div class="mb-4">
            <label for="editAssetDepartment" class="block text-sm font-medium text-gray-700">แผนกใหม่</label>
            <select id="editAssetDepartment"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500">
            </select>
        </div>
        <div class="flex justify-end space-x-2">
            <button id="saveEditBtn"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-300 ease-in-out">
                บันทึก
            </button>
            <button id="cancelEditBtn"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg shadow transition duration-300 ease-in-out">
                ยกเลิก
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const assetListElement = document.getElementById('assetList');
        const newAssetIdInput = document.getElementById('newAssetId');
        const newAssetNameInput = document.getElementById('newAssetName');
        const newAssetDescriptionInput = document.getElementById('newAssetDescription');
        const newAssetDepartmentSelect = document.getElementById('newAssetDepartment');
        const addAssetBtn = document.getElementById('addAssetBtn');
        const assetIdInput = document.getElementById('assetId');
        const userIdInput = document.getElementById('userId');
        const borrowBtn = document.getElementById('borrowBtn');
        const returnBtn = document.getElementById('returnBtn');
        const statusMessage = document.getElementById('statusMessage');
        const editModal = document.getElementById('editModal');
        const editAssetNameInput = document.getElementById('editAssetName');
        const editAssetDescriptionInput = document.getElementById('editAssetDescription');
        const editAssetDepartmentSelect = document.getElementById('editAssetDepartment');
        const saveEditBtn = document.getElementById('saveEditBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        let currentAssetId = null;
        let departments = [];

        const API_BASE_URL = '/api/assets';

        const showMessage = (message, colorClass) => {
            statusMessage.textContent = message;
            statusMessage.className = `text-center text-sm font-medium mb-6 ${colorClass}`;
            setTimeout(() => {
                statusMessage.textContent = '';
                statusMessage.className = `text-center text-sm font-medium text-gray-700 mb-6`;
            }, 5000);
        };

        const fetchDepartments = async () => {
            try {
                const response = await fetch('/api/departments');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                departments = await response.json();

                newAssetDepartmentSelect.innerHTML = '';
                editAssetDepartmentSelect.innerHTML = '';

                departments.forEach(dept => {
                    const newOption = document.createElement('option');
                    newOption.value = dept.id;
                    newOption.textContent = dept.name;
                    newAssetDepartmentSelect.appendChild(newOption);

                    const editOption = document.createElement('option');
                    editOption.value = dept.id;
                    editOption.textContent = dept.name;
                    editAssetDepartmentSelect.appendChild(editOption);
                });

            } catch (error) {
                console.error('Error fetching departments:', error);
                showMessage('ไม่สามารถดึงข้อมูลแผนกได้', 'text-red-600');
            }
        };

        const fetchAssets = async () => {
            assetListElement.innerHTML = `<tr><td colspan="7" class="p-6 text-center text-gray-500">กำลังโหลดข้อมูล...</td></tr>`;
            try {
                const response = await fetch(API_BASE_URL);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const assets = await response.json();

                assetListElement.innerHTML = '';

                if (assets.length === 0) {
                    assetListElement.innerHTML = `<tr><td colspan="7" class="p-6 text-center text-gray-500">ไม่พบรายการครุภัณฑ์</td></tr>`;
                } else {
                    assets.forEach(asset => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${asset.id}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${asset.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${asset.description || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${asset.department_name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${asset.is_borrowed ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                                    ${asset.is_borrowed ? 'ถูกยืมแล้ว' : 'ว่าง'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${asset.borrowed_by || ' - '}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button data-action="edit" data-id="${asset.id}" data-name="${asset.name}" data-description="${asset.description || ''}" data-department-id="${asset.department_id}" class="text-yellow-600 hover:text-yellow-900 mx-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button data-action="delete" data-id="${asset.id}" class="text-red-600 hover:text-red-900 mx-2">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        `;
                        assetListElement.appendChild(row);
                    });
                }
            } catch (error) {
                console.error('Error fetching assets:', error);
                showMessage('เกิดข้อผิดพลาดในการดึงข้อมูลครุภัณฑ์', 'text-red-600');
            }
        };

        const editAsset = (id, name, description, departmentId) => {
            currentAssetId = id;
            editAssetNameInput.value = name;
            editAssetDescriptionInput.value = description;
            editAssetDepartmentSelect.value = departmentId;
            editModal.classList.remove('hidden');
        };

        const deleteAsset = async (id) => {
            if (!confirm('คุณต้องการลบครุภัณฑ์นี้ใช่หรือไม่?')) return;

            try {
                const response = await fetch(`${API_BASE_URL}/${id}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                });
                const result = await response.json();
                if (response.ok) {
                    showMessage('ลบครุภัณฑ์สำเร็จ!', 'text-green-600');
                    fetchAssets();
                } else {
                    throw new Error(result.message || 'การลบครุภัณฑ์ล้มเหลว');
                }
            } catch (error) {
                showMessage(`เกิดข้อผิดพลาด: ${error.message}`, 'text-red-600');
            }
        };

        const handleAddAsset = async () => {
            const id = newAssetIdInput.value.trim();
            const name = newAssetNameInput.value.trim();
            const description = newAssetDescriptionInput.value.trim();
            const departmentId = newAssetDepartmentSelect.value;
            if (!id || !name || !departmentId) {
                showMessage('กรุณากรอกข้อมูลให้ครบถ้วน', 'text-red-600');
                return;
            }

            try {
                const response = await fetch(API_BASE_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, name, description, department_id: departmentId })
                });
                const result = await response.json();
                if (response.ok) {
                    showMessage('เพิ่มครุภัณฑ์สำเร็จ!', 'text-green-600');
                    newAssetIdInput.value = '';
                    newAssetNameInput.value = '';
                    newAssetDescriptionInput.value = '';
                    fetchAssets();
                } else {
                    throw new Error(result.message || 'การเพิ่มครุภัณฑ์ล้มเหลว');
                }
            } catch (error) {
                showMessage(`เกิดข้อผิดพลาด: ${error.message}`, 'text-red-600');
            }
        };

        const handleSaveEdit = async () => {
            const newName = editAssetNameInput.value.trim();
            const newDescription = editAssetDescriptionInput.value.trim();
            const newDepartmentId = editAssetDepartmentSelect.value;
            if (!newName || !newDepartmentId || !currentAssetId) {
                showMessage('กรุณากรอกข้อมูลให้ครบถ้วน', 'text-red-600');
                return;
            }

            try {
                const response = await fetch(`${API_BASE_URL}/${currentAssetId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: newName, description: newDescription, department_id: newDepartmentId })
                });
                const result = await response.json();
                if (response.ok) {
                    showMessage('แก้ไขครุภัณฑ์สำเร็จ!', 'text-green-600');
                    editModal.classList.add('hidden');
                    fetchAssets();
                } else {
                    throw new Error(result.message || 'การแก้ไขล้มเหลว');
                }
            } catch (error) {
                showMessage(`เกิดข้อผิดพลาด: ${error.message}`, 'text-red-600');
            }
        };

        const handleBorrow = async () => {
            const assetId = assetIdInput.value.trim();
            const userId = userIdInput.value.trim();
            if (!assetId || !userId) {
                showMessage('กรุณากรอกรหัสครุภัณฑ์และรหัสผู้ใช้งาน', 'text-red-600');
                return;
            }

            try {
                const response = await fetch(`${API_BASE_URL}/borrow`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ asset_id: assetId, user_id: userId })
                });
                const result = await response.json();
                if (response.ok) {
                    showMessage('ยืมครุภัณฑ์สำเร็จ!', 'text-green-600');
                    fetchAssets();
                } else {
                    throw new Error(result.message || 'การยืมล้มเหลว');
                }
            } catch (error) {
                showMessage(`เกิดข้อผิดพลาด: ${error.message}`, 'text-red-600');
            }
        };

        const handleReturn = async () => {
            const assetId = assetIdInput.value.trim();
            const userId = userIdInput.value.trim();
            if (!assetId || !userId) {
                showMessage('กรุณากรอกรหัสครุภัณฑ์และรหัสผู้ใช้งาน', 'text-red-600');
                return;
            }

            try {
                const response = await fetch(`${API_BASE_URL}/return`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ asset_id: assetId, user_id: userId })
                });
                const result = await response.json();
                if (response.ok) {
                    showMessage('คืนครุภัณฑ์สำเร็จ!', 'text-green-600');
                    fetchAssets();
                } else {
                    throw new Error(result.message || 'การคืนล้มเหลว');
                }
            } catch (error) {
                showMessage(`เกิดข้อผิดพลาด: ${error.message}`, 'text-red-600');
            }
        };

        // Add event listeners
        addAssetBtn.addEventListener('click', handleAddAsset);
        borrowBtn.addEventListener('click', handleBorrow);
        returnBtn.addEventListener('click', handleReturn);
        saveEditBtn.addEventListener('click', handleSaveEdit);
        cancelEditBtn.addEventListener('click', () => editModal.classList.add('hidden'));

        assetListElement.addEventListener('click', (e) => {
            const target = e.target.closest('button');
            if (!target) return;

            const action = target.dataset.action;
            const id = target.dataset.id;

            if (action === 'edit') {
                const name = target.dataset.name;
                const description = target.dataset.description;
                const departmentId = target.dataset.departmentId;
                editAsset(id, name, description, departmentId);
            } else if (action === 'delete') {
                deleteAsset(id);
            }
        });

        fetchDepartments();
        fetchAssets();
    });
</script>
@endsection