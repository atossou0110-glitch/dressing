<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SplFileInfo;

class ProductGalleryManager
{
    /**
     * Supported image extensions.
     *
     * @var list<string>
     */
    private const ALLOWED_EXTENSIONS = ['png', 'jpg', 'jpeg', 'jfif', 'webp'];

    /**
     * Resolve the gallery URLs used on the storefront.
     *
     * @return list<string>
     */
    public function galleryUrls(Product $product): array
    {
        $managedImages = $this->managedImageFiles($product);

        if ($managedImages->isEmpty()) {
            return $this->fallbackGallery($product);
        }

        return $managedImages
            ->map(fn (SplFileInfo $file) => $this->publicImageUrl($product, $file))
            ->all();
    }

    /**
     * Resolve the gallery images shown in the admin dashboard.
     *
     * @return list<array{url: string, filename: string, managed: bool, updated_at: ?string}>
     */
    public function adminImages(Product $product): array
    {
        $managedImages = $this->managedImageFiles($product);

        if ($managedImages->isNotEmpty()) {
            return $managedImages
                ->map(fn (SplFileInfo $file) => [
                    'url' => $this->publicImageUrl($product, $file),
                    'filename' => $file->getFilename(),
                    'managed' => true,
                    'updated_at' => date('d/m/Y H:i', $file->getMTime()),
                ])
                ->all();
        }

        return collect($this->fallbackGallery($product))
            ->values()
            ->map(fn (string $url, int $index) => [
                'url' => $url,
                'filename' => 'Image par defaut '.($index + 1),
                'managed' => false,
                'updated_at' => null,
            ])
            ->all();
    }

    /**
     * Store new gallery images for a product.
     *
     * @param  array<int, UploadedFile>  $images
     */
    public function store(Product $product, array $images): void
    {
        $directory = $this->galleryDirectory($product);

        File::ensureDirectoryExists($directory);

        foreach ($images as $image) {
            $extension = strtolower($image->getClientOriginalExtension() ?: $image->extension() ?: 'png');
            $filename = sprintf(
                '%s-%s-%s.%s',
                $product->slug,
                now()->format('YmdHis'),
                Str::lower(Str::random(8)),
                $extension,
            );

            $image->move($directory, $filename);
        }
    }

    /**
     * Delete a managed gallery image.
     */
    public function delete(Product $product, string $filename): bool
    {
        $path = $this->imagePath($product, $filename);

        if ($path === null || ! is_file($path)) {
            return false;
        }

        return (bool) File::delete($path);
    }

    /**
     * Resolve an image path for public serving.
     */
    public function imagePath(Product $product, string $filename): ?string
    {
        $safeFilename = basename($filename);
        $path = $this->galleryDirectory($product).DIRECTORY_SEPARATOR.$safeFilename;

        return is_file($path) ? $path : null;
    }

    /**
     * Build a public URL for a managed image.
     */
    public function publicImageUrl(Product $product, SplFileInfo $file): string
    {
        return route('catalog.media.product', [
            'product' => $product->slug,
            'filename' => $file->getFilename(),
        ]).'?v='.$file->getMTime();
    }

    /**
     * Read managed gallery files while removing duplicates.
     *
     * @return Collection<int, SplFileInfo>
     */
    private function managedImageFiles(Product $product): Collection
    {
        $directory = $this->galleryDirectory($product);

        if (! is_dir($directory)) {
            return collect();
        }

        return collect(File::files($directory))
            ->filter(fn (SplFileInfo $file) => in_array(strtolower($file->getExtension()), self::ALLOWED_EXTENSIONS, true))
            ->groupBy(fn (SplFileInfo $file) => hash_file('sha256', $file->getPathname()))
            ->map(function (Collection $files) {
                return $files
                    ->sortBy(fn (SplFileInfo $file) => str_contains($file->getFilename(), ' (') ? 1 : 0)
                    ->first();
            })
            ->filter()
            ->sortBy(fn (SplFileInfo $file) => $file->getFilename())
            ->values();
    }

    /**
     * Resolve the product gallery directory.
     */
    private function galleryDirectory(Product $product): string
    {
        $relativeDirectory = (string) config("catalog.products.{$product->slug}.directory", 'catalog-media');

        return base_path(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativeDirectory));
    }

    /**
     * Resolve the fallback gallery.
     *
     * @return list<string>
     */
    private function fallbackGallery(Product $product): array
    {
        return array_values((array) config("catalog.products.{$product->slug}.fallback", []));
    }
}
