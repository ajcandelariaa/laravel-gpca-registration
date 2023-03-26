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
        registrationPrimaryColorHover: '#0358a8',
        registrationInputFieldsBGColor: '#F5F5F5',
        registrationSecondaryColor: '#D9A125',
        registrationCardBGColor: '#E3EDF6',
        dashboardNavItemHoverColor: '#F9BC35',
        adminEventDetailNavigationBGColor: '#E3EDF7',
      },
      gridTemplateColumns: {
        addDelegateGrid: '250px auto',
        delegateDetailGrid: '200px auto',
      },
      backgroundImage: {
        loginBg: "url('/public/assets/images/loginbg.png')",
      },
    },
  },
  plugins: [],
}