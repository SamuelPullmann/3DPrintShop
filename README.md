# 3D Print Shop

Webová aplikácia pre e-shop s 3D tlačenými produktami. Umožňuje prezeranie produktov, pridávanie do košíka, objednávanie a hodnotenie a admin panel umožňuje správu produktov.

## Funkcie

- Prezeranie produktov s filtráciou (typ, kategória, cena)
- Nákupný košík a checkout proces
- Systém hodnotení produktov
- Autentifikácia užívateľov (registrácia, prihlásenie)
- Admin panel pre správu produktov (pridávanie, editovanie, mazanie cez AJAX)
- Responzívny dizajn

## Technológie

- **Backend:** Laravel 11
- **Frontend:** Blade templates, Vanilla JavaScript (AJAX)
- **Databáza:** MySQL
- **Build tool:** Vite
- **Štýly:** Čistý CSS (bez frameworkov)

## Požiadavky

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL
- XAMPP (Apache + MySQL)

## Inštalácia

### 1. Klonuj/skopíruj projekt

```bash
git clone https://github.com/SamuelPullmann/3DPrintShop.git
```

### 2. Nainštaluj PHP závislosti

```bash
composer install
```

### 3. Nainštaluj npm balíčky

```bash
npm install
```

### 4. Vytvor `.env` súbor

```bash
copy .env.example .env
```

### 5. Vygeneruj application key

```bash
php artisan key:generate
```

### 6. Nastav databázové pripojenie

Uprav `.env` súbor:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=3dprintshop
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Vytvor databázu

V phpMyAdmin alebo MySQL konzole vytvor databázu:

```sql
CREATE DATABASE 3dprintshop;
```

### 8. Spusti migrácie

```bash
php artisan migrate
```
## Spustenie projektu

### 1. Spusti MySQL v XAMPP

- Zapni **Apache**
- Zapni **MySQL**

### 2. Zbuilduj frontend assets

```bash
npm run build
```

Alebo pre development mode s hot reload:

```bash
npm run dev
```

### 3. Spusti Laravel server

```bash
php artisan serve
```

### 4. Otvor v prehliadači

Aplikácia beží na: **http://127.0.0.1:8000**

## Autor

Samuel Pullmann.
