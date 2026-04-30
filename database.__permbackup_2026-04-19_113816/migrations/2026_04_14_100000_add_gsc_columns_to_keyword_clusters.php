<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keyword_clusters', function (Blueprint $table) {
            // Real data synced from Google Search Console API
            $table->integer('gsc_clicks')->nullable()->after('estimated_volume')
                ->comment('Real total clicks from GSC in last sync window');
            $table->integer('gsc_impressions')->nullable()->after('gsc_clicks')
                ->comment('Real total impressions from GSC in last sync window');
            $table->decimal('gsc_avg_position', 5, 1)->nullable()->after('gsc_impressions')
                ->comment('Real average SERP position from GSC');
            $table->decimal('gsc_ctr', 5, 2)->nullable()->after('gsc_avg_position')
                ->comment('Real average CTR (%) from GSC');
            $table->timestamp('gsc_synced_at')->nullable()->after('gsc_ctr')
                ->comment('When GSC data was last synced for this cluster');
        });
    }

    public function down(): void
    {
        Schema::table('keyword_clusters', function (Blueprint $table) {
            $table->dropColumn([
                'gsc_clicks',
                'gsc_impressions',
                'gsc_avg_position',
                'gsc_ctr',
                'gsc_synced_at',
            ]);
        });
    }
};
