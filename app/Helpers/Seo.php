<?php

namespace App\Helpers;

use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Seo
{
    /**
     * Generate SEO meta tags for a page.
     *
     * @param string|null $title     Page title
     * @param string|null $description  Meta description
     * @param string      $type      Schema type: 'website', 'game', 'article'
     * @param string|null $image     OG image URL
     * @param array       $extra     Extra data (e.g. published_time, tags, section, breadcrumbs, carousel)
     */
    public static function metaTags(
        ?string $title = null,
        ?string $description = null,
        string $type = 'website',
        ?string $image = null,
        array $extra = [],
    ): SEOData {
        $schema = self::getSchema($type, $extra);

        if (isset($extra['breadcrumbs'])) {
            $schema->push(self::breadcrumbSchema($extra['breadcrumbs']));
        }

        if (isset($extra['carousel'])) {
            $schema->push(self::carouselSchema($extra['carousel']));
        }

        return new SEOData(
            title: $title,
            description: $description,
            image: $image,
            schema: $schema,
            locale: app()->getLocale(),
            published_time: $extra['published_time'] ?? null,
            modified_time: $extra['modified_time'] ?? null,
            section: $extra['section'] ?? null,
            tags: $extra['tags'] ?? null,
        );
    }

    /**
     * Build structured data schema based on page type.
     */
    protected static function getSchema(string $type, array $extra = []): SchemaCollection
    {
        $schema = SchemaCollection::initialize();

        $organization = [
            '@type'  => 'Organization',
            'name'   => config('app.name', 'Rakkez'),
            'url'    => config('app.url'),
            'logo'   => asset('logo.png'),
        ];

        match ($type) {
            'website' => $schema->push([
                '@context' => 'https://schema.org',
                '@type'    => 'WebSite',
                'name'     => config('app.name', 'Rakkez'),
                'url'      => config('app.url'),
                'inLanguage' => app()->getLocale(),
                'publisher' => $organization,
                'potentialAction' => [
                    '@type'  => 'SearchAction',
                    'target' => config('app.url') . '/' . app()->getLocale() . '?search={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ]),

            'game' => $schema->push([
                '@context' => 'https://schema.org',
                '@type'    => ['VideoGame', 'SoftwareApplication'],
                'name'     => $extra['game_name'] ?? config('app.name'),
                'description' => $extra['game_description'] ?? '',
                'image'    => $extra['game_image'] ?? '',
                'url'      => $extra['game_url'] ?? url()->current(),
                'genre'    => $extra['section'] ?? '',
                'gamePlatform' => 'Web Browser',
                'applicationCategory' => 'GameApplication',
                'operatingSystem' => 'Any',
                'offers' => [
                    '@type'         => 'Offer',
                    'price'         => '0',
                    'priceCurrency' => 'USD',
                    'availability'  => 'https://schema.org/InStock',
                ],
                'publisher' => $organization,
                'aggregateRating' => isset($extra['rating_value']) ? [
                    '@type' => 'AggregateRating',
                    'ratingValue' => max(1, round($extra['rating_value'] * 5, 1)),
                    'reviewCount' => $extra['review_count'] ?? 1,
                    'bestRating' => '5',
                    'worstRating' => '1',
                ] : null,
            ]),

            'article' => $schema->push([
                '@context' => 'https://schema.org',
                '@type'    => 'Article',
                'headline' => $extra['headline'] ?? '',
                'publisher' => $organization,
            ]),

            default => $schema->push([
                '@context' => 'https://schema.org',
                '@type'    => 'WebPage',
                'name'     => config('app.name', 'Rakkez'),
                'url'      => url()->current(),
                'publisher' => $organization,
            ]),
        };

        return $schema;
    }

    /**
     * Build breadcrumb structured data.
     */
    protected static function breadcrumbSchema(array $breadcrumbs): array
    {
        $listItems = [];
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }

    /**
     * Build carousel (ItemList) structured data.
     */
    protected static function carouselSchema(array $carousel): array
    {
        $listItems = [];
        foreach ($carousel as $index => $item) {
            $listItem = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => $item['url'] ?? null,
            ];

            if (isset($item['name'])) {
                $listItem['name'] = $item['name'];
            }

            if (isset($item['image'])) {
                $listItem['image'] = $item['image'];
            }

            $listItems[] = $listItem;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $listItems,
        ];
    }
}
