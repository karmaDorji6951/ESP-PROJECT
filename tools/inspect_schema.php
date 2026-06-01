<?php

/**
 * One-off schema inspector to debug FK issues.
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = Illuminate\Support\Facades\DB::connection()->getDatabaseName();

echo "DB={$db}\n";

$tables = ['tblgewog', 'tbldzongkhag'];

$rows = Illuminate\Support\Facades\DB::select(
    'SELECT table_name, engine, table_collation '
    . 'FROM information_schema.tables '
    . 'WHERE table_schema = DATABASE() '
    . 'AND table_name IN (' . implode(',', array_fill(0, count($tables), '?')) . ')',
    $tables
);

foreach ($rows as $r) {
    echo $r->table_name . ' engine=' . ($r->engine ?? 'NULL') . ' collation=' . ($r->table_collation ?? 'NULL') . "\n";
}

$cols = Illuminate\Support\Facades\DB::select(
    'SELECT table_name, column_name, column_type, is_nullable, column_key '
    . 'FROM information_schema.columns '
    . 'WHERE table_schema = DATABASE() '
    . 'AND table_name IN (' . implode(',', array_fill(0, count($tables), '?')) . ') '
    . 'AND column_name IN ("id", "dzongkhag_id") '
    . 'ORDER BY table_name, column_name',
    $tables
);

foreach ($cols as $c) {
    echo $c->table_name . '.' . $c->column_name
        . ' type=' . $c->column_type
        . ' nullable=' . $c->is_nullable
        . ' key=' . $c->column_key
        . "\n";
}
