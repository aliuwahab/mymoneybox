# MyPiggyBox Color Scheme

## Primary Color - Facebook Blue
The primary color is Facebook blue (#1877F2), used for main branding, CTAs, and important UI elements.

| Shade | Hex Code | Usage |
|-------|----------|-------|
| primary-50 | #e8f2fe | Very light backgrounds, hover states |
| primary-100 | #d1e5fd | Light backgrounds |
| primary-200 | #a3cbfb | Subtle highlights |
| primary-300 | #75b1f9 | Muted accents |
| primary-400 | #4697f7 | Light accents |
| primary-500 | #1877f2 | Medium blue |
| **primary-600** | **#1877f2** | **Main primary color (Facebook Blue)** ⭐ |
| primary-700 | #155fcb | Deeper primary for contrast |
| primary-800 | #1147a4 | Dark primary |
| primary-900 | #0d2f7d | Very dark primary |
| primary-950 | #091856 | Almost black blue |

## Secondary Color - Amber/Orange
The secondary color is a warm amber/orange, used for secondary actions, accents, and complementary elements.

| Shade | Hex Code | Usage |
|-------|----------|-------|
| secondary-50 | #fffbeb | Very light backgrounds |
| secondary-100 | #fef3c7 | Light backgrounds |
| secondary-200 | #fde68a | Subtle highlights |
| secondary-300 | #fcd34d | Muted accents |
| secondary-400 | #fbbf24 | Light accents |
| **secondary-500** | **#f59e0b** | **Main secondary color** ⭐ |
| secondary-600 | #d97706 | Deeper secondary |
| secondary-700 | #b45309 | Dark secondary |
| secondary-800 | #92400e | Darker secondary |
| secondary-900 | #78350f | Very dark secondary |

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
  /* Primary Colors - Facebook Blue */
  --color-primary-600: #1877f2;  /* Main primary (Facebook Blue) */
  --color-primary-700: #155fcb;  /* Hover/Active states */

  /* Secondary Colors - Amber/Orange */
  --color-secondary-500: #f59e0b;  /* Main secondary */
  --color-secondary-600: #d97706;  /* Hover states */
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
