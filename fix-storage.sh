#!/bin/bash
# ============================================
# Towncore Storage Fix Script
# Run from your Laravel root on cPanel:
#   bash fix-storage.sh
# Delete after running.
# ============================================

echo "=== Towncore Storage Fix ==="
echo ""

# 1. Detect paths
LARAVEL_ROOT=$(pwd)
PUBLIC_DIR="$LARAVEL_ROOT/public"
STORAGE_TARGET="$LARAVEL_ROOT/storage/app/public"

echo "[1/6] Laravel root:    $LARAVEL_ROOT"
echo "      Public dir:      $PUBLIC_DIR"
echo "      Storage target:  $STORAGE_TARGET"
echo ""

# 2. Fix APP_URL in .env
CURRENT_URL=$(grep "^APP_URL=" .env | cut -d'=' -f2-)
echo "[2/6] Current APP_URL: $CURRENT_URL"

if echo "$CURRENT_URL" | grep -qi "localhost"; then
    echo "      ERROR: APP_URL contains 'localhost'!"
    echo "      Please edit .env and set APP_URL=https://yourdomain.com"
    echo "      Then re-run this script."
    echo ""
fi

# 3. Remove broken symlink and recreate
echo "[3/6] Fixing storage symlink..."
if [ -L "$PUBLIC_DIR/storage" ]; then
    echo "      Removing existing symlink..."
    rm "$PUBLIC_DIR/storage"
elif [ -d "$PUBLIC_DIR/storage" ]; then
    echo "      Found directory instead of symlink, removing..."
    rm -rf "$PUBLIC_DIR/storage"
fi

ln -s "$STORAGE_TARGET" "$PUBLIC_DIR/storage"
echo "      Created: $PUBLIC_DIR/storage -> $STORAGE_TARGET"
echo ""

# 4. Create required subdirectories
echo "[4/6] Creating storage subdirectories..."
mkdir -p "$STORAGE_TARGET/posts"
mkdir -p "$STORAGE_TARGET/categories"
mkdir -p "$STORAGE_TARGET/categories/featured"
mkdir -p "$STORAGE_TARGET/categories/gallery"
mkdir -p "$STORAGE_TARGET/portfolios"
mkdir -p "$STORAGE_TARGET/project-files"
mkdir -p "$STORAGE_TARGET/chat-files"
mkdir -p "$STORAGE_TARGET/freelancer-invoices"
mkdir -p "$STORAGE_TARGET/media"
mkdir -p "$STORAGE_TARGET/settings"
echo "      Done."
echo ""

# 5. Fix permissions
echo "[5/6] Fixing permissions..."
chmod -R 775 "$LARAVEL_ROOT/storage"
chmod -R 775 "$LARAVEL_ROOT/bootstrap/cache"
echo "      Done."
echo ""

# 6. Clear caches
echo "[6/6] Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo ""

# Verify
echo "=== Verification ==="
echo "Symlink:"
ls -la "$PUBLIC_DIR/storage"
echo ""
echo "Storage contents:"
ls "$STORAGE_TARGET/" 2>/dev/null || echo "(empty)"
echo ""
echo "APP_URL: $(grep '^APP_URL=' .env | cut -d'=' -f2-)"
echo ""
echo "=== DONE ==="
echo "IMPORTANT: Make sure APP_URL in .env is set to your live domain (https://yourdomain.com)"
echo "Then delete this script: rm fix-storage.sh"
