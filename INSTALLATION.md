# 📋 GUIDE D'INSTALLATION - PROJET IBAM

## 🔧 Prérequis Système
- PHP 8.2 ou supérieur
- Node.js 18 ou supérieur
- MySQL 8.0 ou supérieur
- Composer
- Git

## 🗄️ Configuration Base de Données

### 1. Créer la base de données
```sql
CREATE DATABASE ibam_reclamations CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Créer un utilisateur dédié (optionnel)
```sql
CREATE USER 'ibam_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON ibam_reclamations.* TO 'ibam_user'@'localhost';
FLUSH PRIVILEGES;
```

## 🏗️ Installation Backend (Laravel)

```bash
# 1. Aller dans le dossier backend
cd backend

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env

# 4. Modifier le .env avec vos paramètres DB
# DB_DATABASE=ibam_reclamations
# DB_USERNAME=root (ou ibam_user)
# DB_PASSWORD=votre_mot_de_passe

# 5. Générer la clé d'application
php artisan key:generate

# 6. Exécuter les migrations
php artisan migrate

# 7. Créer le lien symbolique pour le storage
php artisan storage:link

# 8. Lancer le serveur
php artisan serve
```

## ⚛️ Installation Frontend (React)

```bash
# 1. Aller dans le dossier frontend
cd frontend

# 2. Installer les dépendances
npm install

# 3. Configurer l'environnement
cp .env.example .env

# 4. Lancer le serveur de développement
npm run dev
```

## ✅ Vérification Installation

### Backend
- Accéder à http://localhost:8000
- Vérifier que l'API répond

### Frontend  
- Accéder à http://localhost:5173
- Interface de connexion doit s'afficher

## 🚨 Dépannage

### Erreur de base de données
- Vérifier que MySQL est démarré
- Vérifier les paramètres de connexion dans .env

### Erreur de permissions
```bash
chmod -R 775 storage bootstrap/cache
```