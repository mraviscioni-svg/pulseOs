/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./app/views/**/*.php'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
      },
      colors: {
        navy: {
          50: '#f0f4fa',
          100: '#dce4f0',
          200: '#b8c5db',
          600: '#243b5a',
          800: '#152238',
          900: '#0c1524',
          950: '#070d18',
        },
        accent: {
          50: '#eef6ff',
          100: '#d9ebff',
          200: '#b8d9ff',
          500: '#3b82f6',
          600: '#2563eb',
        },
        surface: {
          DEFAULT: '#f3f5f9',
          card: '#ffffff',
          muted: '#eef1f6',
        },
      },
      boxShadow: {
        card: '0 1px 2px rgba(15, 23, 42, 0.04), 0 4px 16px rgba(15, 23, 42, 0.06)',
        soft: '0 1px 3px rgba(15, 23, 42, 0.08)',
      },
    },
  },
  plugins: [],
};
