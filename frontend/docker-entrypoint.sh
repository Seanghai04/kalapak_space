#!/bin/sh
set -e

# Use Render's PORT or default to 10000
PORT=${PORT:-10000}

# Backend URL for nginx proxy (set in Render dashboard)
BACKEND_URL=${BACKEND_URL:-http://backend:8000}

# Add https:// scheme if missing
case "$BACKEND_URL" in
  http://*|https://*) ;;
  *) BACKEND_URL="https://${BACKEND_URL}" ;;
esac

# Remove trailing slash
BACKEND_URL=$(echo "$BACKEND_URL" | sed 's|/$||')

# Generate nginx config from template (using sed to avoid conflicts with nginx $variables)
sed -e "s|PORT_PLACEHOLDER|${PORT}|g" \
    -e "s|BACKEND_URL_PLACEHOLDER|${BACKEND_URL}|g" \
    /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

echo "==> Nginx starting on port ${PORT}"
echo "==> API proxy target: ${BACKEND_URL}"

exec "$@"
