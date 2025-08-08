@echo off
cd "C:\xampp\htdocs\inventory_system"
"C:\xampp\php\php.exe" artisan schedule:run >> NUL 2>&1
exit