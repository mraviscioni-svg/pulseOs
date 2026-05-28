/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./app/views/**/*.php'],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        pulse: {
          400: '#f0c96a',
          500: '#e8b44a',
          600: '#4f46e5',
          700: '#4338ca',
        },
      },
    },
  },
  plugins: [],
};
