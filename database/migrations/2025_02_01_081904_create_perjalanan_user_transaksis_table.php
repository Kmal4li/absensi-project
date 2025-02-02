<?

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerjalananUserTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perjalanan_user_transaksis', function (Blueprint $table) {
            $table->id(); // Kolom ID otomatis
            $table->foreignId('perjalanan_id')->constrained('perjalanans')->onDelete('cascade'); // Foreign key ke tabel perjalanans
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade'); // Foreign key ke tabel transaksis
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perjalanan_user_transaksis');
    }
}