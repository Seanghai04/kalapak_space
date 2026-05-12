# ===========================================
# Stage 1: Base (shared package files)
# ===========================================
FROM node:20-alpine AS base
WORKDIR /app
COPY package.json package-lock.json* ./

# ===========================================
# Stage 2: Development (docker-compose local)
# ===========================================
FROM base AS dev
RUN npm install
COPY . .
EXPOSE 3000
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "3000"]

# ===========================================
# Stage 3: Build (production bundle)
# ===========================================
FROM base AS build
# Render (and other hosts) pass service env as Docker build args so Vite/Nuxt can embed public config.
ARG NUXT_PUBLIC_API_URL
ENV NUXT_PUBLIC_API_URL=$NUXT_PUBLIC_API_URL
ARG NUXT_PUBLIC_SITE_URL
ENV NUXT_PUBLIC_SITE_URL=$NUXT_PUBLIC_SITE_URL
ARG VITE_APP_NAME
ENV VITE_APP_NAME=$VITE_APP_NAME
ARG VITE_APP_ENV
ENV VITE_APP_ENV=$VITE_APP_ENV
ARG VITE_API_URL
ENV VITE_API_URL=$VITE_API_URL
ARG VITE_BASE_URL
ENV VITE_BASE_URL=$VITE_BASE_URL
ARG VITE_WITH_CREDENTIALS
ENV VITE_WITH_CREDENTIALS=$VITE_WITH_CREDENTIALS
ARG VITE_TURNSTILE_SITE_KEY
ENV VITE_TURNSTILE_SITE_KEY=$VITE_TURNSTILE_SITE_KEY
RUN npm ci
COPY . .
RUN npm run build

# ===========================================
# Stage 4: Production (Render / Nuxt SSR)
# ===========================================
FROM node:20-alpine AS production
WORKDIR /app
ENV NODE_ENV=production

# Copy Nuxt production output (server + public assets)
COPY --from=build /app/.output ./.output

EXPOSE 10000

CMD ["node", ".output/server/index.mjs"]
