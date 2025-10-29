# MyMoneyBox Color Scheme

## Primary Color - Deeper Green
The primary color is a deeper green (emerald/green scale), used for main branding, CTAs, and important UI elements.

| Shade | Hex Code | Usage |
|-------|----------|-------|
| primary-50 | #f0fdf4 | Very light backgrounds, hover states |
| primary-100 | #dcfce7 | Light backgrounds |
| primary-200 | #bbf7d0 | Subtle highlights |
| primary-300 | #86efac | Muted accents |
| primary-400 | #4ade80 | Light accents |
| primary-500 | #22c55e | Medium green |
| **primary-600** | **#16a34a** | **Main primary color** ⭐ |
| primary-700 | #15803d | Deeper primary for contrast |
| primary-800 | #166534 | Dark primary |
| primary-900 | #14532d | Very dark primary |
| primary-950 | #052e16 | Almost black green |

## Secondary Color - Lighter Green
The secondary color is a lighter green, used for secondary actions, accents, and complementary elements.

| Shade | Hex Code | Usage |
|-------|----------|-------|
| secondary-50 | #f0fdf4 | Very light backgrounds |
| secondary-100 | #dcfce7 | Light backgrounds |
| secondary-200 | #bbf7d0 | Subtle highlights |
| secondary-300 | #86efac | Muted accents |
| **secondary-400** | **#4ade80** | **Main secondary color** ⭐ |
| secondary-500 | #22c55e | Medium secondary |
| secondary-600 | #16a34a | Deeper secondary |
| secondary-700 | #15803d | Dark secondary |
| secondary-800 | #166534 | Darker secondary |
| secondary-900 | #14532d | Very dark secondary |

## Usage Examples

### Tailwind CSS Classes

```html
<!-- Primary Color Usage -->
<button class="bg-primary-600 hover:bg-primary-700 text-white">
  Click Me
</button>

<div class="text-primary-600 border-primary-600">
  Primary Text
</div>

<!-- Secondary Color Usage -->
<button class="bg-secondary-400 hover:bg-secondary-500 text-white">
  Secondary Action
</button>

<div class="text-secondary-400">
  Secondary Text
</div>

<!-- Gradients -->
<div class="bg-gradient-to-r from-primary-600 to-primary-800">
  Gradient Background
</div>
```

## Current Implementation

The colors are defined in `/resources/css/app.css` using Tailwind CSS 4's `@theme` directive:

```css
@theme {
  /* Primary Colors - Deeper Green */
  --color-primary-600: #16a34a;  /* Main primary */
  --color-primary-700: #15803d;  /* Hover/Active states */

  /* Secondary Colors - Lighter Green */
  --color-secondary-400: #4ade80;  /* Main secondary */
  --color-secondary-500: #22c55e;  /* Hover states */
}
```

## Where Colors Are Used

- **Navigation**: Logo icon uses `text-primary-600`
- **Hero Section**: Background gradient `from-primary-600 via-primary-700 to-primary-900`
- **Buttons**: Primary CTAs use `bg-primary-600 hover:bg-primary-700`
- **Links**: Text links use `text-primary-600 hover:text-primary-700`
- **Feature Cards**: Icons use various primary shades
- **Progress Bars**: Use gradient `from-primary-500 to-primary-600`

## Rebuilding Assets

After making changes to colors, rebuild the assets:

```bash
npm run build
# or for development
npm run dev
```
