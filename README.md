# FixIt

FixIt is a Laravel-based **Campus Complaint Management System**.  
It helps students to submit complaints, track their progress, and interact with the campus community.

---

## Features
- 📝 Submit and manage complaints  
- 🗂️ Filter complaints by category, status, and priority  
- 💬 Comment and upvote complaints in the community page  
- 📊 Dashboard with quick actions and filters  
- 👤 Student profile with picture upload, course selection, year level, and bio  

---

## Installation

### Prerequisites
- PHP 8.2+  
- Composer  
- Node.js + npm  
- MySQL/MariaDB  

### Steps
1. Clone the repo:
   ```bash
   git clone https://github.com/doopii/FixIt.git
   cd FixIt

2. Install dependencies:

   ```bash
   composer install
   npm install
   ```

3. Copy environment file and generate key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Update `.env` with your database details:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=fixit
   DB_USERNAME=root
   DB_PASSWORD=yourpassword
   ```

5. Run migrations:

   ```bash
   php artisan migrate
   ```

6. Link storage for uploads (profile pictures, etc.):

   ```bash
   php artisan storage:link
   ```

7. Start the dev server:

   ```bash
   php artisan serve
   ```

---

## Screenshots

### Home
<img width="1880" height="885" alt="image" src="https://github.com/user-attachments/assets/6de17af7-4af0-47b6-8d8e-0430c027c491" />

### My Complaints
<img width="1880" height="891" alt="image" src="https://github.com/user-attachments/assets/92a25238-17d5-499e-8df4-4f1db508956a" />

### Complaint Detail
<img width="1893" height="901" alt="image" src="https://github.com/user-attachments/assets/be14ed95-914c-43ea-805c-23b41ce82e48" />

### Profile
<img width="1899" height="903" alt="image" src="https://github.com/user-attachments/assets/ff9a8c69-a35b-4ea2-aaad-b7ea46c809a1" />

### Community
<img width="1887" height="902" alt="image" src="https://github.com/user-attachments/assets/2a843bf0-10e3-41f5-86db-3cde4dd78fed" />

---

