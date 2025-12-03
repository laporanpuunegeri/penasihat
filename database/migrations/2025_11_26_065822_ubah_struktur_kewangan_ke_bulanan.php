use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kewangan_records', function (Blueprint $table) {
            // Tambah 12 kolum bulan selepas belanja_s4
            $table->decimal('belanja_jan', 15, 2)->default(0)->after('belanja_s4');
            $table->decimal('belanja_feb', 15, 2)->default(0)->after('belanja_jan');
            $table->decimal('belanja_mac', 15, 2)->default(0)->after('belanja_feb');
            $table->decimal('belanja_apr', 15, 2)->default(0)->after('belanja_mac');
            $table->decimal('belanja_mei', 15, 2)->default(0)->after('belanja_apr');
            $table->decimal('belanja_jun', 15, 2)->default(0)->after('belanja_mei');
            $table->decimal('belanja_jul', 15, 2)->default(0)->after('belanja_jun');
            $table->decimal('belanja_ogos', 15, 2)->default(0)->after('belanja_jul');
            $table->decimal('belanja_sep', 15, 2)->default(0)->after('belanja_ogos');
            $table->decimal('belanja_okt', 15, 2)->default(0)->after('belanja_sep');
            $table->decimal('belanja_nov', 15, 2)->default(0)->after('belanja_okt');
            $table->decimal('belanja_dis', 15, 2)->default(0)->after('belanja_nov');
        });
    }

    public function down(): void
    {
        Schema::table('kewangan_records', function (Blueprint $table) {
            $table->dropColumn([
                'belanja_jan','belanja_feb','belanja_mac','belanja_apr',
                'belanja_mei','belanja_jun','belanja_jul','belanja_ogos',
                'belanja_sep','belanja_okt','belanja_nov','belanja_dis'
            ]);
        });
    }
};
