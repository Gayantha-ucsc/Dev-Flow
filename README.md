# Dev-Flow

## Local Setup (XAMPP)
1. Clone into `htdocs/dev-flow`
2. Copy `config/database.example.php` → `config/database.php`, fill in credentials
3. Import `database/schema.sql` and `database/seed.sql` into MySQL (via phpMyAdmin or CLI)
4. Ensure `mod_rewrite` is enabled and `AllowOverride All` is set for `htdocs` in `httpd.conf`
5. Visit `http://localhost/devflow/public/`
