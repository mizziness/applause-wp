module.exports = function(grunt) {

    grunt.initConfig({
      pkg: grunt.file.readJSON('package.json'),

      clean: ['wp-content/themes/applause/dist/**/*'],
      
      sass: {
        dist: {
          files: [
            {
              expand: true,
              cwd: 'wp-content/themes/applause/src/css',
              src: ['**/*.scss'],
              dest: 'wp-content/themes/applause/dist/css',
              ext: '.css'
            }
          ]
        }
      },

      concat: {
        options: {
          stripBanners: true,
          banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
            '<%= grunt.template.today("yyyy-mm-dd") %> */',
        },
        css: {
          src: ['wp-content/themes/applause/dist/css/*.css', '!admin.css'],
          dest: 'wp-content/themes/applause/dist/app-build.css',
        },
        folders: {
          src: 'wp-content/themes/applause/dist/css/*/*.css',
          dest: 'wp-content/themes/applause/dist/extras-build.css'
        }
      },
      
    });


    // Handle SCSS
    grunt.loadNpmTasks('grunt-contrib-sass');

    // Merge CSS Files
    grunt.loadNpmTasks('grunt-contrib-concat');

    // Clean folders 
    grunt.loadNpmTasks('grunt-contrib-clean');
  
  };