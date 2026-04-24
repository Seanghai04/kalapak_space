type ApiRequestOptions<T> = Omit<Parameters<typeof useFetch<T>>[1], "baseURL">

export function useApi<T>(path: string, options?: ApiRequestOptions<T>) {
  const config = useRuntimeConfig()

  return useFetch<T>(path, {
    baseURL: config.public.apiUrl,
    ...options,
  })
}

