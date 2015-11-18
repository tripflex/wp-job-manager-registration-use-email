'use strict';
module.exports = function ( grunt ) {

	require( 'load-grunt-tasks' )( grunt );

	grunt.initConfig(
		{
			pkg: grunt.file.readJSON( 'package.json' ),

			wp_readme_to_markdown: {
				your_target: {
					files: {
						'readme.md': 'readme.txt'
					},
				},
			}

		}
	);

	grunt.registerTask( 'default', [ 'wp_readme_to_markdown' ] );

};