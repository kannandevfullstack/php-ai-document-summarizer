# 📄 PHP AI Document Summarizer

An AI-powered document processing platform built with **PHP (Laravel)** that allows users to upload documents (PDF, DOCX, TXT), extract text, and generate intelligent summaries, keywords, sentiment analysis, and even LinkedIn-ready posts using AI.

---

## 🚀 Features

- 📂 Upload documents (PDF, DOCX, TXT)
- 🔍 Extract text using PHP libraries
- 🤖 AI-powered:
  - Document summarization
  - Keyword extraction
  - Sentiment analysis
  - LinkedIn post generation

- 📊 Dashboard to view processed documents
- 📥 Export results (PDF / Markdown)
- 🔐 Authentication (Custom Laravel Auth without dependencies)
- 🧾 History tracking of all uploads

---

## 🧠 Use Cases

- Quickly summarize large documents
- Generate professional LinkedIn posts from reports/articles
- Extract insights from research papers
- Content repurposing for blogs/social media

---

## 🛠 Tech Stack

| Layer        | Technology                |
| ------------ | ------------------------- |
| Backend      | PHP (Laravel 10+)         |
| AI/NLP       | OpenAI API                |
| HTTP Client  | Guzzle                    |
| Database     | MySQL                     |
| Frontend     | Blade + Vanilla CSS       |
| File Storage | Local                     |

---

## 🏗 Architecture Overview

User → Upload Document → Laravel Backend
→ Text Extraction → AI Processing → Store Results → Display Dashboard

---

## 📂 Project Structure

```text
php-ai-document-summarizer/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── DocumentController.php
│   │   ├── PasswordResetController.php
│   ├── Services/
│   │   ├── DocumentParserService.php
│   │   ├── AIService.php
│   ├── Models/
│   │   ├── Document.php
├── database/
│   ├── migrations/
├── resources/views/
│   ├── auth/
│   ├── documents/
│   ├── layouts/
├── resources/css/app.css
├── routes/web.php
├── storage/app/uploads/
```

---

## ⚙️ Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/php-ai-document-summarizer.git
cd php-ai-document-summarizer
```

### 2. Install dependencies

```bash
composer install
npm install && npm run build
```

### 3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env`:

```env
DB_CONNECTION=mysql
DB_DATABASE=ai_summarizer
DB_USERNAME=root
DB_PASSWORD=

OPENAI_API_KEY=your_openai_api_key_here
```

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Start server

```bash
php artisan serve
```

---

## 📡 Endpoints / Routes

The application uses standard web routes protected by `auth` middleware (except for authentication endpoints).

- `GET /` -> Redirects to Dashboard
- `GET /documents` -> Dashboard
- `GET /documents/create` -> Upload form
- `POST /documents` -> Process document
- `GET /documents/{id}` -> View results
- `DELETE /documents/{id}` -> Delete document
- `GET /documents/{id}/export/pdf` -> Download as PDF
- `GET /documents/{id}/export/markdown` -> Download as Markdown

Authentication:
- `GET/POST /login` -> Login
- `GET/POST /register` -> Registration
- `POST /logout` -> Logout
- `GET/POST /forgot-password` -> Password Reset
- `GET/POST /reset-password` -> Set New Password

---

## 🤖 AI Features

### 1. Summarization

- Condenses document into short readable format

### 2. Keyword Extraction

- Extracts top relevant keywords

### 3. Sentiment Analysis

- Positive / Negative / Neutral classification

### 4. LinkedIn Post Generator

- Converts document into professional LinkedIn content

---

## 📸 Screenshots

*(Add screenshots here: upload page, dashboard, AI results)*

---

## 📜 License

MIT License
