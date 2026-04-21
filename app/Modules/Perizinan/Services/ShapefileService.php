<?php

namespace App\Modules\Perizinan\Services;

use Shapefile\Shapefile;
use Shapefile\ShapefileWriter;
use Shapefile\Geometry\Polygon;
use Illuminate\Support\Str;
use ZipArchive;

class ShapefileService
{
    /**
     * Generate a zipped Shapefile (.shp + .shx + .dbf + .prj) from GeoJSON polygon data.
     *
     * @param array $coordinates Array of [lng, lat] coordinate pairs forming the polygon
     * @param array $attributes  DBF field values (NAMA, LUAS_M2, etc.)
     * @param string|null $name  Base filename (without extension)
     * @return string Path to the generated ZIP file relative to storage
     */
    public function generate(array $coordinates, array $attributes, ?string $name = null): string
    {
        $slug = $name ? Str::slug($name) : 'polygon_' . date('Ymd_His');
        $uniqueId = Str::random(8);
        $storagePath = config('shapefile.path', 'shapefiles');
        $storageDir = storage_path("app/{$storagePath}");
        $dir = $storageDir . '/' . $slug . '_' . $uniqueId;

        // Ensure base storage directory exists
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $basePath = $dir . '/' . $slug;

        try {
            $this->writeShapefile($basePath, $coordinates, $attributes);
            $zipPath = $this->createZip($dir, $slug, $storagePath, $slug . '_' . $uniqueId);
            $this->cleanupTempFiles($dir, $slug);

            return $zipPath;
        } catch (\Exception $e) {
            $this->cleanupDir($dir);
            throw $e;
        }
    }

    /**
     * Write the .shp, .shx, .dbf, .prj files using php-shapefile.
     */
    private function writeShapefile(string $basePath, array $coordinates, array $attributes): void
    {
        $shp = new ShapefileWriter($basePath . '.shp');
        $shp->setShapeType(Shapefile::SHAPE_TYPE_POLYGON);

        // Set WGS84 projection
        $shp->setPRJ(config('shapefile.prj'));

        // Add DBF fields from config
        $fields = config('shapefile.fields', []);
        foreach ($fields as $fieldName => $def) {
            switch ($def['type']) {
                case 'char':
                    $shp->addCharField($fieldName, $def['size'] ?? 254);
                    break;
                case 'numeric':
                    $shp->addNumericField($fieldName, $def['size'] ?? 10, $def['decimals'] ?? 0);
                    break;
                case 'date':
                    $shp->addDateField($fieldName);
                    break;
            }
        }

        // Ensure polygon ring is closed (first point == last point)
        $coords = $coordinates;
        $first = $coords[0];
        $last = end($coords);
        if ($first[0] !== $last[0] || $first[1] !== $last[1]) {
            $coords[] = $first;
        }

        // Build WKT from WGS84 coordinates (degrees): POLYGON((lng lat, lng lat, ...))
        $wktPoints = implode(', ', array_map(fn($c) => $c[0] . ' ' . $c[1], $coords));
        $wkt = "POLYGON(({$wktPoints}))";

        // Create polygon geometry from WKT
        $polygon = new Polygon();
        $polygon->initFromWKT($wkt);

        // Set data attributes
        foreach ($fields as $fieldName => $def) {
            $value = $attributes[$fieldName] ?? ($def['type'] === 'numeric' ? 0 : '');
            $polygon->setData($fieldName, $value);
        }

        $shp->writeRecord($polygon);

        // Finalize and close files
        $shp = null;
    }

    /**
     * Bundle all shapefile components into a ZIP.
     */
    private function createZip(string $dir, string $name, string $storagePath, string $zipBaseName): string
    {
        $zipFilename = $zipBaseName . '.zip';
        $zipFullPath = storage_path("app/{$storagePath}/{$zipFilename}");

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Failed to create ZIP file');
        }

        $extensions = ['shp', 'shx', 'dbf', 'prj'];
        foreach ($extensions as $ext) {
            $file = $dir . '/' . $name . '.' . $ext;
            if (file_exists($file)) {
                $zip->addFile($file, $name . '.' . $ext);
            }
        }

        $zip->close();

        return "{$storagePath}/{$zipFilename}";
    }

    /**
     * Remove temporary individual shapefile components after zipping.
     */
    private function cleanupTempFiles(string $dir, string $name): void
    {
        $extensions = ['shp', 'shx', 'dbf', 'prj', 'cpg', 'dbt'];
        foreach ($extensions as $ext) {
            $file = $dir . '/' . $name . '.' . $ext;
            if (file_exists($file)) {
                unlink($file);
            }
        }
        @rmdir($dir);
    }

    /**
     * Emergency cleanup of the entire temp directory.
     */
    private function cleanupDir(string $dir): void
    {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            @rmdir($dir);
        }
    }

    /**
     * Calculate polygon area in m² using Web Mercator (EPSG:3857) projection + Shoelace.
     * Matches OSS Indonesia which uses "Proyeksi Mercator Auxiliary Sphere".
     */
    public function calculateArea(array $coordinates): float
    {
        if (count($coordinates) < 3) {
            return 0;
        }

        $projected = $this->projectToWebMercator($coordinates);

        $area = 0;
        $n = count($projected);
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $area += $projected[$i]['x'] * $projected[$j]['y'];
            $area -= $projected[$j]['x'] * $projected[$i]['y'];
        }

        return abs($area) / 2;
    }

    /**
     * Project WGS84 [lng, lat] to Web Mercator EPSG:3857 [x, y] in meters.
     */
    private function projectToWebMercator(array $coordinates): array
    {
        $a = 6378137.0; // WGS84 semi-major axis

        $projected = [];
        foreach ($coordinates as $c) {
            $lngRad = deg2rad($c[0]);
            $latRad = deg2rad($c[1]);

            $x = $a * $lngRad;
            $y = $a * log(tan(M_PI / 4 + $latRad / 2));

            $projected[] = ['x' => $x, 'y' => $y];
        }

        return $projected;
    }

    /**
     * Calculate polygon perimeter in meters.
     */
    public function calculatePerimeter(array $coordinates): float
    {
        $total = 0;
        $n = count($coordinates);
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $total += $this->haversineDistance(
                $coordinates[$i][1], $coordinates[$i][0],
                $coordinates[$j][1], $coordinates[$j][0]
            );
        }
        return $total;
    }

    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
