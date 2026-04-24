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
RUN npm ci
COPY . .
RUN npm run build

# ===========================================
# Stage 4: Production (Render / nginx)
# ===========================================
FROM nginx:alpine AS production

# Copy built assets
COPY --from=build /app/.output/public /usr/share/nginx/html

# Copy nginx config template & entrypoint
COPY nginx.conf /etc/nginx/nginx.conf.template
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]
