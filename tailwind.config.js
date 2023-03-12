/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        registrationPrimaryColor: '#034889',
        registrationInputFieldsBGColor: '#F5F5F5',
        registrationSecondaryColor: '#D9A125',
        registrationCardBGColor: '#E3EDF6',
      },
      gridTemplateColumns: {
        addDelegateGrid: '250px auto',
      },
    },
  },
  plugins: [],
}