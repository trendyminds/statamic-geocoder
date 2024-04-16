import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue2'

export default defineConfig({
	plugins: [
		laravel({
			input: ['resources/js/main.js'],
			publicDirectory: 'resources/dist',
		}),
		vue(),
	],
})
