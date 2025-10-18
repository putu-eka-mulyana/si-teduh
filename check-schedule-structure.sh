#!/bin/bash

# Script untuk menampilkan informasi tentang struktur tabel schedules
# Termasuk valid enum values untuk kolom 'type'

echo "📋 Schedule Table Information"
echo "============================="
echo ""

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fungsi untuk print dengan warna
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_status "Checking schedule table structure..."

# Cek struktur tabel schedules
echo ""
echo "Schedule Table Structure:"
echo "========================"
php artisan tinker --execute="
\$columns = \Illuminate\Support\Facades\Schema::getColumnListing('schedules');
echo 'Columns: ' . implode(', ', \$columns) . PHP_EOL;
echo '';

// Cek detail kolom type
\$typeColumn = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM schedules WHERE Field = \"type\"')[0];
echo 'Type Column Details:' . PHP_EOL;
echo '  Field: ' . \$typeColumn->Field . PHP_EOL;
echo '  Type: ' . \$typeColumn->Type . PHP_EOL;
echo '  Null: ' . \$typeColumn->Null . PHP_EOL;
echo '  Key: ' . \$typeColumn->Key . PHP_EOL;
echo '  Default: ' . (\$typeColumn->Default ?? 'NULL') . PHP_EOL;
echo '  Extra: ' . \$typeColumn->Extra . PHP_EOL;
"

echo ""
echo "Valid Schedule Types:"
echo "===================="
echo "✅ edukasi"
echo "✅ konsultasi" 
echo "✅ ambil obat"
echo ""

print_status "Checking existing schedules..."

# Cek schedules yang ada
php artisan tinker --execute="
\$schedules = \App\Models\Schedule::all();
echo 'Total schedules: ' . \$schedules->count() . PHP_EOL;
echo '';

if (\$schedules->count() > 0) {
    echo 'Existing schedules:' . PHP_EOL;
    foreach (\$schedules as \$schedule) {
        echo '  ID: ' . \$schedule->id . ' | Type: ' . \$schedule->type . ' | Date: ' . \$schedule->datetime . PHP_EOL;
    }
} else {
    echo 'No existing schedules found.' . PHP_EOL;
}
"

echo ""
echo "📝 Usage Examples:"
echo "=================="
echo ""
echo "Creating a new schedule:"
echo "php artisan tinker --execute=\""
echo "\\\$schedule = \\App\\Models\\Schedule::create(["
echo "    'patient_id' => 1,"
echo "    'datetime' => now()->addHours(1),"
echo "    'officer_id' => 1,"
echo "    'status' => 1,"
echo "    'type' => 'konsultasi',  // Valid: edukasi, konsultasi, ambil obat"
echo "    'message' => 'Your message here'"
echo "]);"
echo "\""
echo ""

print_success "Schedule table information displayed successfully!"
