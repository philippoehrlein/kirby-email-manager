import { resolve } from 'node:path'
import { defineConfig } from 'kirbyup/config'

export default defineConfig({
  alias: {
    '@': resolve(import.meta.dirname, 'src'),
    '@components': resolve(import.meta.dirname, 'src/components'),
  },
})
