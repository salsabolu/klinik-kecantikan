const { h } = require('vue');

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        'serif': ['Instrument Serif', 'serif'],
        'sans': ['Neue Montreal', 'sans-serif'],
      },
      backgroundColor: {
        'primary-bg': '#FCFCF7',
      },
      colors: {
        'clinic-yellow': { DEFAULT: '#FFE95C', hover: '#FDD412' },
        'clinic-green': { DEFAULT: '#1A3300' },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('daisyui'),
  ],
  daisyui: {
    themes: [
      {
        light: {
          "primary": "#FFE95C",
          "primary-content": "#1A3300",
          "secondary": "#1A3300",
          "secondary-content": "#FFFFFF",
          "accent": "#10b981",
          "neutral": "#374151",
          "base-100": "#FCFCF7",
          "base-200": "#f3f4f6",
          "base-300": "#e5e7eb",
          "info": "#3abff8",
          "success": "#36d399",
          "warning": "#fbbd23",
          "error": "#f87272",
        },
      },
      "dark"
    ],
  },
}
