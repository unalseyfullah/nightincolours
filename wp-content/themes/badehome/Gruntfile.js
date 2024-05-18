module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    less: {
      development: {
        files: {
          "css/layout.css": "css/layout.less",
          "css/mixins.css": "css/mixins.less",
          "css/style.css": "css/style.less",
          "css/woocommerce.css": "css/woocommerce.less"
        }
      }
    },
    watch: {
      less: {
        files: ['css/*.less'],
        tasks: ['default']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['less', 'watch']);
};