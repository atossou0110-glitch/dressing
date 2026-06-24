<?php

use App\Models\Product;
use App\Models\ProductPreorder;
use App\Models\ProductReview;
use App\Models\ProductVote;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('catalog:db-overview', function () {
    Product::syncRequiredCatalog();

    $this->info('Catalog database overview');
    $this->line('Users: '.User::query()->count());
    $this->line('Admins: '.User::query()->where('is_admin', true)->count());
    $this->line('Products: '.Product::query()->count());
    $this->line('Votes recorded: '.ProductVote::query()->count());
    $this->line('Preorders recorded: '.ProductPreorder::query()->count());
    $this->line('Reviews: '.ProductReview::query()->count());
    $this->line('Settings: '.SiteSetting::query()->count());
    $this->line('WhatsApp: '.(SiteSetting::value('whatsapp_number', (string) config('services.whatsapp.number')) ?: 'not configured'));

    $this->newLine();
    $this->table(
        ['Code', 'Name', 'Votes', 'Preorders'],
        Product::query()
            ->orderBy('code')
            ->get(['code', 'name', 'vote_count', 'preorder_count'])
            ->map(fn (Product $product) => [
                $product->code,
                $product->name,
                (int) $product->vote_count,
                (int) $product->preorder_count,
            ])
            ->all(),
    );
})->purpose('Display a lightweight overview of the catalog database');

Artisan::command('catalog:preflight {--production : Fail when production profile checks are not satisfied}', function () {
    $strictProduction = (bool) $this->option('production');

    $blockingIssues = [];
    $warnings = [];
    $checks = [];

    $environment = trim((string) config('app.env', 'production'));
    $debug = (bool) config('app.debug', false);
    $appUrl = trim((string) config('app.url', ''));
    $sessionSecureCookie = (bool) config('session.secure', false);
    $mailDriver = trim((string) config('mail.default', ''));
    $queueDriver = trim((string) config('queue.default', ''));
    $cacheStore = trim((string) config('cache.default', ''));
    $sessionDriver = trim((string) config('session.driver', ''));
    $appKey = trim((string) config('app.key', ''));
    $fedapaySecret = trim((string) config('services.fedapay.secret_key', ''));
    $smtpHost = trim((string) config('mail.mailers.smtp.host', ''));
    $smtpUsername = trim((string) config('mail.mailers.smtp.username', ''));
    $smtpPassword = trim((string) config('mail.mailers.smtp.password', ''));

    $looksPlaceholder = static function (string $value): bool {
        $normalized = strtolower(trim($value));

        if ($normalized === '') {
            return true;
        }

        foreach (['change_me', 'your_', 'example.com', 'placeholder'] as $token) {
            if (str_contains($normalized, $token)) {
                return true;
            }
        }

        return false;
    };

    $fedapayConfigured = $fedapaySecret !== '';
    $smtpConfigured = $mailDriver !== 'smtp'
        || (! $looksPlaceholder($smtpHost) && ! $looksPlaceholder($smtpUsername) && ! $looksPlaceholder($smtpPassword));

    $addProductionCheck = function (bool $condition, string $message) use (&$blockingIssues, &$warnings, $strictProduction): void {
        if (! $condition) {
            return;
        }

        if ($strictProduction) {
            $blockingIssues[] = $message;

            return;
        }

        $warnings[] = $message;
    };

    $checks[] = ['Environment', $environment !== '' ? $environment : '(empty)'];
    $checks[] = ['Debug mode', $debug ? 'true' : 'false'];
    $checks[] = ['App URL', $appUrl !== '' ? $appUrl : '(empty)'];
    $checks[] = ['Session secure cookie', $sessionSecureCookie ? 'true' : 'false'];
    $checks[] = ['Mail driver', $mailDriver !== '' ? $mailDriver : '(empty)'];
    $checks[] = ['SMTP ready', $smtpConfigured ? 'yes' : 'no'];
    $checks[] = ['Queue connection', $queueDriver !== '' ? $queueDriver : '(empty)'];
    $checks[] = ['Cache store', $cacheStore !== '' ? $cacheStore : '(empty)'];
    $checks[] = ['Session driver', $sessionDriver !== '' ? $sessionDriver : '(empty)'];
    $checks[] = ['FedaPay configured', $fedapayConfigured ? 'yes' : 'no'];

    if ($appKey === '') {
        $blockingIssues[] = 'APP_KEY is missing.';
    }

    try {
        DB::connection()->getPdo();

        $checks[] = ['Database connection', 'ok'];
    } catch (\Throwable $exception) {
        $checks[] = ['Database connection', 'failed'];
        $blockingIssues[] = 'Database connection failed: '.$exception->getMessage();
    }

    try {
        Product::syncRequiredCatalog();
    } catch (\Throwable $exception) {
        $blockingIssues[] = 'Unable to sync required catalog products: '.$exception->getMessage();
    }

    try {
        if (! Schema::hasTable('migrations')) {
            $blockingIssues[] = 'The migrations table is missing.';
            $checks[] = ['Migrations', 'table missing'];
        } else {
            $migrationFiles = glob(database_path('migrations'.DIRECTORY_SEPARATOR.'*.php')) ?: [];
            $appliedMigrations = (int) DB::table('migrations')->count();
            $checks[] = ['Migrations applied', (string) $appliedMigrations];
            $checks[] = ['Migration files', (string) count($migrationFiles)];

            if ($appliedMigrations < count($migrationFiles)) {
                $blockingIssues[] = sprintf(
                    'Pending migrations detected (%d missing).',
                    count($migrationFiles) - $appliedMigrations,
                );
            }
        }
    } catch (\Throwable $exception) {
        $blockingIssues[] = 'Unable to inspect migrations: '.$exception->getMessage();
    }

    $productCount = Product::query()->count();
    $checks[] = ['Catalog products', (string) $productCount];

    if ($productCount < 2) {
        $blockingIssues[] = 'Catalog products are incomplete.';
    }

    $whatsAppNumber = preg_replace(
        '/\D+/',
        '',
        (string) SiteSetting::value('whatsapp_number', (string) config('services.whatsapp.number')),
    );

    $checks[] = ['WhatsApp configured', $whatsAppNumber !== '' ? 'yes' : 'no'];

    if (! $fedapayConfigured) {
        $warnings[] = 'FedaPay is not configured; checkout payments will stay disabled.';
    }

    if (! $smtpConfigured) {
        $warnings[] = 'SMTP credentials look incomplete; email delivery may fail.';
    }

    $urlHost = strtolower((string) parse_url($appUrl, PHP_URL_HOST));
    $urlScheme = strtolower((string) parse_url($appUrl, PHP_URL_SCHEME));
    $looksLocalHost = in_array($urlHost, ['localhost', '127.0.0.1', '::1'], true)
        || str_starts_with($urlHost, '192.168.')
        || str_starts_with($urlHost, '10.');

    $addProductionCheck($environment !== 'production', 'APP_ENV should be set to production.');
    $addProductionCheck($debug, 'APP_DEBUG should be false in production.');
    $addProductionCheck($appUrl === '' || $urlHost === '', 'APP_URL must be configured.');
    $addProductionCheck($appUrl !== '' && $urlScheme !== 'https', 'APP_URL should use HTTPS.');
    $addProductionCheck($looksLocalHost, 'APP_URL should not point to localhost or a private network host.');
    $addProductionCheck(! $sessionSecureCookie, 'SESSION_SECURE should be true.');
    $addProductionCheck(in_array($mailDriver, ['log', 'array', ''], true), 'MAIL_MAILER should be a real provider (smtp, ses, postmark, etc.).');
    $addProductionCheck(! $smtpConfigured, 'SMTP credentials are incomplete or still placeholders.');
    $addProductionCheck(in_array($queueDriver, ['sync', ''], true), 'QUEUE_CONNECTION should be asynchronous (database, redis, etc.).');
    $addProductionCheck(in_array($cacheStore, ['array', ''], true), 'CACHE_STORE should be persistent (database, redis, etc.).');
    $addProductionCheck(in_array($sessionDriver, ['array', ''], true), 'SESSION_DRIVER should be persistent (database, redis, etc.).');
    $addProductionCheck(! is_file(public_path('build/manifest.json')), 'Frontend assets are missing (run npm run build).');
    $addProductionCheck($whatsAppNumber === '', 'WhatsApp number is missing; preorders will not work.');
    $addProductionCheck(! $fedapayConfigured, 'FEDAPAY_SECRET_KEY is missing; checkout payments will not work.');

    foreach ([storage_path(), base_path('bootstrap/cache')] as $path) {
        $label = str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
        $exists = is_dir($path);
        $writable = $exists && is_writable($path);

        $checks[] = [$label.' writable', $writable ? 'yes' : 'no'];

        if (! $exists) {
            $blockingIssues[] = $label.' directory is missing.';
            continue;
        }

        if (! $writable) {
            $blockingIssues[] = $label.' directory is not writable.';
        }
    }

    $this->info('Catalog deployment preflight');
    $this->line('Mode: '.($strictProduction ? 'production profile (strict)' : 'baseline (warnings allowed)'));
    $this->newLine();

    $this->table(['Check', 'Value'], $checks);

    if ($warnings !== []) {
        $this->warn('Warnings:');

        foreach ($warnings as $warning) {
            $this->line('- '.$warning);
        }

        $this->newLine();
        $this->line('Tip: run "php artisan catalog:preflight --production" to enforce strict production checks.');
    }

    if ($blockingIssues !== []) {
        $this->error('Blocking issues:');

        foreach ($blockingIssues as $issue) {
            $this->line('- '.$issue);
        }

        return 1;
    }

    $this->info('Preflight passed.');

    return 0;
})->purpose('Validate baseline and production deployment readiness for the catalog');

Artisan::command('catalog:ops:backup-db {--target= : Optional target directory for backup files}', function () {
    $targetDirectory = trim((string) $this->option('target'));
    $backupDirectory = $targetDirectory !== ''
        ? $targetDirectory
        : storage_path('app/backups/database');

    File::ensureDirectoryExists($backupDirectory);

    $connectionName = (string) config('database.default', '');
    $driver = (string) config("database.connections.{$connectionName}.driver", '');
    $timestamp = now()->format('Ymd-His');
    $files = [];

    if ($driver === 'sqlite') {
        $sqlitePath = (string) config("database.connections.{$connectionName}.database", '');

        if ($sqlitePath !== '' && $sqlitePath !== ':memory:' && is_file($sqlitePath)) {
            $sqliteBackupPath = $backupDirectory.DIRECTORY_SEPARATOR."catalog-db-{$timestamp}.sqlite";
            File::copy($sqlitePath, $sqliteBackupPath);
            $files[] = $sqliteBackupPath;
        }
    }

    $snapshotPath = $backupDirectory.DIRECTORY_SEPARATOR."catalog-snapshot-{$timestamp}.json";

    $snapshot = [
        'generated_at' => now()->toIso8601String(),
        'connection' => $connectionName,
        'driver' => $driver,
        'products' => Product::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'slug' => $product->slug,
                'code' => $product->code,
                'name' => $product->name,
                'vote_count' => (int) $product->vote_count,
                'preorder_count' => (int) $product->preorder_count,
                'content' => $product->resolvedContent(),
                'created_at' => $product->created_at?->toIso8601String(),
                'updated_at' => $product->updated_at?->toIso8601String(),
            ])
            ->all(),
        'product_votes' => ProductVote::query()
            ->orderBy('id')
            ->get(['id', 'product_id', 'visitor_hash', 'created_at', 'updated_at'])
            ->map(fn (ProductVote $vote) => [
                'id' => $vote->id,
                'product_id' => $vote->product_id,
                'visitor_hash' => $vote->visitor_hash,
                'created_at' => $vote->created_at?->toIso8601String(),
                'updated_at' => $vote->updated_at?->toIso8601String(),
            ])
            ->all(),
        'product_preorders' => ProductPreorder::query()
            ->orderBy('id')
            ->get(['id', 'product_id', 'visitor_hash', 'created_at', 'updated_at'])
            ->map(fn (ProductPreorder $preorder) => [
                'id' => $preorder->id,
                'product_id' => $preorder->product_id,
                'visitor_hash' => $preorder->visitor_hash,
                'created_at' => $preorder->created_at?->toIso8601String(),
                'updated_at' => $preorder->updated_at?->toIso8601String(),
            ])
            ->all(),
        'product_reviews' => ProductReview::query()
            ->orderBy('id')
            ->get(['id', 'product_id', 'author_name', 'body', 'rating', 'created_at', 'updated_at'])
            ->map(fn (ProductReview $review) => [
                'id' => $review->id,
                'product_id' => $review->product_id,
                'author_name' => $review->author_name,
                'body' => $review->body,
                'rating' => (int) $review->rating,
                'created_at' => $review->created_at?->toIso8601String(),
                'updated_at' => $review->updated_at?->toIso8601String(),
            ])
            ->all(),
        'site_settings' => SiteSetting::query()
            ->orderBy('key')
            ->get(['key', 'value', 'updated_at'])
            ->map(fn (SiteSetting $setting) => [
                'key' => $setting->key,
                'value' => $setting->value,
                'updated_at' => $setting->updated_at?->toIso8601String(),
            ])
            ->all(),
        'users' => User::query()
            ->orderBy('id')
            ->get(['id', 'name', 'email', 'is_admin', 'created_at', 'updated_at'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => (bool) $user->is_admin,
                'created_at' => $user->created_at?->toIso8601String(),
                'updated_at' => $user->updated_at?->toIso8601String(),
            ])
            ->all(),
    ];

    File::put($snapshotPath, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $files[] = $snapshotPath;

    $this->info('Backup termine.');
    $this->line('Repertoire: '.$backupDirectory);

    foreach ($files as $file) {
        $this->line('- '.$file);
    }

    if ($driver !== 'sqlite') {
        $this->warn('Driver non-sqlite detecte: backup snapshot JSON cree (compatible multi-hebergeurs).');
    }

    return 0;
})->purpose('Create a database backup snapshot for operational safety');

Artisan::command('catalog:ops:maintenance {state : on|off} {--secret= : Optional bypass secret while maintenance is on}', function () {
    $state = strtolower((string) $this->argument('state'));

    if (! in_array($state, ['on', 'off'], true)) {
        $this->error("Etat invalide. Utilise 'on' ou 'off'.");

        return 1;
    }

    if ($state === 'on') {
        $parameters = [
            '--render' => 'maintenance',
            '--retry' => 60,
        ];

        $secret = trim((string) $this->option('secret'));

        if ($secret !== '') {
            $parameters['--secret'] = $secret;
        }

        Artisan::call('down', $parameters);
        $this->info('Mode maintenance active.');

        if ($secret !== '') {
            $this->line('Bypass URL: '.url($secret));
        }

        return 0;
    }

    Artisan::call('up');
    $this->info('Mode maintenance desactive.');

    return 0;
})->purpose('Toggle maintenance mode with catalog defaults');

Artisan::command('catalog:ops:release-check {--with-tests : Also run php artisan test}', function () {
    $this->info('Release check demarre...');
    $this->newLine();

    $preflightExit = Artisan::call('catalog:preflight', ['--production' => true]);
    $this->line(trim(Artisan::output()));

    if ($preflightExit !== 0) {
        $this->error('Release check bloque: preflight strict a echoue.');

        return $preflightExit;
    }

    if ((bool) $this->option('with-tests')) {
        $testExit = Artisan::call('test');
        $this->line(trim(Artisan::output()));

        if ($testExit !== 0) {
            $this->error('Release check bloque: la suite de tests a echoue.');

            return $testExit;
        }
    }

    $this->info('Release check valide.');

    return 0;
})->purpose('Run one-command release validation checks');
