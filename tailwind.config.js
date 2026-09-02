/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'selector',
  content: [
    "./resources/Views/**/*.lady.php",
    "./resources/Layouts/**/*.lady.php",
    "./app/Modules/**/*.lady.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        'vazir': ['Vazir', 'sans-serif'],
        'faraz': ['Faraz', 'sans-serif'],
        'titr': ['Titr', 'sans-serif'],
        'traffic': ['Traffic'],
        'irsans': ['IranSans'],
        // می‌توانید فونت پیش‌فرض را هم تغییر دهید
        'sans': ['Vazir', 'system-ui', 'sans-serif'],
      },
      colors: {
        'brand-green': {
          50:  '#f0f7f2',
          100: '#dcebe1',
          200: '#b9d7c3',
          300: '#94c2a3',
          400: '#70ad84',
          500: '#4f9868', // رنگ اصلی
          600: '#3d7a53',
          700: '#2d5d3f',
          800: '#1e402b',
          900: '#0f2417',
        },
        'light-green': {
          25:  '#f6fdf9',   // تقریباً سفید با بوی سبز
          50:  '#ecf9f1',
          100: '#d4f0df',
          200: '#a8e0bf',
          300: '#7cd09f',
          400: '#50c07f',
          500: '#34b06f',   // ➕ رنگ اصلی - روشن و شاداب
          600: '#2a8d58',
          700: '#1f6a42',
          800: '#15482c',
          900: '#0a2516',
        }
      },
    },
  },
  plugins: [],
}