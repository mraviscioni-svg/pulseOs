/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./app/views/**/*.php'],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        pulse: {
          500: '#6366f1',
          600: '#4f46e5',
          700: '#4338ca',
        },
      },
    },
  },
  plugins: [],
};
