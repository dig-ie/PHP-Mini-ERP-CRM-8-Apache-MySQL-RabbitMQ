@echo off
echo 🚀 Starting PHP ERP/CRM System...
echo ==================================

REM Check if Docker is running
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker is not running. Please start Docker Desktop first.
    pause
    exit /b 1
)

REM Start all services
echo 📦 Starting containers...
docker-compose up -d

REM Wait for services to be ready
echo ⏳ Waiting for services to be ready...
timeout /t 10 /nobreak >nul

REM Check if services are running
echo 🔍 Checking services status...
docker-compose ps

echo.
echo ✅ System started successfully!
echo.
echo 🌐 Web Application: http://localhost:8080
echo 🐰 RabbitMQ Management: http://localhost:15672 (guest/guest)
echo 🗄️  MySQL: localhost:3307 (erp_user/erp_pass)
echo.
echo 📊 Worker Status:
docker logs worker --tail 5
echo.
echo 💡 To view worker logs: docker logs -f worker
echo 💡 To stop system: docker-compose down
pause
