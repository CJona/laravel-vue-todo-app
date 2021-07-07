module.exports = {
	root: true,
	
	env: {
		'amd': true,
		'browser': true,
	},
	extends: [
		'eslint:recommended',

		'prettier',
		// Uncomment any of the lines below to choose desired strictness,
		// but leave only one uncommented!
		// See https://eslint.vuejs.org/rules/#available-rules
		'plugin:vue/base',
		// 'plugin:vue/vue3-essential', // Priority A: Essential (Error Prevention)
		// 'plugin:vue/vue3-strongly-recommended', // Priority B: Strongly Recommended (Improving Readability)
		'plugin:vue/vue3-recommended', // Priority C: Recommended (Minimizing Arbitrary Choices and Cognitive Overhead)
	],
	// required to lint *.vue files
	plugins: [
		'vue'
	],
	parserOptions: {
		'impliedStrict': true,
	},
	rules: {
		'semi': ['error', 'always'],
		'indent': ['error', 'tab'],
		'no-tabs': 0,
		'no-throw-literal': 0,
		'one-var': 0,
		'no-undef': 0,

		// allow debugger during development only
		'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off'
	},
	ignorePatterns: [
		'./public/*',
		'./storage/*'
	]
};
