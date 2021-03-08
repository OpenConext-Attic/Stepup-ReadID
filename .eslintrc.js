module.exports = {
  env: {
    browser: true,
    es2021: true,
  },
  extends: [
    "eslint:recommended",
    "plugin:@typescript-eslint/eslint-recommended",
    "plugin:@typescript-eslint/recommended",
    "plugin:@typescript-eslint/recommended-requiring-type-checking",
    "airbnb/base",
    "prettier",
    "plugin:prettier/recommended",
    "plugin:react/recommended",
    "plugin:react-hooks/recommended",
    "plugin:jest/recommended"
  ],
  parser: '@typescript-eslint/parser',
  parserOptions: {
    ecmaFeatures: {
      jsx: true,
    },
    "project": "./tsconfig.json",
    ecmaVersion: 12,
    sourceType: 'module',
  },
  "settings": {
    "react": {
      "version": "detect"
    },
    "import/resolver": "webpack"
  },
  plugins: [
    'prettier',
    "react",
    "react-hooks",
    "eslint-plugin-jest"
  ],
  rules: {
    "no-use-before-define": "off",
    "@typescript-eslint/no-use-before-define": ["error"],
    'react/jsx-filename-extension': ["off", { 'extensions': ['.ts', '.tsx'] }],
    "import/extensions": [
      "error",
      "ignorePackages",
      {
        "ts": "never",
        "tsx": "never"
      }
    ]
  },
};
