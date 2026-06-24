# Tailwind CSS Setup for POSVENTA

This project uses Tailwind CSS for styling, configured via PostCSS.

## Files

- `tailwind.config.js` - Tailwind configuration file
- `postcss.config.js` - PostCSS configuration file
- `public/css/tailwind.css` - Input CSS file with Tailwind directives
- `public/css/style.css` - Output CSS file (compiled Tailwind CSS)

## Installation

1. Install Node.js dependencies:
   ```bash
   npm install
   ```

## Usage

### Development (with watch mode)
```bash
npm run watch:css
```
This will watch for changes in your Tailwind CSS and automatically rebuild the stylesheet.

### Production Build
```bash
npm run build:css
```
This will build the CSS once for production deployment.

## Important Notes

- The old Tailwind CDN link (`<script src="https://cdn.tailwindcss.com"></script>`) has been removed from `app/views/inc/header.php`
- The compiled CSS is now included via `<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">` in the header
- Do not edit `public/css/style.css` directly as it is generated from `tailwind.css`
- To customize Tailwind, edit `tailwind.config.js`
- To add custom CSS, add it to `public/css/tailwind.css` using Tailwind's `@layer` directives or add custom CSS outside of the Tailwind directives

## Tailwind Directives in tailwind.css

The `tailwind.css` file contains the standard Tailwind directives:
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

Additional custom styles can be added before, between, or after these directives using Tailwind's layer system.