#!/bin/bash

echo "🚀 Starting PHP ERP/CRM System..."
echo "=================================="

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker Desktop first."
    exit 1
fi

# Start all services
echo "📦 Starting containers..."
docker-compose up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Check if services are running
echo "🔍 Checking services status..."
docker-compose ps

echo ""
echo "✅ System started successfully!"
echo ""
echo "🌐 Web Application: http://localhost:8080"
echo "🐰 RabbitMQ Management: http://localhost:15672 (guest/guest)"
echo "🗄️  MySQL: localhost:3307 (erp_user/erp_pass)"
echo ""
echo "📊 Worker Status:"
docker logs worker --tail 5
echo ""
echo "💡 To view worker logs: docker logs -f worker"
echo "💡 To stop system: docker-compose down"
