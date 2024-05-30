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
        registrationSecondaryColorHover: '#ab8020',
        registrationCardBGColor: '#E3EDF6',
        dashboardNavItemHoverColor: '#F9BC35',
        adminEventDetailNavigationBGColor: '#E3EDF7',
      },
      gridTemplateColumns: {
        addDelegateGrid: '250px auto',
        horizontalProgressBarGrid: '55px auto 55px auto 55px auto 55px auto 55px',
        horizontalProgressBarGridSpouse: '55px auto 55px auto 55px',
        horizontalProgressBarGridRCCAwards: '55px auto 55px auto 55px auto 55px',
        addDelegateFeeGrid: 'auto 100px',
        delegateFeesGrid: 'auto 150px',
        delegateDetailGrid: '320px auto',
        delegateDetailGrid2: '200px auto',
        '13': 'repeat(13, minmax(0, 1fr))',
      },
      backgroundImage: {
        loginBg: "url('/public/assets/images/loginbg.png')",
      },
      marginLeft: {
        '360': '360px',
      },
      fontFamily: {
          'montserrat': ['Montserrat'],
      }
    },
  },
  plugins: [],
}