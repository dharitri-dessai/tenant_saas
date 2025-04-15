const defaultTheme = require('tailwindcss/defaultTheme');
module.exports = {
    content: [
        "./assets/**/*.{vue,js,ts,jsx,tsx,css}",
        "./templates/**/*.{html,twig}",
        "./src/**/*.php",
    ],
    theme: {
      extend: {
          fontFamily: {
              sans: ['Inter var', ...defaultTheme.fontFamily.sans],
          },
      }
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}