# Veterinaria Pro

Sistema web de gestión para clínica veterinaria. Conecta a la base de datos MySQL **Veterinaria_Pro**.

## Requisitos

- PHP 7.4+ (recomendado 8.x)
- MySQL o MariaDB
- Servidor web (XAMPP, WAMP, Laragon, etc.)

## Instalación

1. Copie la carpeta `Veterinaria_pro` en `htdocs` (XAMPP) o el directorio web de su servidor.
2. Inicie Apache y MySQL.
3. Importe la base de datos:
   - Abra phpMyAdmin → Importar → seleccione `database/veterinaria_pro.sql`
   - O en consola: `mysql -u root < database/veterinaria_pro.sql`
4. Si su MySQL tiene contraseña, edite `backend/config.php`.
5. Abra en el navegador: `http://localhost/Veterinaria_pro/` (Apache, puerto 80).

> **Importante:** No abra `index.html` con Live Server ni en el puerto 8080. La app necesita **Apache + PHP** (XAMPP). Si ve “sin conexión”, inicie Apache y MySQL en el panel de XAMPP.

## Acceso demo

| Campo      | Valor                    |
|-----------|---------------------------|
| Correo    | admin@veterinaria.com     |
| Contraseña| admin123                  |

## Módulos

- **Clientes** — dueños de mascotas
- **Mascotas** — pacientes vinculados a clientes
- **Citas** — agenda con estados (pendiente, confirmada, completada, cancelada)
- **Historial** — registros clínicos por mascota

## Estructura

```
Veterinaria_pro/
├── index.html          # Login
├── dashboard.php       # Panel principal
├── backend/            # Lógica y conexión BD
├── views/              # Pantallas protegidas
├── includes/           # Auth, header, footer
├── css/                # Estilos
└── database/           # Script SQL
```
