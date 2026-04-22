# Theming Guide

StreamSuite uses CSS custom properties (variables) for all colors, spacing, and typography. Themes are defined in `assets/css/themes.css` and activated via a `data-theme` attribute on `<html>`.

---

## Built-in Themes

| Theme ID | Description |
|---|---|
| `obsidian` | Default. Dark near-black with a warm gold accent |
| `midnight` | Dark navy with a periwinkle-blue accent |
| `forest` | Dark green tones with a mint accent |
| `ember` | Dark warm brown with an orange-red accent |
| `paper` | Light off-white with a dark charcoal accent |

Activate in HTML:
```html
<html lang="en" data-theme="midnight">
```

Activate via JavaScript (and persist in preferences):
```javascript
document.documentElement.dataset.theme = 'midnight';
const prefs = JSON.parse(localStorage.getItem('ss_prefs') || '{}');
prefs.theme = 'midnight';
localStorage.setItem('ss_prefs', JSON.stringify(prefs));
```

---

## CSS Variables Reference

All variables are defined on `:root` in `assets/css/tokens.css` and overridden per-theme in `assets/css/themes.css`.

### Colors

| Variable | Description |
|---|---|
| `--c-bg` | Page background (darkest) |
| `--c-bg-2` | Card and section backgrounds |
| `--c-bg-3` | Input and control backgrounds |
| `--c-bg-4` | Hover states, disabled elements |
| `--c-surface` | Elevated surfaces (modals, drawers) |
| `--c-border` | Default border color |
| `--c-border-2` | Active/hover border color |
| `--c-text` | Primary text |
| `--c-text-2` | Secondary/muted text |
| `--c-text-3` | Placeholder and disabled text |
| `--c-accent` | Brand accent color (links, buttons, ratings) |
| `--c-accent-dim` | Translucent accent (backgrounds of active states) |
| `--c-accent-glow` | Very faint accent (hero glow effects) |
| `--c-red` | Destructive actions |
| `--c-green` | Success states |
| `--c-blue` | Informational |
| `--c-overlay` | Semi-transparent overlay (backdrops, scrims) |

### Typography

| Variable | Description |
|---|---|
| `--font-display` | Heading font stack |
| `--font-body` | Body text font stack |
| `--font-mono` | Monospace font (labels, metadata, badges) |
| `--font-size-base` | Root font size (default 15px, adjustable in settings) |

### Spacing & Shape

| Variable | Description |
|---|---|
| `--radius-s` | Small border radius (4px) |
| `--radius-m` | Medium border radius (8px) |
| `--radius-l` | Large border radius (14px) |
| `--radius-xl` | Extra-large border radius (22px) |
| `--shadow-s` | Subtle shadow |
| `--shadow-m` | Card shadow |
| `--shadow-l` | Floating element shadow (modals, posters) |

### Layout

| Variable | Description |
|---|---|
| `--header-h` | Fixed header height (60px) |
| `--sidebar-w` | Reserved sidebar width (240px) |
| `--max-content` | Max content width (1400px) |

---

## Creating a Custom Theme

Add a new block to `assets/css/themes.css`:

```css
[data-theme="custom"] {
    --c-bg:         #1a1a2e;
    --c-bg-2:       #16213e;
    --c-bg-3:       #0f3460;
    --c-bg-4:       #1a3a6e;
    --c-surface:    #1e4080;
    --c-border:     #1e4080;
    --c-border-2:   #2a5298;

    --c-text:       #e0e0f0;
    --c-text-2:     #8888aa;
    --c-text-3:     #555577;

    --c-accent:     #e94560;
    --c-accent-dim: rgba(233, 69, 96, 0.12);
    --c-accent-glow: rgba(233, 69, 96, 0.06);
}
```

Then add it to the `THEMES` array in `assets/js/settings.js` and the `themeColors` map (for the swatch preview).

---

## Font Options

Three font stacks are available, toggled by the `data-font` attribute on `<html>`:

| Value | Font |
|---|---|
| _(default)_ | Satoshi (self-hosted WOFF2) |
| `mono` | DM Mono (self-hosted WOFF2) |
| `system` | `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, ...` |

```javascript
document.documentElement.dataset.font = 'mono';
```
