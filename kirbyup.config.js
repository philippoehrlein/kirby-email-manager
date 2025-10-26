import { resolve } from 'node:path'

export default {
  alias: {
    '@': resolve(__dirname, 'src'),
    '@components': resolve(__dirname, 'src/components'),
  }
}
