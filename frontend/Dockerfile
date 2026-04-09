FROM node:20-alpine

# Set working directory
WORKDIR /app

# Copy package files
COPY package.json package-lock.json* ./

# Install dependencies
RUN npm install

# Copy application
COPY . .

# Expose port
EXPOSE 3000

# Start dev server
CMD ["npx", "vite", "--host", "0.0.0.0", "--port", "3000"]
