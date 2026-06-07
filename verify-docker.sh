#!/bin/bash
# Quick verification script for Docker deployment

echo "=== Docker Deployment Verification ==="
echo ""

# Check Dockerfile exists
if [ -f "Dockerfile" ]; then
    echo "✓ Dockerfile found"
else
    echo "✗ Dockerfile not found"
    exit 1
fi

# Check docker-compose exists
if [ -f "docker-compose.yml" ]; then
    echo "✓ docker-compose.yml found"
else
    echo "✗ docker-compose.yml not found"
fi

# Check render.yaml exists
if [ -f "render.yaml" ]; then
    echo "✓ render.yaml found"
else
    echo "✗ render.yaml not found"
fi

# Check .env.example exists
if [ -f ".env.example" ]; then
    echo "✓ .env.example found"
else
    echo "✗ .env.example not found"
fi

# Check docker directory exists
if [ -d "docker" ]; then
    echo "✓ docker/ directory found"
    if [ -f "docker/nginx.conf" ]; then
        echo "  ✓ docker/nginx.conf"
    fi
    if [ -f "docker/default.conf" ]; then
        echo "  ✓ docker/default.conf"
    fi
    if [ -f "docker/php-fpm.conf" ]; then
        echo "  ✓ docker/php-fpm.conf"
    fi
    if [ -f "docker/supervisor.conf" ]; then
        echo "  ✓ docker/supervisor.conf"
    fi
    if [ -f "docker/start.sh" ]; then
        echo "  ✓ docker/start.sh"
    fi
else
    echo "✗ docker/ directory not found"
fi

echo ""
echo "=== Files Ready ==="
echo "You can now:"
echo "1. Test locally: docker-compose up -d"
echo "2. Deploy to Render: push to GitHub and follow RENDER_DEPLOYMENT.md"
echo ""
