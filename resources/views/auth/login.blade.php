<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ระบบสต็อกยาและเวชภัณฑ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #EEF2F7; min-height: 100vh; display: flex; flex-direction: column; }

        /* Topbar */
        .topbar {
            background: #0C447C;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            flex-shrink: 0;
        }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand-pill {
            background: #185FA5;
            border-radius: 8px;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .brand-name { font-size: 14px; font-weight: 500; color: white; }
        .topbar-right { font-size: 11px; color: rgba(255,255,255,0.5); display: flex; align-items: center; gap: 6px; }

        /* Hero */
        .hero {
            background: #185FA5;
            padding: 28px 24px 56px;
            text-align: center;
            flex-shrink: 0;
        }
        .hero-title { font-size: 22px; font-weight: 500; color: white; margin-bottom: 6px; }
        .hero-sub { font-size: 12px; color: rgba(255,255,255,0.6); margin-bottom: 14px; }
        .hero-badges { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255,255,255,0.12);
            border: 0.5px solid rgba(255,255,255,0.2);
            border-radius: 99px;
            padding: 4px 12px;
            font-size: 11px;
            color: rgba(255,255,255,0.8);
        }

        /* Content */
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 0 24px 32px;
            margin-top: -32px;
        }

        /* Card */
        .login-card {
            background: white;
            border-radius: 16px;
            border: 0.5px solid #D3D1C7;
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        .card-header-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 24px;
            border-bottom: 0.5px solid #F0EDE6;
            background: #FAFAF8;
        }
        .card-icon {
            width: 42px; height: 42px;
            background: #E6F1FB;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .card-title { font-size: 16px; font-weight: 500; color: #0C447C; }
        .card-sub { font-size: 11px; color: #888780; margin-top: 2px; }
        .card-body-custom { padding: 24px; }

        /* Form */
        .form-group { margin-bottom: 16px; }
        .form-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .form-label-text { font-size: 12px; font-weight: 500; color: #2C2C2A; }
        .form-label-link { font-size: 11px; color: #185FA5; text-decoration: none; }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute;
            left: 11px; top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #B4B2A9;
        }
        .form-input {
            width: 100%;
            height: 42px;
            border: 0.5px solid #D3D1C7;
            border-radius: 8px;
            padding: 0 11px 0 36px;
            font-size: 13px;
            color: #2C2C2A;
            outline: none;
            background: #FAFAF8;
            transition: border-color 0.15s;
        }
        .form-input:focus { border-color: #185FA5; background: white; }
        .form-input.is-invalid { border-color: #A32D2D; }
        .eye-btn {
            position: absolute;
            right: 10px; top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #B4B2A9;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }
        .check-row {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            color: #5F5E5A;
            margin-bottom: 20px;
        }
        .submit-btn {
            width: 100%;
            height: 44px;
            background: #185FA5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: background 0.15s;
        }
        .submit-btn:hover { background: #0C447C; }

        /* Card footer */
        .card-footer-custom {
            padding: 14px 24px;
            background: #F7F5EF;
            border-top: 0.5px solid #E0DDD5;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .security-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: #888780;
        }

        /* Bottom stats */
        .bottom-stats {
            display: flex;
            gap: 0;
            margin-top: 16px;
            padding-top: 14px;
            border-top: 0.5px solid #F0EDE6;
        }
        .bstat { flex: 1; text-align: center; }
        .bstat-val { font-size: 16px; font-weight: 500; color: #185FA5; }
        .bstat-label { font-size: 10px; color: #888780; margin-top: 2px; }
        .bstat-div { width: 0.5px; background: #F0EDE6; }

        /* Alert */
        .alert-custom {
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .alert-danger-custom { background: #FCEBEB; color: #791F1F; border: 0.5px solid #F7C1C1; }
        .alert-success-custom { background: #EAF3DE; color: #27500A; border: 0.5px solid #C0DD97; }

        /* Footer */
        .page-footer {
            text-align: center;
            padding: 12px;
            font-size: 10px;
            color: #B4B2A9;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

{{-- Topbar --}}
<div class="topbar">
    <div class="brand">
        <div class="brand-pill">
            <i class="fas fa-hospital-alt" style="color:white;font-size:15px;"></i>
            <span class="brand-name">ระบบสต็อกยาและเวชภัณฑ์โรงพยาบาล</span>
        </div>
    </div>
    <div class="topbar-right">
        <i class="fas fa-circle" style="font-size:7px;color:#9FE1CB;"></i>
        ระบบออฟไลน์
    </div>
</div>

{{-- Hero --}}
<div class="hero">
    <div class="hero-title">ยินดีต้อนรับ</div>
    <div class="hero-sub">โรงพยาบาลวัดห้วยปลากั้งเพื่อสังคม · โดยมูลนิธิพระอาจารย์พบโชคเพื่อสังคม</div>
    <div class="hero-badges">
        <div class="hero-badge"><i class="fas fa-boxes" style="font-size:11px;"></i> จัดการสต็อก</div>
        <div class="hero-badge"><i class="fas fa-file-invoice" style="font-size:11px;"></i> ใบขอเบิก</div>
        <div class="hero-badge"><i class="fas fa-chart-bar" style="font-size:11px;"></i> รายงาน</div>
    </div>
</div>

{{-- Content --}}
<div class="content">
    <div class="login-card">
        <div class="card-header-custom">
            <div class="card-icon">
                <i class="fas fa-lock" style="color:#185FA5;font-size:18px;"></i>
            </div>
            <div>
                <div class="card-title">เข้าสู่ระบบ</div>
                <div class="card-sub">กรุณากรอกข้อมูลเพื่อเข้าใช้งาน</div>
            </div>
        </div>

        <div class="card-body-custom">
            {{-- Error --}}
            @if ($errors->any())
            <div class="alert-custom alert-danger-custom">
                <i class="fas fa-exclamation-circle" style="margin-top:1px;flex-shrink:0;"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Success --}}
            @if (session('success'))
            <div class="alert-custom alert-success-custom">
                <i class="fas fa-check-circle" style="margin-top:1px;flex-shrink:0;"></i>
                <div>{{ session('success') }}</div>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                {{-- Username --}}
                <div class="form-group">
                    <div class="form-label-row">
                        <span class="form-label-text">ชื่อผู้ใช้งาน</span>
                    </div>
                    <div class="input-wrap">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text"
                               class="form-input {{ $errors->has('username') ? 'is-invalid' : '' }}"
                               name="username"
                               value="{{ old('username') }}"
                               placeholder="กรอกชื่อผู้ใช้งาน"
                               required autofocus>
                    </div>
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <div class="form-label-row">
                        <span class="form-label-text">รหัสผ่าน</span>
                    </div>
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password"
                               class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               id="password"
                               name="password"
                               placeholder="กรอกรหัสผ่าน"
                               required>
                        <button type="button" class="eye-btn" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember --}}
                <div class="check-row">
                    <input type="checkbox"
                           id="remember"
                           name="remember"
                           style="width:14px;height:14px;accent-color:#185FA5;">
                    <label for="remember">จดจำการเข้าสู่ระบบ</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt" style="font-size:14px;"></i>
                    เข้าสู่ระบบ
                </button>
            </form>

            {{-- Bottom Stats --}}
            <div class="bottom-stats">
                <div class="bstat">
                    <div class="bstat-val">1,248+</div>
                    <div class="bstat-label">รายการสินค้า</div>
                </div>
                <div class="bstat-div"></div>
                <div class="bstat">
                    <div class="bstat-val">11</div>
                    <div class="bstat-label">แผนก</div>
                </div>
                <div class="bstat-div"></div>
                <div class="bstat">
                    <div class="bstat-val">24</div>
                    <div class="bstat-label">ผู้จัดจำหน่าย</div>
                </div>
                <div class="bstat-div"></div>
                <div class="bstat">
                    <div class="bstat-val">8</div>
                    <div class="bstat-label">ผู้ใช้งาน</div>
                </div>
            </div>
        </div>

        <div class="card-footer-custom">
            <div class="security-badge">
                <i class="fas fa-shield-alt" style="color:#3B6D11;font-size:12px;"></i>
                ระบบปลอดภัย
            </div>
            <div class="security-badge">
                เข้าใช้งานได้เฉพาะผู้ได้รับสิทธิ์
            </div>
        </div>
    </div>
</div>

<div class="page-footer">
    © 2569 มูลนิธิพระอาจารย์พบโชคเพื่อสังคม · โรงพยาบาลวัดห้วยปลากั้งเพื่อสังคม
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
</body>
</html>