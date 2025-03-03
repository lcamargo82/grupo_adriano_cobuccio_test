FROM node:22-alpine

WORKDIR /app

COPY app/package*.json ./

RUN npm install

COPY app/. ./

RUN npm run build

EXPOSE 4173

CMD ["npm", "run", "preview"]
