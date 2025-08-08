    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>500 - ข้อผิดพลาดเซิร์ฟเวอร์ภายใน</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            body {
                background-color: #f8f9fa;
                font-family: 'Inter', sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                color: #343a40;
            }
            .error-container {
                text-align: center;
                background-color: #ffffff;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                max-width: 600px;
                width: 90%;
            }
            .error-code {
                font-size: 8rem;
                font-weight: bold;
                color: #ffc107; /* Bootstrap warning color */
                margin-bottom: 20px;
                line-height: 1;
            }
            .error-message {
                font-size: 2rem;
                color: #6c757d; /* Bootstrap secondary color */
                margin-bottom: 20px;
            }
            .error-description {
                font-size: 1.1rem;
                color: #495057;
                margin-bottom: 30px;
            }
            .btn-home {
                background-color: #007bff; /* Bootstrap primary color */
                color: #ffffff;
                border-radius: 50px;
                padding: 12px 30px;
                font-size: 1.1rem;
                transition: background-color 0.3s ease;
            }
            .btn-home:hover {
                background-color: #0056b3;
                color: #ffffff;
            }
            .icon-large {
                font-size: 6rem;
                color: #ffc107;
                margin-bottom: 15px;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <i class="fas fa-bug icon-large"></i>
            <div class="error-code">500</div>
            <div class="error-message">ข้อผิดพลาดเซิร์ฟเวอร์ภายใน!</div>
            <p class="error-description">
                ขออภัย, เกิดข้อผิดพลาดที่ไม่คาดคิดขึ้นบนเซิร์ฟเวอร์ของเรา.
                ทีมงานของเราได้รับแจ้งปัญหาแล้วและกำลังดำเนินการแก้ไข.
                กรุณาลองใหม่อีกครั้งในภายหลัง.
            </p>
            <a href="{{ url('/') }}" class="btn btn-home">
                <i class="fas fa-home me-2"></i> กลับสู่หน้าหลัก
            </a>
        </div>
        <!-- Bootstrap JS Bundle (optional) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    