#!/usr/bin/env bash
set -e

VM_HOST="invenio-vm"
VM_DEST="/var/www/invenio"
LOCAL_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "🚀 Memulai sinkronisasi kode ke VM ($VM_HOST)..."

# 1. Sync file aplikasi via rsync
rsync -avz --delete \
    --exclude='.git' \
    --exclude='.env' \
    --exclude='node_modules' \
    --exclude='prompt*.txt' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    "$LOCAL_DIR/" "$VM_HOST:$VM_DEST/"

# 2. Update permission & clear cache di VM
ssh "$VM_HOST" bash << 'EOF'
set -e
cd /var/www/invenio
sudo chown -R fiemas:www-data /var/www/invenio
sudo chmod -R 775 /var/www/invenio/storage /var/www/invenio/bootstrap/cache
php artisan optimize:clear
EOF

echo "✅ Sinkronisasi berhasil! Buka http://10.10.10.101 di browser."
