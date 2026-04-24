interface KalapakSeoInput {
  title?: string
  description?: string
  image?: string
}

export function useKalapakSeo(input: KalapakSeoInput = {}) {
  const config = useRuntimeConfig()

  const title = input.title || "Kalapak Code Team"
  const description =
    input.description || "Modern tech solutions from Cambodia by Kalapak Code Team."
  const image =
    input.image ||
    "https://res.cloudinary.com/kalapak/image/upload/q_auto/f_auto/v1775860922/Logo_kalapak_om1ygl.png"
  const url = config.public.siteUrl

  useSeoMeta({
    title,
    description,
    ogTitle: title,
    ogDescription: description,
    ogType: "website",
    ogImage: image,
    ogUrl: url,
    twitterCard: "summary_large_image",
    twitterTitle: title,
    twitterDescription: description,
    twitterImage: image,
  })
}

