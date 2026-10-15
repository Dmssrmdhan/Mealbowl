<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Supabase otomatis membuka tabel di schema "public" lewat REST API publik.
 * Aplikasi ini tidak memakai API itu (Laravel konek langsung sebagai role postgres,
 * yang tidak terpengaruh RLS), jadi RLS diaktifkan TANPA policy = akses lewat API publik
 * ditutup total, sedangkan aplikasi tetap berjalan normal.
 *
 * Hanya berjalan di PostgreSQL; di MySQL (lokal) tidak melakukan apa-apa.
 * Kalau nanti menambah tabel baru, jalankan ulang SQL di bawah ini.
 */
return new class extends Migration
{
    public function up()
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            DO $$
            DECLARE t record;
            BEGIN
                FOR t IN SELECT tablename FROM pg_tables WHERE schemaname = 'public' LOOP
                    EXECUTE format('ALTER TABLE public.%I ENABLE ROW LEVEL SECURITY', t.tablename);
                END LOOP;
            END $$;
        SQL);
    }

    public function down()
    {
        // Sengaja dikosongkan: rollback tidak perlu membuka kembali akses API publik.
    }
};
