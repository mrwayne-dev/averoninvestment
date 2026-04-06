<?php
/**
 * includes/icons.php
 * Phosphor icon helper — sources SVGs from assets/icons/core-main/assets/
 *
 * Usage:
 *   <?= ph('house') ?>                    regular, 20 px
 *   <?= ph('bell', 18) ?>                 regular, 18 px
 *   <?= ph('crown', 26, 'bold') ?>        bold, 26 px
 *   <?= ph('wallet', 18, 'regular', 'my-class') ?>   with extra CSS class
 */

/**
 * Render a Phosphor icon as an inline SVG element.
 *
 * @param  string  $name    Icon name without suffix, e.g. 'house', 'wallet'
 * @param  int     $size    Width & height in pixels (default 20)
 * @param  string  $weight  'regular' (default) | 'bold'
 * @param  string  $class   Optional CSS class to add to the <svg> element
 * @return string           Inline SVG HTML — safe to echo directly
 */
function ph(string $name, int $size = 20, string $weight = 'regular', string $class = ''): string
{
    static $cache = [];

    $cacheKey = "{$weight}/{$name}";

    if (!array_key_exists($cacheKey, $cache)) {
        // Regular weight:  assets/regular/house.svg
        // Bold weight:     assets/bold/house-bold.svg
        $suffix = ($weight === 'regular') ? '' : "-{$weight}";
        $base   = dirname(__DIR__) . '/assets/icons/core-main/assets';
        $file   = "{$base}/{$weight}/{$name}{$suffix}.svg";

        $cache[$cacheKey] = is_file($file) ? (string) file_get_contents($file) : '';
    }

    if ($cache[$cacheKey] === '') {
        // Icon not found — return a transparent placeholder of the right size
        return "<svg aria-hidden=\"true\" width=\"{$size}\" height=\"{$size}\" "
             . "viewBox=\"0 0 256 256\" fill=\"currentColor\"></svg>";
    }

    // Build extra attributes to inject into the opening <svg …> tag
    $attrs  = " aria-hidden=\"true\" width=\"{$size}\" height=\"{$size}\"";
    if ($class !== '') {
        $attrs .= ' class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"';
    }

    // Inject after the first <svg (before any existing attributes)
    return (string) preg_replace('/<svg/', "<svg{$attrs}", $cache[$cacheKey], 1);
}
