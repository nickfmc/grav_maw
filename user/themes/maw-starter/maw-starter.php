<?php
namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Media\Interfaces\MediaObjectInterface;
use Grav\Common\Page\Interfaces\PageInterface;
use Grav\Common\Page\Medium\MediumFactory;
use Grav\Common\Theme;
use Grav\Common\Yaml;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * MAW Starter theme.
 *
 * Keep this class small: it only exposes helpers to Twig. All markup lives in
 * templates/, all block schemas live in blueprints/blocks/. See AGENTS.md.
 */
class MawStarter extends Theme
{
    /** @var array<string,array>|null */
    protected ?array $catalog = null;

    public static function getSubscribedEvents(): array
    {
        // Subscribe statically. Never gate on isAdmin() (Admin2 sets admin context late).
        return [
            'onTwigInitialized' => ['onTwigInitialized', 0],
        ];
    }

    public function onTwigInitialized(): void
    {
        $twig = $this->grav['twig'];

        // Class hooks consumed by the Form plugin's templates.
        $twig->twig_vars = array_merge($twig->twig_vars, [
            'form_button_outer_classes'   => 'form-actions',
            'form_button_classes'         => 'btn btn-primary',
            'form_errors_classes'         => 'form-errors',
            'form_field_outer_classes'    => 'form-field',
            'form_field_label_classes'    => 'form-label',
            'form_field_input_classes'    => 'form-input',
            'form_field_textarea_classes' => 'form-input',
            'form_field_select_classes'   => 'form-input',
            'form_field_radio_classes'    => 'form-choice',
            'form_field_checkbox_classes' => 'form-choice',
        ]);

        $env = $twig->twig;
        $env->addFunction(new TwigFunction('maw_blocks', [$this, 'blockCatalog']));
        $env->addFunction(new TwigFunction('maw_media', [$this, 'resolveMedia']));
        $env->addFilter(new TwigFilter('fa_icon', [$this, 'faIconClass']));
        $env->addFilter(new TwigFilter('maw_embed_url', [$this, 'embedUrl']));
        $env->addFilter(new TwigFilter('maw_slug', [$this, 'slug']));
        $env->addFilter(new TwigFilter('maw_contrast', [$this, 'contrastTone']));
        // Inline editing marker: ` data-maw-edit="heading"` inside the builder preview, empty on the live site.
        $env->addFunction(new TwigFunction('maw_edit', [$this, 'editAttribute'], ['is_safe' => ['html']]));
        // Same for Markdown output wrappers: ` data-maw-edit-md="text"` (+ data-maw-md-inline for |markdown(false)).
        $env->addFunction(new TwigFunction('maw_edit_md', [$this, 'editMarkdownAttribute'], ['is_safe' => ['html']]));
        // Image fields: ` data-maw-edit-image="image"` so the builder can open its media picker on click.
        $env->addFunction(new TwigFunction('maw_edit_image', [$this, 'editImageAttribute'], ['is_safe' => ['html']]));
        // Repeater (list) fields: container ` data-maw-list="items" data-maw-list-label="question"`, items ` data-maw-item="0"`.
        $env->addFunction(new TwigFunction('maw_edit_list', [$this, 'editListAttribute'], ['is_safe' => ['html']]));
        $env->addFunction(new TwigFunction('maw_edit_item', [$this, 'editItemAttribute'], ['is_safe' => ['html']]));
        // `{% if x is maw_medium %}` — true for Grav media objects (resizable), false for URL strings.
        $env->addTest(new TwigTest('maw_medium', static fn ($v) => $v instanceof MediaObjectInterface));
    }

    /**
     * Block catalogue built from blueprints/blocks/*.yaml.
     * Returns [type => ['type', 'title', 'description', 'icon', 'example']].
     */
    public function blockCatalog(): array
    {
        if ($this->catalog !== null) {
            return $this->catalog;
        }

        $this->catalog = [];
        $dir = __DIR__ . '/blueprints/blocks';
        foreach (glob($dir . '/*.yaml') ?: [] as $file) {
            $type = basename($file, '.yaml');
            if ($type[0] === '_') {
                continue;
            }
            $data = Yaml::parse((string) file_get_contents($file)) ?: [];
            $this->catalog[$type] = [
                'type'        => $type,
                'title'       => $data['title'] ?? ucfirst($type),
                'description' => $data['description'] ?? '',
                'icon'        => $data['icon'] ?? 'fa-square',
                'example'     => $data['example'] ?? [],
            ];
        }
        ksort($this->catalog);

        return $this->catalog;
    }

    /**
     * Resolve an image reference from content into a Medium (resizable) or a URL string.
     *
     * Accepts: a page media filename ("hero.jpg"), a stream ("user://media/x.jpg",
     * "theme://images/x.svg"), a root path ("/user/media/x.jpg") or a full URL.
     *
     * `$owner` is the page (or Flex object) whose folder holds bare filenames; defaults to the current page.
     *
     * @return MediaObjectInterface|string|null
     */
    public function resolveMedia($ref, $owner = null)
    {
        if (is_array($ref)) {
            // Admin2 media fields may store a list or an object with a path/name.
            $ref = $ref['path'] ?? $ref['name'] ?? (reset($ref) ?: null);
            if (is_array($ref)) {
                $ref = $ref['path'] ?? $ref['name'] ?? null;
            }
        }
        $ref = trim((string) $ref);
        if ($ref === '') {
            return null;
        }

        if (preg_match('#^(https?:)?//#i', $ref) || str_starts_with($ref, 'data:')) {
            return $ref;
        }

        $grav = Grav::instance();
        $owner = $owner ?? $grav['page'] ?? null;
        // Pages expose media(); Flex objects expose getMedia().
        $media = match (true) {
            $owner instanceof PageInterface => $owner->media(),
            is_object($owner) && method_exists($owner, 'getMedia') => $owner->getMedia(),
            default => null,
        };

        if (!str_contains($ref, '://') && !str_starts_with($ref, '/') && $media) {
            $medium = $media->get($ref);
            if ($medium) {
                return $medium;
            }
            $ref = 'theme://images/' . $ref;
        }

        if (str_starts_with($ref, '/')) {
            $ref = GRAV_ROOT . $ref;
        }

        $locator = $grav['locator'];
        $path = str_contains($ref, '://') ? $locator->findResource($ref, true) : $ref;
        if ($path && is_file($path)) {
            return MediumFactory::fromFile($path) ?: null;
        }

        return null;
    }

    /**
     * Normalise an icon value (Admin2 icon picker, bare name or FA4 style) to FA classes.
     */
    public function faIconClass(?string $icon): string
    {
        $icon = trim((string) $icon);
        if ($icon === '') {
            return '';
        }
        $families = ['fa-solid' => 'fa-solid', 'fas' => 'fa-solid', 'fa-regular' => 'fa-regular',
            'far' => 'fa-regular', 'fa-brands' => 'fa-brands', 'fab' => 'fa-brands'];
        $family = '';
        $name = '';
        foreach (preg_split('/\s+/', $icon) ?: [] as $token) {
            if (isset($families[$token])) {
                $family = $families[$token];
            } elseif ($token !== 'fa' && $token !== 'fa-classic') {
                $name = preg_replace('/^fa-/', '', $token);
            }
        }

        return $name === '' ? '' : ($family ?: 'fa-solid') . ' fa-' . $name;
    }

    /**
     * Turn a YouTube/Vimeo page URL into its embed URL. Other https URLs pass through; anything else returns ''.
     */
    public function embedUrl(?string $url): string
    {
        $url = trim((string) $url);
        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1];
        }
        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return str_starts_with($url, 'https://') ? $url : '';
    }

    /**
     * 'light' or 'dark': which text tone reads best on a hex background (WCAG relative luminance).
     */
    public function contrastTone(?string $hex): string
    {
        $hex = ltrim((string) $hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (!preg_match('/^[0-9a-f]{6}$/i', $hex)) {
            return 'dark';
        }
        $lin = static function (int $c): float {
            $c /= 255;
            return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        };
        [$r, $g, $b] = array_map('hexdec', str_split($hex, 2));
        $l = 0.2126 * $lin($r) + 0.7152 * $lin($g) + 0.0722 * $lin($b);

        // Pick whichever of white (L=1) or near-black (#0f172a, L≈0.0097) gives the higher contrast ratio.
        return (1.05 / ($l + 0.05)) >= (($l + 0.05) / 0.0597) ? 'light' : 'dark';
    }

    /**
     * Marks a plain-text element as inline-editable in the MAW Builder preview.
     * `$path` is the field path inside the block's content, e.g. "heading", "items.2.title", "buttons.0.label".
     * Only for single-line text fields: Markdown fields stay in the builder's side panel.
     */
    public function editAttribute(string $path): string
    {
        if (!isset($this->grav['maw_preview']) || !preg_match('/^[a-z0-9_]+(\.[a-z0-9_]+)*$/i', $path)) {
            return '';
        }

        return ' data-maw-edit="' . htmlspecialchars($path, ENT_QUOTES) . '"';
    }

    /**
     * Marks the wrapper of a Markdown field as inline-editable in the MAW Builder preview.
     * Put it on the element whose children are the rendered Markdown. `$inline` = rendered with |markdown(false)
     * (no paragraphs/lists), so the editor only allows bold, italic and links.
     */
    public function editMarkdownAttribute(string $path, bool $inline = false): string
    {
        if (!isset($this->grav['maw_preview']) || !preg_match('/^[a-z0-9_]+(\.[a-z0-9_]+)*$/i', $path)) {
            return '';
        }

        return ' data-maw-edit-md="' . htmlspecialchars($path, ENT_QUOTES) . '"' . ($inline ? ' data-maw-md-inline="1"' : '');
    }

    /** Marks an image (or its placeholder) as click-to-replace in the MAW Builder preview. */
    public function editImageAttribute(string $path): string
    {
        if (!isset($this->grav['maw_preview']) || !preg_match('/^[a-z0-9_]+(\.[a-z0-9_]+)*$/i', $path)) {
            return '';
        }

        return ' data-maw-edit-image="' . htmlspecialchars($path, ENT_QUOTES) . '"';
    }

    /**
     * Marks the element that directly contains a repeater's items, so the builder can offer "+ Add <label>" inline.
     * `$label` is the singular noun shown on the button ("question", "plan", "logo").
     */
    public function editListAttribute(string $path, string $label = 'item'): string
    {
        if (!isset($this->grav['maw_preview']) || !preg_match('/^[a-z0-9_]+(\.[a-z0-9_]+)*$/i', $path)) {
            return '';
        }

        return ' data-maw-list="' . htmlspecialchars($path, ENT_QUOTES) . '" data-maw-list-label="' . htmlspecialchars($label, ENT_QUOTES) . '"';
    }

    /** Marks one repeater item (its index in the stored list) for the inline move / duplicate / delete toolbar. */
    public function editItemAttribute(int|string $index): string
    {
        if (!isset($this->grav['maw_preview']) || !ctype_digit((string) $index)) {
            return '';
        }

        return ' data-maw-item="' . (int) $index . '"';
    }

    public function slug(?string $value): string
    {
        return trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower((string) $value)), '-');
    }
}
