export default defineNuxtPlugin((nuxtApp) => {
  const shouldSuppress = (args) => {
    const text = args
      .map((arg) => {
        if (typeof arg === 'string') return arg
        try {
          return JSON.stringify(arg)
        } catch {
          return String(arg)
        }
      })
      .join(' ')

    return (
      text.includes('<Suspense> is an experimental feature') ||
      text.includes('Unable to add filesystem: <illegal path>')
    )
  }

  const originalConsoleWarn = console.warn
  console.warn = (...args) => {
    if (shouldSuppress(args)) return
    originalConsoleWarn(...args)
  }

  const originalConsoleError = console.error
  console.error = (...args) => {
    if (shouldSuppress(args)) return
    originalConsoleError(...args)
  }

  const previousWarnHandler = nuxtApp.vueApp.config.warnHandler

  nuxtApp.vueApp.config.warnHandler = (msg, instance, trace) => {
    if (shouldSuppress([msg, trace])) {
      return
    }

    if (previousWarnHandler) {
      previousWarnHandler(msg, instance, trace)
      return
    }

    // Fallback to Vue's default warn formatting when no previous handler exists.
    console.warn(`[Vue warn]: ${msg}${trace || ''}`)
  }
})
