module.exports = {
	content: ["./**/*.php", "./assets/js/*.js"],
	darkMode: "class",
	theme: {
		extend: {
			fontFamily: {
				jost: "Jost, sans-serif",
				mulish: "Mulish, sans-serif",
			},
			backgroundImage: {
				"index-banner":
					"linear-gradient(rgba(0, 0, 0, .8), rgba(0, 0, 0, .8)), url(../img/background.jpg)",
				"details-banner":
					"linear-gradient(rgba(0, 0, 0, .8), rgba(0, 0, 0, .8)), url(../img/background.jpg)",
				"admin-nav":
					"linear-gradient(90deg, rgba(255, 186, 104, 0.05) 0%, rgba(13, 26, 38, 0.05) 117.12%)",
				"search-result":
					"linear-gradient(rgba(0, 0, 0, .8), rgba(0, 0, 0, .8)), url(../img/search-result.jpg)",
				"light-search-result":
					"linear-gradient(rgba(0, 0, 0, .5), rgba(0, 0, 0, .5)), url(../img/search-result.jpg)",
			},
			minHeight: {
				'screen-map': '91vh', // Add the custom min-height value
			  },		
		},
	},
	plugins: [
		require("@tailwindcss/forms")({
			strategy: "class",
		}),
	],
};
