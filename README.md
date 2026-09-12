# SACS Nuevo

Base inicial para la reconstruccion progresiva del sistema SACS.

## Objetivo

Este proyecto es independiente del sistema Java legacy. Durante la primera etapa solo verifica el entorno PHP y la conectividad con una copia de la base de datos.

## Requisitos

- XAMPP con Apache, PHP 8.1 o superior y la extension `pdo_mysql` habilitada.
- MySQL o MariaDB con una copia de la base de datos de pruebas.

## Configuracion

1. Copiar `.env.example` como `.env`.
2. Revisar host, puerto, base, usuario y contrasena.
3. No usar credenciales de produccion en desarrollo.
4. Abrir `http://localhost/sacs_nuevo/public/`.
5. Usar el enlace de prueba de conexion.

## Siguiente etapa

Antes de crear operaciones de escritura se debe documentar el esquema legacy, definir permisos de lectura y preparar respaldos y scripts reversibles.
