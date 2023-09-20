module.exports = {
	artifact: {
		files: [
			{
				expand: true,
				cwd: ".",
				src: [
					// Folders to copy
					"classes/**",
					"vendor/**",
					"!vendor/bin",
					"js/dist/*.js",
					"!js/dist/*.nomin.js",
					// Files to copy
					"*.php",
				],
				dest: "artifact",
			},
		],
	},
};
