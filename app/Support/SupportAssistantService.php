<?php

namespace App\Support;

use App\Models\FlashCampaign;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SupportAssistantService
{
    /**
     * Generate one assistant reply for the storefront support widget.
     *
     * @param  list<array{role?: string, body?: string}>  $history
     * @return array{
     *     body: string,
     *     confidence: float,
     *     needsHuman: bool,
     *     quickReplies: list<string>
     * }
     */
    public function reply(string $message, ?Product $product = null, array $history = []): array
    {
        $normalized = $this->normalize($message);
        $historyContext = $this->historyContext($history);
        $whatsAppUrl = $this->whatsAppUrl();
        $campaign = FlashCampaign::query()->activeNow()->latest('starts_at')->first();
        $productName = $product?->name ?? 'ce produit';
        $productPrice = $product?->resolvedContent()['home_price'] ?? null;
        $productDimensions = $product !== null ? $this->productDimensions($product) : null;
        $knownDimensions = $this->extractDimensions($message) ?? $this->lastMentionedDimensions($history);
        $knownCity = $this->extractCity($message) ?? $this->lastMentionedCity($history);
        $budgetMax = $this->extractBudgetMax($message);
        $budgetSensitive = $budgetMax !== null || $this->containsAny($normalized, [
            'pas cher',
            'moins cher',
            'abordable',
            'petit budget',
            'budget serre',
            'trop cher',
        ]);
        $intent = $this->detectIntent(
            $normalized,
            $historyContext,
            $product,
            $campaign !== null,
            $knownDimensions !== null,
            $knownCity !== null,
            $budgetSensitive,
        );

        if ($normalized === '') {
            return [
                'body' => 'Je peux vous aider sur les prix, la livraison, le paiement, les dimensions et le choix du meuble selon votre espace.',
                'confidence' => 0.45,
                'needsHuman' => false,
                'quickReplies' => ['Choisir un meuble', 'Delai de livraison', 'Paiement disponible'],
            ];
        }

        switch ($intent) {
            case 'thanks':
                return [
                    'body' => 'Avec plaisir. Si vous voulez continuer, je peux encore vous aider sur les dimensions, la livraison, le paiement ou le choix du meuble le plus adapte.',
                    'confidence' => 0.82,
                    'needsHuman' => false,
                    'quickReplies' => ['Envoyer mes dimensions', 'Comment payer', 'Parler a un expert'],
                ];

            case 'human':
                $body = 'Je peux vous orienter tout de suite vers un expert pour finaliser votre projet.';

                if ($whatsAppUrl !== null) {
                    $body .= ' Ouvrez WhatsApp et envoyez votre besoin pour une reprise humaine rapide.';
                }

                return [
                    'body' => $body,
                    'confidence' => 0.96,
                    'needsHuman' => true,
                    'quickReplies' => ['Parler a un expert', 'Envoyer les dimensions', 'Recevoir un conseil sur mesure'],
                ];

            case 'delivery':
                if ($knownCity !== null) {
                    return [
                        'body' => sprintf(
                            'Merci. Pour %s, nous pouvons organiser la livraison et vous confirmer le meilleur delai apres validation de commande. Si vous voulez, envoyez aussi votre quartier ou votre disponibilite pour affiner la proposition.',
                            $knownCity
                        ),
                        'confidence' => 0.91,
                        'needsHuman' => false,
                        'quickReplies' => ['Parler a un expert', 'Comment payer', 'Envoyer mes dimensions'],
                    ];
                }

                return [
                    'body' => 'La livraison est organisee apres validation de commande, puis l installation se cale selon la ville et la disponibilite de l equipe. Donnez-moi simplement votre ville pour une reponse plus precise.',
                    'confidence' => 0.88,
                    'needsHuman' => false,
                    'quickReplies' => ['Je suis a Cotonou', 'Je veux un expert', 'Comment payer'],
                ];

            case 'payment':
                return [
                    'body' => 'Vous pouvez lancer le paiement depuis la fiche produit via FedaPay. Si le paiement ne passe pas, l equipe peut aussi reprendre la commande manuellement.',
                    'confidence' => 0.9,
                    'needsHuman' => false,
                    'quickReplies' => ['Voir un produit', 'Delai de livraison', 'Parler a un expert'],
                ];

            case 'price':
                if ($product !== null) {
                    $body = $productPrice !== null
                        ? sprintf('%s est actuellement affiche a %s.', $productName, $productPrice)
                        : sprintf('Je peux vous aider sur %s, mais le prix n est pas encore renseigne sur cette fiche.', $productName);

                    if ($budgetSensitive) {
                        $recommendations = $this->recommendProducts($product, $normalized, $budgetMax, true);

                        if ($recommendations !== []) {
                            $body .= ' Si vous cherchez une option plus accessible, je peux deja vous orienter vers '.$this->recommendationSentence($recommendations).'.';
                        }
                    } else {
                        $body .= ' Si vous voulez, je peux aussi vous expliquer son usage, sa capacite ou les options de paiement.';
                    }

                    return [
                        'body' => $body,
                        'confidence' => 0.92,
                        'needsHuman' => false,
                        'quickReplies' => ['Caracteristiques', 'Paiement disponible', 'Voir la reduction flash'],
                    ];
                }

                $priceRange = $this->catalogPriceRange();
                $body = $priceRange !== null
                    ? sprintf(
                        'Sur la collection visible en ce moment, les prix vont environ de %s a %s. Dites-moi votre budget ou le type de meuble voulu et je vous oriente.',
                        $this->formatMoney($priceRange['min']),
                        $this->formatMoney($priceRange['max']),
                    )
                    : 'Donnez-moi le meuble vise ou votre budget et je vous aiderai a trouver une option adaptee.';

                return [
                    'body' => $body,
                    'confidence' => 0.8,
                    'needsHuman' => false,
                    'quickReplies' => ['Petit budget', 'Choisir un meuble', 'Parler a un expert'],
                ];

            case 'dimensions':
                if ($product !== null && $productDimensions !== null && $knownDimensions !== null) {
                    $fitsWidth = $knownDimensions['width'] >= $productDimensions['width'];
                    $fitsHeight = $knownDimensions['height'] >= $productDimensions['height'];

                    if ($fitsWidth && $fitsHeight) {
                        return [
                            'body' => sprintf(
                                '%s peut entrer dans votre espace. Le meuble mesure %s cm x %s cm et vous gardez environ %s cm en largeur et %s cm en hauteur.',
                                $productName,
                                $this->formatDimension($productDimensions['width']),
                                $this->formatDimension($productDimensions['height']),
                                $this->formatDimension($knownDimensions['width'] - $productDimensions['width']),
                                $this->formatDimension($knownDimensions['height'] - $productDimensions['height']),
                            ),
                            'confidence' => 0.95,
                            'needsHuman' => false,
                            'quickReplies' => ['Paiement disponible', 'Parler a un expert', 'Voir un autre produit'],
                        ];
                    }

                    return [
                        'body' => sprintf(
                            '%s risque d etre juste pour votre espace. Le meuble mesure %s cm x %s cm et il manque environ %s cm en largeur et %s cm en hauteur.',
                            $productName,
                            $this->formatDimension($productDimensions['width']),
                            $this->formatDimension($productDimensions['height']),
                            $this->formatDimension(max(0, $productDimensions['width'] - $knownDimensions['width'])),
                            $this->formatDimension(max(0, $productDimensions['height'] - $knownDimensions['height'])),
                        ),
                        'confidence' => 0.93,
                        'needsHuman' => false,
                        'quickReplies' => ['Voir un autre produit', 'Parler a un expert', 'Choisir un meuble'],
                    ];
                }

                if ($product !== null && $productDimensions !== null) {
                    return [
                        'body' => sprintf(
                            '%s mesure environ %s cm de largeur, %s cm de hauteur et %s cm de profondeur. Si vous me donnez votre espace disponible, je peux vous dire tout de suite si cela passe.',
                            $productName,
                            $this->formatDimension($productDimensions['width']),
                            $this->formatDimension($productDimensions['height']),
                            $this->formatDimension($productDimensions['depth']),
                        ),
                        'confidence' => 0.91,
                        'needsHuman' => false,
                        'quickReplies' => ['Envoyer mes dimensions', 'Parler a un expert', 'Voir un autre produit'],
                    ];
                }

                if ($knownDimensions !== null) {
                    return [
                        'body' => sprintf(
                            'J ai note un espace d environ %s cm x %s cm. Dites-moi maintenant quel meuble vous visez et je pourrai vous dire s il est compatible.',
                            $this->formatDimension($knownDimensions['width']),
                            $this->formatDimension($knownDimensions['height']),
                        ),
                        'confidence' => 0.79,
                        'needsHuman' => false,
                        'quickReplies' => ['Choisir un meuble', 'Parler a un expert', 'Voir les prix'],
                    ];
                }

                return [
                    'body' => 'Je peux vous aider sur les dimensions. Envoyez simplement votre largeur et votre hauteur disponibles, par exemple 280 x 240 cm, et je vous guiderai.',
                    'confidence' => 0.75,
                    'needsHuman' => false,
                    'quickReplies' => ['280 x 240 cm', 'Choisir un meuble', 'Parler a un expert'],
                ];

            case 'promotion':
                if ($campaign !== null) {
                    $body = sprintf(
                        'Une offre flash est en cours: %s. %s',
                        $campaign->message,
                        filled($campaign->discount_code)
                            ? 'Code a utiliser: '.$campaign->discount_code.'.'
                            : 'Je peux vous diriger vers la campagne active.'
                    );

                    return [
                        'body' => $body,
                        'confidence' => 0.9,
                        'needsHuman' => false,
                        'quickReplies' => ['Activer les alertes flash', 'Voir la reduction', 'Parler a un expert'],
                    ];
                }

                return [
                    'body' => 'Je ne vois pas d offre flash active pour le moment, mais je peux vous aider a comparer les produits ou a trouver une option adaptee a votre budget.',
                    'confidence' => 0.72,
                    'needsHuman' => false,
                    'quickReplies' => ['Choisir un meuble', 'Voir les prix', 'Parler a un expert'],
                ];

            case 'recommendation':
                $recommendations = $this->recommendProducts($product, $normalized, $budgetMax, $budgetSensitive);

                if ($recommendations !== []) {
                    $body = $budgetSensitive && $product !== null
                        ? 'Si vous cherchez une alternative plus accessible, je peux vous orienter vers '.$this->recommendationSentence($recommendations).'.'
                        : 'Pour ce besoin, je regarderais en priorite '.$this->recommendationSentence($recommendations).'.';

                    $body .= ' Si vous me donnez aussi la piece, les dimensions ou le budget, je peux affiner encore.';

                    return [
                        'body' => $body,
                        'confidence' => 0.86,
                        'needsHuman' => false,
                        'quickReplies' => ['Envoyer mes dimensions', 'Voir les prix', 'Parler a un expert'],
                    ];
                }

                return [
                    'body' => 'Je peux vous aider a choisir. Dites-moi simplement la piece a amenager, l espace disponible ou votre budget, et je vous guiderai vers le bon meuble.',
                    'confidence' => 0.7,
                    'needsHuman' => false,
                    'quickReplies' => ['J ai un petit budget', 'Ma piece fait 280 x 240', 'Parler a un expert'],
                ];

            case 'greeting':
                return [
                    'body' => 'Bonjour. Je peux vous guider sur le choix d un meuble, les dimensions, le paiement, la livraison et les reductions flash disponibles.',
                    'confidence' => 0.65,
                    'needsHuman' => false,
                    'quickReplies' => ['Choisir un meuble', 'Delai de livraison', 'Parler a un expert'],
                ];
        }

        return [
            'body' => 'Je peux deja vous aider de maniere concrete. Dites-moi par exemple "je cherche un dressing", "mon espace fait 280 x 240", "je suis a Cotonou" ou "je veux payer avec FedaPay".',
            'confidence' => 0.58,
            'needsHuman' => false,
            'quickReplies' => ['Choisir un meuble', 'Envoyer mes dimensions', 'Parler a un expert'],
        ];
    }

    /**
     * Resolve a WhatsApp support URL when configured.
     */
    private function whatsAppUrl(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) SiteSetting::value('whatsapp_number', (string) config('services.whatsapp.number')));

        if ($number === null || $number === '') {
            return null;
        }

        if (str_starts_with($number, '0')) {
            $number = '229'.substr($number, 1);
        }

        return 'https://wa.me/'.$number;
    }

    /**
     * Choose the strongest intent from one free-text message and its context.
     */
    private function detectIntent(
        string $normalized,
        string $historyContext,
        ?Product $product,
        bool $hasCampaign,
        bool $hasKnownDimensions,
        bool $hasKnownCity,
        bool $budgetSensitive,
    ): string {
        $scores = [
            'thanks' => $this->scoreIntent($normalized, $historyContext, ['merci', 'parfait', 'super', 'top', 'genial']),
            'human' => $this->scoreIntent($normalized, $historyContext, ['expert', 'humain', 'conseiller', 'whatsapp', 'appel', 'contact']),
            'delivery' => $this->scoreIntent($normalized, $historyContext, ['livraison', 'delai', 'livrer', 'installer', 'installation', 'montage', 'transport', 'ville']),
            'payment' => $this->scoreIntent($normalized, $historyContext, ['payer', 'paiement', 'fedapay', 'carte', 'momo', 'moov', 'celtiis', 'mobile money', 'reglement']),
            'price' => $this->scoreIntent($normalized, $historyContext, ['prix', 'tarif', 'cout', 'combien', 'cher', 'budget']),
            'dimensions' => $this->scoreIntent($normalized, $historyContext, ['taille', 'dimension', 'mesure', 'largeur', 'hauteur', 'profondeur', 'espace', 'capacite', 'ca passe', 'ca rentre', 'compatible']),
            'promotion' => $this->scoreIntent($normalized, $historyContext, ['promo', 'promotion', 'reduction', 'flash', 'code', 'remise']),
            'recommendation' => $this->scoreIntent($normalized, $historyContext, ['choisir', 'conseil', 'recommande', 'cherche', 'besoin', 'souhaite', 'veut', 'projet', 'chambre', 'salon', 'couloir', 'studio', 'famille', 'dressing', 'armoire', 'etagere', 'commode']),
            'greeting' => $this->scoreIntent($normalized, $historyContext, ['bonjour', 'salut', 'hello', 'bonsoir']),
        ];

        if ($hasKnownCity) {
            $scores['delivery'] += 3;

            if ($this->containsAny($historyContext, ['livraison', 'delai', 'transport', 'ville'])) {
                $scores['delivery'] += 4;
            }
        }

        if ($hasKnownDimensions) {
            $scores['dimensions'] += 4;

            if ($product !== null || $this->containsAny($historyContext, ['dimension', 'mesure', 'largeur', 'hauteur', 'espace'])) {
                $scores['dimensions'] += 3;
            }
        }

        if ($product !== null) {
            $scores['price'] += 1;
            $scores['dimensions'] += 1;
        }

        if ($budgetSensitive) {
            $scores['recommendation'] += 4;
            $scores['price'] += 2;
        }

        if (! $hasCampaign) {
            $scores['promotion'] -= 1;
        }

        arsort($scores);
        $intent = array_key_first($scores);

        return ($intent !== null && $scores[$intent] > 0) ? $intent : 'fallback';
    }

    /**
     * Score one intent from the current message and the recent history.
     *
     * @param  list<string>  $keywords
     */
    private function scoreIntent(string $normalized, string $historyContext, array $keywords): int
    {
        $score = 0;

        foreach ($keywords as $keyword) {
            $needle = $this->normalize($keyword);

            if ($needle === '') {
                continue;
            }

            if (Str::contains($normalized, $needle)) {
                $score += 4;
            }

            if ($historyContext !== '' && Str::contains($historyContext, $needle)) {
                $score += 1;
            }
        }

        return $score;
    }

    /**
     * Find one compact dimensions tuple inside free text.
     *
     * @return array{width: float, height: float}|null
     */
    private function extractDimensions(string $message): ?array
    {
        $ascii = Str::lower(Str::ascii($message));

        if (preg_match('/largeur\s*(?:de|:)?\s*(\d+(?:[.,]\d+)?)\s*(cm|m)?/i', $ascii, $widthMatch)
            && preg_match('/hauteur\s*(?:de|:)?\s*(\d+(?:[.,]\d+)?)\s*(cm|m)?/i', $ascii, $heightMatch)) {
            return [
                'width' => $this->toCentimeters((float) str_replace(',', '.', $widthMatch[1]), $widthMatch[2] ?? null),
                'height' => $this->toCentimeters((float) str_replace(',', '.', $heightMatch[1]), $heightMatch[2] ?? null),
            ];
        }

        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(cm|m)?\s*(?:x|sur|by)\s*(\d+(?:[.,]\d+)?)\s*(cm|m)?/i', $ascii, $matches)) {
            return [
                'width' => $this->toCentimeters((float) str_replace(',', '.', $matches[1]), $matches[2] ?? null),
                'height' => $this->toCentimeters((float) str_replace(',', '.', $matches[3]), $matches[4] ?? null),
            ];
        }

        return null;
    }

    /**
     * Find the latest user-provided dimensions inside recent history.
     *
     * @param  list<array{role?: string, body?: string}>  $history
     * @return array{width: float, height: float}|null
     */
    private function lastMentionedDimensions(array $history): ?array
    {
        foreach (array_reverse($history) as $entry) {
            if (($entry['role'] ?? '') !== 'user') {
                continue;
            }

            $dimensions = $this->extractDimensions((string) ($entry['body'] ?? ''));

            if ($dimensions !== null) {
                return $dimensions;
            }
        }

        return null;
    }

    /**
     * Find one delivery city from a message or recent history.
     */
    private function extractCity(string $message): ?string
    {
        $normalized = $this->normalize($message);

        foreach ([
            'Cotonou',
            'Porto-Novo',
            'Abomey-Calavi',
            'Parakou',
            'Bohicon',
            'Ouidah',
        ] as $city) {
            if (Str::contains($normalized, $this->normalize($city))) {
                return $city;
            }
        }

        return null;
    }

    /**
     * Find the latest city mention from user messages.
     *
     * @param  list<array{role?: string, body?: string}>  $history
     */
    private function lastMentionedCity(array $history): ?string
    {
        foreach (array_reverse($history) as $entry) {
            if (($entry['role'] ?? '') !== 'user') {
                continue;
            }

            $city = $this->extractCity((string) ($entry['body'] ?? ''));

            if ($city !== null) {
                return $city;
            }
        }

        return null;
    }

    /**
     * Parse one budget ceiling from free text.
     */
    private function extractBudgetMax(string $message): ?int
    {
        $ascii = Str::lower(Str::ascii($message));

        if (preg_match('/(?:moins de|maximum|max|budget(?: de)?|jusqu a|jusqua|en dessous de)\s*(\d[\d\s]{2,})/i', $ascii, $matches)) {
            $digits = preg_replace('/\D+/', '', $matches[1]);

            return $digits !== null && $digits !== '' ? (int) $digits : null;
        }

        return null;
    }

    /**
     * Read the latest meaningful chat context.
     *
     * @param  list<array{role?: string, body?: string}>  $history
     */
    private function historyContext(array $history): string
    {
        $context = collect($history)
            ->take(-6)
            ->map(fn (array $entry): string => trim((string) ($entry['body'] ?? '')))
            ->filter()
            ->implode(' ');

        return $this->normalize($context);
    }

    /**
     * Read one product dimensions tuple from its specifications.
     *
     * @return array{width: float, height: float, depth: float}|null
     */
    private function productDimensions(Product $product): ?array
    {
        foreach ((array) ($product->resolvedContent()['specifications'] ?? []) as $specification) {
            $normalized = str_replace(',', '.', Str::ascii((string) $specification));

            if (! preg_match('/(\d+(?:\.\d+)?)\s*x\s*(\d+(?:\.\d+)?)\s*x\s*(\d+(?:\.\d+)?)/i', $normalized, $matches)) {
                continue;
            }

            return [
                'width' => (float) $matches[1],
                'height' => (float) $matches[2],
                'depth' => (float) $matches[3],
            ];
        }

        return null;
    }

    /**
     * Recommend up to two visible products based on the conversation.
     *
     * @return list<Product>
     */
    private function recommendProducts(?Product $currentProduct, string $normalized, ?int $budgetMax, bool $preferAffordable): array
    {
        $categoryHint = $this->detectCategoryHint($normalized, $currentProduct);

        /** @var Collection<int, Product> $products */
        $products = Product::query()
            ->orderBy('code')
            ->get()
            ->filter(fn (Product $candidate): bool => $candidate->isStorefrontVisible())
            ->values();

        if ($categoryHint !== null) {
            $filteredByCategory = $products
                ->filter(fn (Product $candidate): bool => in_array((string) $candidate->category, $this->categoryGroup($categoryHint), true))
                ->values();

            if ($filteredByCategory->isNotEmpty()) {
                $products = $filteredByCategory;
            }
        }

        if ($budgetMax !== null) {
            $filteredByBudget = $products
                ->filter(fn (Product $candidate): bool => ($candidate->checkoutAmount() ?? PHP_INT_MAX) <= $budgetMax)
                ->values();

            if ($filteredByBudget->isNotEmpty()) {
                $products = $filteredByBudget;
            }
        }

        if ($preferAffordable) {
            $products = $products
                ->sortBy(fn (Product $candidate): int => $candidate->checkoutAmount() ?? PHP_INT_MAX)
                ->values();
        }

        if ($currentProduct !== null) {
            $products = $products
                ->reject(fn (Product $candidate): bool => $candidate->is($currentProduct))
                ->values();
        }

        return $products->take(2)->all();
    }

    /**
     * Detect the strongest category hint in one message.
     */
    private function detectCategoryHint(string $normalized, ?Product $currentProduct): ?string
    {
        $categoryHints = [
            'dressing' => ['dressing', 'suite parentale', 'parentale', 'garde robe', 'famille'],
            'armoire' => ['armoire', 'portes', 'meuble ferme'],
            'etagere' => ['etagere', 'murale', 'colonne', 'bibliotheque', 'couloir', 'bureau'],
            'commode' => ['commode', 'tiroir', 'salon', 'entree'],
        ];

        foreach ($categoryHints as $category => $keywords) {
            if ($this->containsAny($normalized, $keywords)) {
                return $category;
            }
        }

        return $currentProduct?->category;
    }

    /**
     * Resolve one category group for recommendation filtering.
     *
     * @return list<string>
     */
    private function categoryGroup(string $category): array
    {
        return match ($category) {
            'dressing' => ['dressing', 'armoire'],
            'armoire' => ['armoire', 'dressing'],
            default => [$category],
        };
    }

    /**
     * Turn product recommendations into one natural sentence.
     *
     * @param  list<Product>  $products
     */
    private function recommendationSentence(array $products): string
    {
        $snippets = array_map(function (Product $product): string {
            $price = $product->resolvedContent()['home_price'] ?? 'prix sur demande';

            return sprintf('%s (%s)', $product->name, $price);
        }, $products);

        return count($snippets) > 1
            ? implode(' puis ', $snippets)
            : ($snippets[0] ?? 'quelques produits adaptes');
    }

    /**
     * Resolve the current visible catalog price range.
     *
     * @return array{min: int, max: int}|null
     */
    private function catalogPriceRange(): ?array
    {
        $prices = Product::query()
            ->get()
            ->filter(fn (Product $product): bool => $product->isStorefrontVisible())
            ->map(fn (Product $product): ?int => $product->checkoutAmount())
            ->filter(fn (?int $amount): bool => $amount !== null)
            ->values();

        if ($prices->isEmpty()) {
            return null;
        }

        return [
            'min' => (int) $prices->min(),
            'max' => (int) $prices->max(),
        ];
    }

    /**
     * Normalize free text for intent matching.
     */
    private function normalize(string $value): string
    {
        return (string) Str::of(Str::ascii($value))
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/u', ' ')
            ->squish();
    }

    /**
     * Check if the normalized text contains at least one keyword.
     *
     * @param  list<string>  $keywords
     */
    private function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (Str::contains($text, $this->normalize($keyword))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert one raw dimension to centimeters.
     */
    private function toCentimeters(float $value, ?string $unit): float
    {
        return in_array($unit, ['m', 'metre', 'metres'], true)
            ? $value * 100
            : $value;
    }

    /**
     * Format one money amount for storefront replies.
     */
    private function formatMoney(int $amount): string
    {
        return number_format($amount, 0, ',', ' ').' FCFA';
    }

    /**
     * Format one dimension value without noisy decimals.
     */
    private function formatDimension(float $value): string
    {
        return fmod($value, 1.0) === 0.0
            ? (string) (int) $value
            : number_format($value, 1, '.', '');
    }
}
